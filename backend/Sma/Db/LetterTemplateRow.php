<?php
namespace Sma\Db;

use Sma\Db\Generated\AbstractLetterTemplateRow;
use App\Document\Model\LetterTemplate\LetterTemplateManager as LTM;
use Sma\Bean\LetterBean;
use Sma\Bean\ContactBean;
use Sma\Bean\InvoiceBean;

/**
 * Row model for table letter_template
 *
 * Use this class to complete AbstractLetterTemplateRow
 *
 * @version 1.0
 * @author Guillaume Ponçon - OpenStates Framework PHP Generator
 * @since OSF 2.0
 * @package osf
 * @subpackage generated
 */
class LetterTemplateRow extends AbstractLetterTemplateRow
{
    /**
     * Rendu en allant chercher les informations du bean dans la base
     * @param int $id identifiant de l'enregistrement correspondant au type de données lié au template
     * @param bool $persistant Rendre le rendu persistant (mise en cache)
     * @return LetterBean
     */
    public function render(int $id, bool $persistant = true): LetterBean
    {
        // On va chercher le bean du document ou le destinataire attaché...
        $dataBean  = LTM::getBeanFromDb($this->getDataType(), $id);
        
        // On va chercher le destinataire lié à la cible... 
        $recipient = LTM::getDataTypeBeanRecipient($dataBean);
       
        // On lance le rendu du contenu du document
        $letterBean = LTM::render($this->getBean(), $dataBean, $persistant);
        
        // On attache au document son expéditeur et destinataire afin de compléter le rendu PDF
        $letterBean
            ->setProvider(ContactBean::buildContactBeanFromContactId())
            ->setRecipient($recipient);
        
        // Si le databean est de type facture, on l'attache au document
        if ($dataBean instanceof InvoiceBean) {
            $letterBean->setAttachmentId($dataBean->getId());
        }
        
        // Titre du document = titre du template
        $title = $this->getTitle();
        if ($dataBean instanceof InvoiceBean) {
            $title .= ' - ' . $dataBean->getCode();
        }
        $letterBean->setTitle($title);
        
        return $letterBean;
    }
}