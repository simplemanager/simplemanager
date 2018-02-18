<?php
namespace Sma\Bean;

use Osf\Pdf\Document\Bean\LetterBean as OLB;
use Osf\Exception\DisplayedException;
use Osf\Exception\ArchException;
use Osf\Stream\Text;
use Sma\Bean\ContactBean as CB;
use Sma\Session\Identity as I;
use Sma\Container as C;
use Sma\Mail;
use DateTime;
use DB;

/**
 * Tout type de lettre
 * 
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 2017
 * @package app
 * @subpackage document
 */
class LetterBean extends OLB implements DocumentBeanInterface
{
    const TYPE = 'letter';
    
    use Addon\Id;
    use Addon\Template;
    use Addon\Attachment;
    
    public function __construct($contactId = null, bool $buildContactIfNull = true)
    {
        $contactId = is_numeric($contactId) ? (int) $contactId : ($buildContactIfNull ? I::getIdContact() : null);
        if ($contactId === null && !$buildContactIfNull) {
            $this->setProvider();
        } else if (!$contactId) {
            throw new ArchException('No user to load');
        } else {
            $this->setProvider(CB::buildContactBeanFromContactId($contactId, true));
        }
        $this->setDisplayCreatedBy(I::getParam('document', 'madesma'));
        $this->setConfidential(I::getParam('document', 'confidential'));
    }
    
    /**
     * Définit le destinataire et le titre avant d'appeller parent::populate
     * @param array $data
     * @param bool $noError
     * @return $this
     */
    public function populate(array $data, bool $noError = false)
    {
        if (isset($data['recipient'])) {
            $this->setRecipient(DB::getCompanyTable()->getContactBean((int) $data['recipient']));
            unset($data['recipient']);
        } else if (isset($data['recipient_bean'])) {
            $this->setRecipient($data['recipient_bean']);
            unset($data['recipient_bean']);
        }
        if (!isset($data['title'])) {
            $this->setTitle($data['object']);
            $this->setSubject($data['object']);
        }
        return parent::populate($data, $noError);
    }
    
    /**
     * Description du document pour identification
     * @return string
     */
    public function getDescription()
    {
        return $this->getRecipient()->getComputedTitle();
    }
    
    /**
     * Données à indexer pour la recherche textuelle
     * @return string
     */
    public function getSearchData(): string
    {
        return html_entity_decode(
                $this->getTitle() . ' ' . 
                $this->getSubject() . ' ' . 
                implode(" ", $this->getLibs()) . 
                $this->getBody());
    }
    
    /**
     * Url vers le document
     * @return string
     */
    public function buildUrl(): string
    {
        if (!$this->getId()) {
            throw new ArchException('Id is required to build url');
        }
        return C::getRouter()->buildUri(['id' => $this->getId()], 'document', 'view');
    }
    
    public function getType(): string
    {
        return self::TYPE;
    }
    
    /**
     * Construit un nom de fichier pour ce document
     * @param int $version
     * @param DateTime $date
     * @return string
     */
    public function buildFileName(int $version = null, DateTime $date = null): string
    {
        return  $this->filenameDate($date) . '_'
                . trim(Text::getAlpha($this->getTitle()), '-') . '_'
                . trim(Text::getAlpha($this->getRecipient()->getComputedTitle()), '-') 
                . ($version !== null ? '-v'  . $version : '')
                . '.pdf';
    }
    
    /**
     * Construit un e-mail avec les données du bean
     * @param Mail|null $mail
     * @param bool $throwDisplayedExceptions
     * @return Mail
     * @throws ArchException
     */
    public function buildEmail(?Mail $mail = null, bool $throwDisplayedExceptions = true): Mail
    {
        $mail = $mail ?? new Mail();
        
        // Vérifications
        $throwDisplayedExceptions && $this->checkBeanForEmail();
        
        // Contenu
        $body = $this->getRecipient()->getComputedCivilityWithLastname() . ",\n\n" . 
                $this->getBody(true) . "\n\n" . 
                $this->getProvider()->getComputedTitle();
        
        // Sujet, from, to, body, footer
        $mail->setSubject($this->getSubject() ?: $this->getTitle())
             ->addReplyTo(
                $this->getProvider()->getEmail(), 
                $this->getProvider()->getComputedTitle())
             ->addTo(
                $this->getRecipient()->getEmail(), 
                $this->getRecipient()->getComputedTitle())
             ->addText($body, false)
             ->setFooterUser();
        
        // Pièces jointes
        if ($this->getAttachLetter()) {
            $doc = DB::getDocumentTable()->getDocument($this->getId(), null, true);
            $mail->addAttachment($doc['history']['dump'], Mail::MIMETYPE_PDF, $this->buildFileName());
        }
//        if ($this->getAttachmentId()) {
//            $doc = DB::getDocumentTable()->getDocument($this->getAttachmentId(), null, true);
//            if (!$doc || !isset($doc['history']['source'])) {
//                throw new DisplayedException(__("Le document lié à votre lettre n'existe pas. A-t-il été supprimé ?"));
//            }
//            $bean = $doc['history']['source'];
//            if (!($bean instanceof DocumentBeanInterface)) {
//                throw new ArchException('Unable to attach a document of type [' . Debug::getType($bean) . '].');
//            }
//            $mail->addAttachment($doc['history']['dump'], Mail::MIMETYPE_PDF, $bean->buildFileName());
//        }
        
        return $mail;
    }
    
    protected function checkBeanForEmail()
    {
        if ($this->getStatus() !== self::STATUS_CREATED) {
            throw new DisplayedException(__("Seuls les messages définis comme 'brouillon' peuvent être envoyés. Changez l'état de votre message si vous souhaitez quand même l'envoyer."));
        }
        if (!$this->getRecipient() || !$this->getRecipient()->getEmail()) {
            throw new DisplayedException(__("Votre message ne peut pas être envoyé car le destinataire n'a pas d'e-mail. Ajoutez un e-mail au destinataire et ré-enregistrez votre message pour l'envoyer."));
        }
        
        return $this;
    }
}
