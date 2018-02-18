<?php
namespace Sma\Db\Generated;

use Osf\Db\Row\AbstractRowGateway;

/**
 * Row gateway for document_history
 *
 * WARNING: This class is generated automatically, do not update it manually!
 *          Please use DocumentHistoryRow instead.
 *
 * @version 1.0
 * @author Guillaume Ponçon - OpenStates Framework PHP Generator
 * @since OSF 3.0.0
 * @package osf
 * @subpackage generated
 */
abstract class AbstractDocumentHistoryRow extends AbstractRowGateway
{

    protected $schemaKey = 'common';

    protected $table = 'document_history';

    protected $primaryKeyColumn = [
        'id',
    ];

    final public function getId()
    {
        return $this->get('id');
    }

    /**
     * @return \Sma\Db\DocumentHistoryRow
     */
    final public function setId($value)
    {
        return $this->set('id', $value);
    }

    final public function getIdDocument()
    {
        return $this->get('id_document');
    }

    /**
     * @return \Sma\Db\DocumentHistoryRow
     */
    final public function setIdDocument($value)
    {
        return $this->set('id_document', $value);
    }

    final public function getDump()
    {
        return $this->get('dump');
    }

    /**
     * @return \Sma\Db\DocumentHistoryRow
     */
    final public function setDump($value)
    {
        return $this->set('dump', $value);
    }

    final public function getSource()
    {
        return $this->get('source');
    }

    /**
     * @return \Sma\Db\DocumentHistoryRow
     */
    final public function setSource($value)
    {
        return $this->set('source', $value);
    }

    final public function getHash()
    {
        return $this->get('hash');
    }

    /**
     * @return \Sma\Db\DocumentHistoryRow
     */
    final public function setHash($value)
    {
        return $this->set('hash', $value);
    }

    final public function getDateInsert()
    {
        return $this->get('date_insert');
    }

    /**
     * @return \Sma\Db\DocumentHistoryRow
     */
    final public function setDateInsert($value)
    {
        return $this->set('date_insert', $value);
    }

    final public function getIdAccount()
    {
        return $this->get('id_account');
    }

    /**
     * @return \Sma\Db\DocumentHistoryRow
     */
    final public function setIdAccount($value)
    {
        return $this->set('id_account', $value);
    }

    /**
     * @return \Sma\Db\DocumentRow
     */
    public function getRelatedDocumentRowFromIdDocumentFk()
    {
        return $this->getInternalFkRow($this->getIdDocument(), \Sma\Db\DbContainer::getDocumentTable(), 'id');
    }

    /**
     * @return \Sma\Db\AccountRow
     */
    public function getRelatedAccountRowFromIdAccountFk()
    {
        return $this->getInternalFkRow($this->getIdAccount(), \Sma\Db\DbContainer::getAccountTable(), 'id');
    }
}