<?php
namespace App\Document\Model\LetterTemplate;

use Osf\Pdf\Document\Bean\BaseDocumentBean as BDB;
use Osf\Stream\TwigLight;
use Osf\Bean\AbstractBean;
use Osf\Container\VendorContainer;
use Osf\Exception\ArchException;
use Sma\Controller\Json as JsonController;
use Sma\Bean\Example\ContactBeanExample;
use Sma\Bean\Example\InvoiceBeanExample;
use Sma\Bean\LetterTemplateBean as LTB;
use Sma\Bean\DocumentBeanInterface;
use Sma\Session\Identity as I;
use Sma\Bean\InvoiceBean;
use Sma\Bean\ContactBean;
use Sma\Bean\LetterBean;
use Sma\Db\InvoiceRow;
use Sma\Cache as SC;
use Sma\Mail;
use Sma\Log;
use App\Common\Container;
use ACL, DB;

/**
 * Gestionnaire de modèles
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 2017
 * @package app
 * @subpackage document
 */
class LetterTemplateManager
{
    const TPL_ENGINE_TWIG = 'twig';
    const TPL_ENGINE_TWIGLIGHT = 'twiglight';
    
    const TPL_ENGINE = self::TPL_ENGINE_TWIG;
    
    /**
     * Rendu de la lettre à partir d'un modèle et d'un bean de données
     * @param LetterBean $templateBean
     * @param AbstractBean $bean
     * @param bool $persistant
     * @return LetterBean
     */
    public static function render(LetterBean $templateBean, AbstractBean $bean, bool $persistant = false): LetterBean
    {
        // Récupération du tableau de valeurs de substitution
        $values = Substitutions::getValues($bean);
        
        // Compilation avec Twig et enregistrement des données 
        // compilées dans une copie du template
        $letterBean = clone $templateBean;
        
        // Récupération des données à compiler
        $libs = $templateBean->getLibs();
        $source = '';
        foreach ($libs as $value) {
            $source .= $value . "<#####>\n";
        }
        $source .= $templateBean->getSubject() . "<#####>\n";
        $source .= $templateBean->getBody();
        
        // Compilation Twig
        switch (self::TPL_ENGINE) {
            case self::TPL_ENGINE_TWIG : 
                $data = VendorContainer::newTwig($source, null, $persistant)->render($values);
                break;
            case self::TPL_ENGINE_TWIGLIGHT :
                $data = TwigLight::quickRender($source, $values);
            default : 
                new ArchException('Unknown template engine [' . self::TPL_ENGINE . ']');
        }
        
        // Répartition des données dans la lettre
        $tab = explode("<#####>\n", $data);
        $renderedLibs = [];
        foreach (array_keys($libs) as $key) {
            $renderedLibs[$key] = array_shift($tab);
        }
        if (count($tab) !== 2) {
            throw new ArchException('Wrong number of elements');
        }
        $letterBean->setLibs($renderedLibs);
        $letterBean->setSubject(array_shift($tab));
        $letterBean->setBody(array_shift($tab));
        
        // Récupération du bean 
        return $letterBean;
    }
    
    /**
     * Envoie un mail au destinataire d'un document pour consultation
     * @param LetterBean $templateBean
     * @param BDB $bean
     * @param JsonController $ctrl
     * @return bool
     * @throws ArchException
     */
    public static function sendDocument(LetterBean $templateBean, BDB $bean, JsonController $ctrl): bool
    {
        // Récupération du row et tests de sécurité
        $templateRow = DB::getLetterTemplateTable()->find($templateBean->getId());
        if ($templateRow->getCategory() === 'mine' && $templateRow->getIdAccount() !== I::getIdAccount()) {
            throw new ArchException("Génération de document non autorisée avec un template privé n'appartenant pas à l'utilisateur courant.");
        }
        if (!($bean instanceof DocumentBeanInterface)) {
            throw new ArchException('Bad document type [' . get_class($bean) . ']');
        }
        if (!$bean->getId()) {
            throw new ArchException('Unable to send a document bean without id.');
        }
        
        // Génération de l'email
        if (in_array($templateRow->getTargetType(), [LTB::TARGET_TYPE_EMAIL, LTB::TARGET_TYPE_BOTH])) {
            $mail = $templateRow->render($bean->getId())->buildEmail();
        } else {
            $mail = $this->buildMailForLetterDocument($bean);
        }
        
        // Envoi de l'email
        $sent = false;
        try {
            $mail->send();
            $sent = true;
        } catch (\Exception $e) {
            Log::error("Erreur d'envoi d'un mail client !", 'MAIL', $e->getMessage());
            $ctrl->alertDanger(__("Envoi impossible"), __("Malheureusement nous n'avons pas pu envoyer votre e-mail. Nous travaillons sur ce problème. Veuillez nous excuser pour la gêne occasionnée."));
        }
        
        // Traitements post-envoi (status, cache, etc.)
        if ($sent) {
            if ($bean->getStatus() === BDB::STATUS_CREATED) {
                $ctrl->alertSuccess(__("Message envoyé"), __("Un e-mail a été envoyé au destinataire et votre document est maintenant défini comme 'Envoyé'."));
                $bean->setStatus(BDB::STATUS_SENT);
                if ($bean instanceof InvoiceBean) {
                    DB::getInvoiceTable()->findSafe($bean->getIdInvoice())
                        ->setBean($bean)
                        ->save();
                    DB::getDocumentTable()->findSafe($bean->getIdDocument())
                        ->setStatus(InvoiceBean::STATUS_SENT)
                        ->save();
                }
                DB::getDocumentTable()->updateStatus($bean->getIdDocument(), BDB::STATUS_SENT, BDB::EVENT_SENDING);
            } else {
                $ctrl->alertSuccess(__("Message envoyé"), __("Un e-mail a été envoyé au destinataire."));
            }
            Container::getCacheSma()->cleanItem(SC::C_DOCUMENT, $bean->getIdDocument());
        }
        
        return $sent;
    }
    
    /**
     * Construit un e-mail type pour inviter le destinataire d'un document PDF à le télécharger
     * @param BDB $bean
     * @return Mail
     */
    protected static function buildMailForLetterDocument(BDB $bean): Mail
    {
        /* @var $docHistoryRow \Sma\Db\DocumentHistoryCurrentRow */
        $docHistoryRow = DB::getDocumentHistoryCurrentTable()->findSafe($bean->getId());
        return (new Mail())
                ->setSubject(sprintf(__("%s | Nouveau document : %s"), $bean->getProvider()->getTitle(), $bean->getSubject()))
                ->addParagraph($bean->getRecipient()->getComputedCivilityWithLastname() . ',')
                ->addParagraph(__("Un nouveau document est en attente de lecture :"))
                ->addLinkBullet($bean->getSubject(), $docHistoryRow->buildUrlWithHash($bean->getRecipientCompanyHash()))
                ->addParagraph(__("Dans votre espace invité, vous pouvez consulter l'ensemble de vos documents, effectuer des actions et mettre à jour les informations vous concernants :"))
                ->addLinkBullet(__("Mon espace invité"), DB::getCompanyTable()->findSafe($bean->getRecipient()->getIdCompany())->buildLoginUrl());
    }
    
    /**
     * Construit un bean Contact ou Invoice correspondant au type de données demandé
     * @param bool $withExample
     * @return \Osf\Bean\AbstractBean
     * @throws ArchException
     */
    public static function buildDataTypeBean(string $dataType, bool $withExample = false)
    {
        switch ($dataType) {
            case 'recipient' : 
                $bean = $withExample ? new ContactBeanExample(random_int(1, 2)) : new ContactBean();
                break;
            case 'invoices' :
                $bean = $withExample ? new InvoiceBeanExample(random_int(1, 3)) : new InvoiceBean();
                break;
            case InvoiceBean::TYPE_INVOICE : 
                $bean = $withExample ? new InvoiceBeanExample(1) : new InvoiceBean();
                break;
            case InvoiceBean::TYPE_ORDER : 
                $bean = $withExample ? new InvoiceBeanExample(2) : new InvoiceBean();
                break;
            case InvoiceBean::TYPE_QUOTE : 
                $bean = $withExample ? new InvoiceBeanExample(3) : new InvoiceBean();
                break;
            default : 
                throw new ArchException('Unknown datatype [' . $dataType . '], unable to build data bean');
        }
        return $bean;
    }
    
    /**
     * Va chercher le bean correspondant au type donné et l'id
     * @param string $dataType
     * @param int $id
     * @return AbstractBean
     * @throws ArchException
     */
    public static function getBeanFromDb(string $dataType, int $id): AbstractBean
    {
        switch ($dataType) {
            case 'recipient' : 
                $bean = DB::getCompanyTable()->getContactBean($id);
                if (!($bean instanceof ContactBean)) {
                    throw new ArchException('Contact bean not found or incorrect');
                }
                return $bean;
            case 'invoices' : 
            case InvoiceBean::TYPE_INVOICE :
            case InvoiceBean::TYPE_ORDER :
            case InvoiceBean::TYPE_QUOTE :
                $result = DB::getInvoiceTable()->select([
                    'id_account' => I::getIdAccount(), 
                    'id_document' => $id
                ]);
                if ($result->count() !== 1) {
                    throw new ArchException('Invoice attached to document not found. [' . $result->count() . '] invoice(s) found.');
                }
                $row = $result->current();
                if (!($row instanceof InvoiceRow)) {
                    throw new ArchException('Invoice row not found or incorrect.');
                }
                return $row->getBean();
        }
    }
    
    /**
     * Extrait le destinataire depuis un bean contenant le jeu de données
     * @param AbstractBean $bean
     * @return ContactBean
     * @throws ArchException
     */
    public static function getDataTypeBeanRecipient(AbstractBean $bean)
    {
        switch (true) {
            case $bean instanceof ContactBean : 
                return $bean;
            case $bean instanceof InvoiceBean : 
                return $bean->getRecipient();
            default : 
                throw new ArchException('This is not a datatype bean');
        }
    }
    
    /**
     * Est-ce que la gestion des modèles est active dans la conf de l'utilisateur courant ?
     * @return bool
     */
    public static function isActive()
    {
        static $active = null;
        
        if ($active === null) {
            $active = (bool) I::getParam('features', 'document') && I::getParam('features', 'tpl');
        }
        return $active;
    }
    
    /**
     * Liste des données liées (pour liste déroulante)
     * @return array
     */
    public static function getDataTypeOptions()
    {
        return [
            '' => '-- CIBLE --',
            'recipient' => __("Contact"),
            'invoices' => __("Tout document"),
            'invoice' => __("Facture"),
            'order' => __("Commande"),
            'quote' => __("Devis"),
        ];
    }
    
    /**
     * Liste des filtres de données liées (pour liste déroulante)
     * @return array
     */
    public static function getDataFiltersOptions()
    {
        return [
            '' => '-- FILTRES --',
            'status_created' => __("brouillon"),
            'status_sent' => __("envoyé"),
            'status_read' => __("lu"),
            'status_processed' => __("résolu (signé, payé)"),
            'status_canceled' => __("annulé"),
            'overdue' => __("en retard"),
            'on_time' => __("à l'heure"),
        ];
    }
    
    /**
     * Optimisé pour quelle type de cible ? (pour liste déroulante)
     * @return array
     */
    public static function getTargetTypeOptions()
    {
        return [
            '' => '-- OPTIMISE POUR --',
            'email' => __("Créer des e-mails"),
            'letter' => __("Créer des courriers (pdf)"),
            'both' => __("Créer des emails et des courriers"),
        ];
    }
    
    /**
     * Icones des données liées
     * @return array
     */
    public static function getDataTypeIcons()
    {
        return [
            'recipient' => 'user',
            'invoices'  => 'file',
            'invoice'   => 'file-text',
            'order'     => 'file-text-o',
            'quote'     => 'file-o',
        ];
    }
    
    /**
     * Options des catégories communes (draft, mine, common)
     * @param bool $withTitle
     * @return array
     */
    public static function getCategoryLabels(bool $withTitle = false)
    {
        $categories = $withTitle ? ['' => '-- TYPE --'] : [];
        $categories['draft' ] = __("Brouillon");
        $categories['mine'  ] = __("Mes modèles");
        $categories['common'] = __("Modèles généraux");
        return $categories;
    }
    
    /**
     * Options communes + complémentaires, filtrées pour l'utilisateur courant
     * @param bool $withTitle
     * @return array
     */
    public static function getCategoryOptions(bool $withTitle = true)
    {
        if (ACL::isAdmin()) {
            $categories = DB::getLetterTemplateTable()->getCategories($withTitle);
        } else {
            $categories = self::getCategoryLabels($withTitle);
            unset($categories['draft']);
            // @task [TPL] Ajouter la ou les catégories spécifiques à l'utilisateur courant
        }
        return $categories;
    }
    
    /**
     * Sélection de modèles pour liste déroulante
     * @param string|null $dataType
     * @param array $dataTypeFilters
     * @param string|null $targetType
     * @param string|null $category
     * @return array
     * @throws ArchException
     */
    public static function getTemplates(?string $dataType = null, ?array $dataTypeFilters = null, ?string $targetType = null, ?string $category = null, ?string $label = null): array
    {
        $sql = 'SELECT id, category, title FROM letter_template WHERE 1 ';
        $params = [];
        
        if ($category === 'mine') {
            $sql .= 'AND category=? AND id_account=? ';
            $params[] = $category;
            $params[] = I::getIdAccount();
        } else if ($category === 'common') {
            $sql .= 'AND category=? ';
            $params[] = $category;
        } else if ($category === null) {
            $sql .= 'AND ((category=\'mine\' AND id_account=?) OR category=\'common\') ';
            $params[] = I::getIdAccount();
        } else {
            throw new ArchException('Bad category [' . $category . ']');
        }
        
        if ($dataType !== null) {
            if (in_array($dataType, ['invoice', 'order', 'quote'])) {
                $sql .= 'AND (data_type=\'invoices\' OR data_type=?) ';
            } else {
                $sql .= 'AND data_type=? ';
            }
            $params[] = $dataType;
        }
        
        if (is_array($dataTypeFilters) && $dataTypeFilters) {
            $sql .= 'AND (';
            $sets = [];
            foreach ($dataTypeFilters as $filter) {
                $sets[] = 'FIND_IN_SET(?, data_type_filters)';
                $params[] = $filter;
            }
            $sql .= implode(' OR ', $sets) . ') ';
        }
        
        if ($targetType !== null) {
                $sql .= 'AND target_type=? ';
            $params = $targetType;
        }
        
        $sql .= 'ORDER BY category, title';
        
        $rows = DB::getLetterTemplateTable()->prepare($sql)->execute($params);
        
        $retVal = $label ? ['' => $label] : [];
        foreach ($rows as $row) {
            $retVal[(int) $row['id']] = trim($row['title'] . ' ' . ($row['category'] === 'common' ? '' : __("(privé)")));
        }
        return $retVal;
    }
    
    /**
     * Va cherche un template si l'utilisateur courant y a access
     * @param int $id
     * @return LetterBean
     */
    public static function getTemplateBeanFromIdSafe(int $id): LetterBean
    {
        $sql = 'SELECT bean FROM letter_template '
                . 'WHERE id=? '
                . 'AND ((category=\'mine\' AND id_account=?) OR category=\'common\')';
        $row = DB::getLetterTemplateTable()->prepare($sql)->execute([$id, I::getIdAccount()])->current();
        if (!is_array($row) || !isset($row['bean']) || !$row['bean']) {
            Log::error('Tentative de récupération de template non autorisée', 'DB', $row);
            throw new ArchException('Letter template bean not found');
        }
        $bean = unserialize($row['bean']);
        if (!($bean instanceof LetterBean)) {
            throw new ArchException('Bad lettre template type [' . get_class($bean) . ']');
        }
        $bean->setId($id);
        return $bean;
    }
}
