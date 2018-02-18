<?php
namespace Sma\Db\Generated;

use Osf\Db\Row\AbstractRowGateway;

/**
 * Row gateway for letter_template
 *
 * WARNING: This class is generated automatically, do not update it manually!
 *          Please use LetterTemplateRow instead.
 *
 * @version 1.0
 * @author Guillaume Ponçon - OpenStates Framework PHP Generator
 * @since OSF 3.0.0
 * @package osf
 * @subpackage generated
 */
abstract class AbstractLetterTemplateRow extends AbstractRowGateway
{

    protected $schemaKey = 'common';

    protected $table = 'letter_template';

    protected $primaryKeyColumn = [
        'id',
    ];

    final public function getId()
    {
        return $this->get('id');
    }

    /**
     * @return \Sma\Db\LetterTemplateRow
     */
    final public function setId($value)
    {
        return $this->set('id', $value);
    }

    final public function getIdAccount()
    {
        return $this->get('id_account');
    }

    /**
     * @return \Sma\Db\LetterTemplateRow
     */
    final public function setIdAccount($value)
    {
        return $this->set('id_account', $value);
    }

    final public function getCategory()
    {
        return $this->get('category');
    }

    /**
     * @return \Sma\Db\LetterTemplateRow
     */
    final public function setCategory($value)
    {
        return $this->set('category', $value);
    }

    final public function getTitle()
    {
        return $this->get('title');
    }

    /**
     * @return \Sma\Db\LetterTemplateRow
     */
    final public function setTitle($value)
    {
        return $this->set('title', $value);
    }

    final public function getDescription()
    {
        return $this->get('description');
    }

    /**
     * @return \Sma\Db\LetterTemplateRow
     */
    final public function setDescription($value)
    {
        return $this->set('description', $value);
    }

    final public function getDataType()
    {
        return $this->get('data_type');
    }

    /**
     * @return \Sma\Db\LetterTemplateRow
     */
    final public function setDataType($value)
    {
        return $this->set('data_type', $value);
    }

    final public function getDataTypeFilters()
    {
        return $this->get('data_type_filters');
    }

    /**
     * @return \Sma\Db\LetterTemplateRow
     */
    final public function setDataTypeFilters($value)
    {
        return $this->set('data_type_filters', $value);
    }

    final public function getTargetType()
    {
        return $this->get('target_type');
    }

    /**
     * @return \Sma\Db\LetterTemplateRow
     */
    final public function setTargetType($value)
    {
        return $this->set('target_type', $value);
    }

    final public function getSearchData()
    {
        return $this->get('search_data');
    }

    /**
     * @return \Sma\Db\LetterTemplateRow
     */
    final public function setSearchData($value)
    {
        return $this->set('search_data', $value);
    }

    final public function getBean()
    {
        return $this->get('bean');
    }

    /**
     * @return \Sma\Db\LetterTemplateRow
     */
    final public function setBean($value)
    {
        return $this->set('bean', $value);
    }

    final public function getDateInsert()
    {
        return $this->get('date_insert');
    }

    /**
     * @return \Sma\Db\LetterTemplateRow
     */
    final public function setDateInsert($value)
    {
        return $this->set('date_insert', $value);
    }

    final public function getDateUpdate()
    {
        return $this->get('date_update');
    }

    /**
     * @return \Sma\Db\LetterTemplateRow
     */
    final public function setDateUpdate($value)
    {
        return $this->set('date_update', $value);
    }

    /**
     * @return \Sma\Db\AccountRow
     */
    public function getRelatedAccountRowFromIdAccountFk()
    {
        return $this->getInternalFkRow($this->getIdAccount(), \Sma\Db\DbContainer::getAccountTable(), 'id');
    }
}