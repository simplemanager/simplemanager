<?php
namespace Sma\Db;

use App\Recipient\Model\RecipientDbManager as RDM;
use Sma\Bean\BeanCollection;
use Sma\Db\Generated\AbstractContactTable;
use Sma\Bean\ContactBean;

/**
 * Table model for table contact
 *
 * Use this class to complete AbstractContactTable
 *
 * @version 1.0
 * @author Guillaume Ponçon - OpenStates Framework PHP Generator
 * @since OSF 2.0
 * @package osf
 * @subpackage generated
 */
class ContactTable extends AbstractContactTable implements DbRegistry\Exchangeable
{
    use Addon\SafeActions;
    
    /**
     * ContactBean depuis la base
     * @param int $idContact
     * @param bool $safe
     * @return ContactBean|null
     */
    public function getBean(int $idContact, bool $safe = true): ?ContactBean
    {
        $row = $safe ? $this->findSafe($idContact) : $this->find($idContact);
        $bean = $row ? $row->getBean() : null;
        return $bean;
    }
    
    /**
     * @return \Zend\Db\Adapter\Driver\ResultInterface
     */
    public static function getBeans(array $settings = []): BeanCollection
    {
        return new BeanCollection(RDM::getContactsForTable($settings, ['contact.bean as bean']));
    }
}
