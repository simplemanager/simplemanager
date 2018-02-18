<?php

use Sma\Db\DbContainer;
use Osf\Container\Zend;
use Osf\Container\AbstractStaticContainer;

/**
 * Database models quick access
 *
 * This class is generated, do not edit it
 *
 * @version 1.0
 * @author Guillaume Ponçon - OpenStates Framework PHP Generator
 * @since OSF 3.0.0
 * @package osf
 * @subpackage generated
 */
abstract class DB extends AbstractStaticContainer
{

    /**
     * Build table from its name
     * @param string $tableName
     * @return \Osf\Db\Table\AbstractTableGateway
     */
    public static function getTable(string $tableName)
    {
        return DbContainer::getTable($tableName);
    }

    /**
     * @return \Sma\Db\AccountTable
     */
    public static function getAccountTable()
    {
        return DbContainer::getAccountTable();
    }

    /**
     * @return \Sma\Db\AccountRow
     */
    public static function buildAccountRow(array $rowData = null, $rowExistsInDatabase = false)
    {
        return DbContainer::buildAccountRow($rowData, $rowExistsInDatabase);
    }

    /**
     * @return \Sma\Db\CampaignTable
     */
    public static function getCampaignTable()
    {
        return DbContainer::getCampaignTable();
    }

    /**
     * @return \Sma\Db\CampaignRow
     */
    public static function buildCampaignRow(array $rowData = null, $rowExistsInDatabase = false)
    {
        return DbContainer::buildCampaignRow($rowData, $rowExistsInDatabase);
    }

    /**
     * @return \Sma\Db\FormulaTable
     */
    public static function getFormulaTable()
    {
        return DbContainer::getFormulaTable();
    }

    /**
     * @return \Sma\Db\FormulaRow
     */
    public static function buildFormulaRow(array $rowData = null, $rowExistsInDatabase = false)
    {
        return DbContainer::buildFormulaRow($rowData, $rowExistsInDatabase);
    }

    /**
     * @return \Sma\Db\PaymentTable
     */
    public static function getPaymentTable()
    {
        return DbContainer::getPaymentTable();
    }

    /**
     * @return \Sma\Db\PaymentRow
     */
    public static function buildPaymentRow(array $rowData = null, $rowExistsInDatabase = false)
    {
        return DbContainer::buildPaymentRow($rowData, $rowExistsInDatabase);
    }

    /**
     * @return \Sma\Db\AddressTable
     */
    public static function getAddressTable()
    {
        return DbContainer::getAddressTable();
    }

    /**
     * @return \Sma\Db\AddressRow
     */
    public static function buildAddressRow(array $rowData = null, $rowExistsInDatabase = false)
    {
        return DbContainer::buildAddressRow($rowData, $rowExistsInDatabase);
    }

    /**
     * @return \Sma\Db\BasketTable
     */
    public static function getBasketTable()
    {
        return DbContainer::getBasketTable();
    }

    /**
     * @return \Sma\Db\BasketRow
     */
    public static function buildBasketRow(array $rowData = null, $rowExistsInDatabase = false)
    {
        return DbContainer::buildBasketRow($rowData, $rowExistsInDatabase);
    }

    /**
     * @return \Sma\Db\CompanyTable
     */
    public static function getCompanyTable()
    {
        return DbContainer::getCompanyTable();
    }

    /**
     * @return \Sma\Db\CompanyRow
     */
    public static function buildCompanyRow(array $rowData = null, $rowExistsInDatabase = false)
    {
        return DbContainer::buildCompanyRow($rowData, $rowExistsInDatabase);
    }

    /**
     * @return \Sma\Db\ContactTable
     */
    public static function getContactTable()
    {
        return DbContainer::getContactTable();
    }

    /**
     * @return \Sma\Db\ContactRow
     */
    public static function buildContactRow(array $rowData = null, $rowExistsInDatabase = false)
    {
        return DbContainer::buildContactRow($rowData, $rowExistsInDatabase);
    }

    /**
     * @return \Sma\Db\DocumentTable
     */
    public static function getDocumentTable()
    {
        return DbContainer::getDocumentTable();
    }

    /**
     * @return \Sma\Db\DocumentRow
     */
    public static function buildDocumentRow(array $rowData = null, $rowExistsInDatabase = false)
    {
        return DbContainer::buildDocumentRow($rowData, $rowExistsInDatabase);
    }

    /**
     * @return \Sma\Db\DocumentEventTable
     */
    public static function getDocumentEventTable()
    {
        return DbContainer::getDocumentEventTable();
    }

    /**
     * @return \Sma\Db\DocumentEventRow
     */
    public static function buildDocumentEventRow(array $rowData = null, $rowExistsInDatabase = false)
    {
        return DbContainer::buildDocumentEventRow($rowData, $rowExistsInDatabase);
    }

    /**
     * @return \Sma\Db\DocumentHistoryTable
     */
    public static function getDocumentHistoryTable()
    {
        return DbContainer::getDocumentHistoryTable();
    }

    /**
     * @return \Sma\Db\DocumentHistoryRow
     */
    public static function buildDocumentHistoryRow(array $rowData = null, $rowExistsInDatabase = false)
    {
        return DbContainer::buildDocumentHistoryRow($rowData, $rowExistsInDatabase);
    }

    /**
     * @return \Sma\Db\EventTable
     */
    public static function getEventTable()
    {
        return DbContainer::getEventTable();
    }

    /**
     * @return \Sma\Db\EventRow
     */
    public static function buildEventRow(array $rowData = null, $rowExistsInDatabase = false)
    {
        return DbContainer::buildEventRow($rowData, $rowExistsInDatabase);
    }

    /**
     * @return \Sma\Db\FormTable
     */
    public static function getFormTable()
    {
        return DbContainer::getFormTable();
    }

    /**
     * @return \Sma\Db\FormRow
     */
    public static function buildFormRow(array $rowData = null, $rowExistsInDatabase = false)
    {
        return DbContainer::buildFormRow($rowData, $rowExistsInDatabase);
    }

    /**
     * @return \Sma\Db\FormStatsTable
     */
    public static function getFormStatsTable()
    {
        return DbContainer::getFormStatsTable();
    }

    /**
     * @return \Sma\Db\FormStatsRow
     */
    public static function buildFormStatsRow(array $rowData = null, $rowExistsInDatabase = false)
    {
        return DbContainer::buildFormStatsRow($rowData, $rowExistsInDatabase);
    }

    /**
     * @return \Sma\Db\ImageTable
     */
    public static function getImageTable()
    {
        return DbContainer::getImageTable();
    }

    /**
     * @return \Sma\Db\ImageRow
     */
    public static function buildImageRow(array $rowData = null, $rowExistsInDatabase = false)
    {
        return DbContainer::buildImageRow($rowData, $rowExistsInDatabase);
    }

    /**
     * @return \Sma\Db\InvoiceTable
     */
    public static function getInvoiceTable()
    {
        return DbContainer::getInvoiceTable();
    }

    /**
     * @return \Sma\Db\InvoiceRow
     */
    public static function buildInvoiceRow(array $rowData = null, $rowExistsInDatabase = false)
    {
        return DbContainer::buildInvoiceRow($rowData, $rowExistsInDatabase);
    }

    /**
     * @return \Sma\Db\LetterTemplateTable
     */
    public static function getLetterTemplateTable()
    {
        return DbContainer::getLetterTemplateTable();
    }

    /**
     * @return \Sma\Db\LetterTemplateRow
     */
    public static function buildLetterTemplateRow(array $rowData = null, $rowExistsInDatabase = false)
    {
        return DbContainer::buildLetterTemplateRow($rowData, $rowExistsInDatabase);
    }

    /**
     * @return \Sma\Db\LogTable
     */
    public static function getLogTable()
    {
        return DbContainer::getLogTable();
    }

    /**
     * @return \Sma\Db\LogRow
     */
    public static function buildLogRow(array $rowData = null, $rowExistsInDatabase = false)
    {
        return DbContainer::buildLogRow($rowData, $rowExistsInDatabase);
    }

    /**
     * @return \Sma\Db\NotificationTable
     */
    public static function getNotificationTable()
    {
        return DbContainer::getNotificationTable();
    }

    /**
     * @return \Sma\Db\NotificationRow
     */
    public static function buildNotificationRow(array $rowData = null, $rowExistsInDatabase = false)
    {
        return DbContainer::buildNotificationRow($rowData, $rowExistsInDatabase);
    }

    /**
     * @return \Sma\Db\ProductTable
     */
    public static function getProductTable()
    {
        return DbContainer::getProductTable();
    }

    /**
     * @return \Sma\Db\ProductRow
     */
    public static function buildProductRow(array $rowData = null, $rowExistsInDatabase = false)
    {
        return DbContainer::buildProductRow($rowData, $rowExistsInDatabase);
    }

    /**
     * @return \Sma\Db\SearchTable
     */
    public static function getSearchTable()
    {
        return DbContainer::getSearchTable();
    }

    /**
     * @return \Sma\Db\SearchRow
     */
    public static function buildSearchRow(array $rowData = null, $rowExistsInDatabase = false)
    {
        return DbContainer::buildSearchRow($rowData, $rowExistsInDatabase);
    }

    /**
     * @return \Sma\Db\SearchTagTable
     */
    public static function getSearchTagTable()
    {
        return DbContainer::getSearchTagTable();
    }

    /**
     * @return \Sma\Db\SearchTagRow
     */
    public static function buildSearchTagRow(array $rowData = null, $rowExistsInDatabase = false)
    {
        return DbContainer::buildSearchTagRow($rowData, $rowExistsInDatabase);
    }

    /**
     * @return \Sma\Db\SequenceTable
     */
    public static function getSequenceTable()
    {
        return DbContainer::getSequenceTable();
    }

    /**
     * @return \Sma\Db\SequenceRow
     */
    public static function buildSequenceRow(array $rowData = null, $rowExistsInDatabase = false)
    {
        return DbContainer::buildSequenceRow($rowData, $rowExistsInDatabase);
    }

    /**
     * @return \Sma\Db\TicketTable
     */
    public static function getTicketTable()
    {
        return DbContainer::getTicketTable();
    }

    /**
     * @return \Sma\Db\TicketRow
     */
    public static function buildTicketRow(array $rowData = null, $rowExistsInDatabase = false)
    {
        return DbContainer::buildTicketRow($rowData, $rowExistsInDatabase);
    }

    /**
     * @return \Sma\Db\TicketLogTable
     */
    public static function getTicketLogTable()
    {
        return DbContainer::getTicketLogTable();
    }

    /**
     * @return \Sma\Db\TicketLogRow
     */
    public static function buildTicketLogRow(array $rowData = null, $rowExistsInDatabase = false)
    {
        return DbContainer::buildTicketLogRow($rowData, $rowExistsInDatabase);
    }

    /**
     * @return \Sma\Db\TicketPollTable
     */
    public static function getTicketPollTable()
    {
        return DbContainer::getTicketPollTable();
    }

    /**
     * @return \Sma\Db\TicketPollRow
     */
    public static function buildTicketPollRow(array $rowData = null, $rowExistsInDatabase = false)
    {
        return DbContainer::buildTicketPollRow($rowData, $rowExistsInDatabase);
    }

    /**
     * @return \Sma\Db\AddressContactTable
     */
    public static function getAddressContactTable()
    {
        return DbContainer::getAddressContactTable();
    }

    /**
     * @return \Sma\Db\AddressContactRow
     */
    public static function buildAddressContactRow(array $rowData = null, $rowExistsInDatabase = false)
    {
        return DbContainer::buildAddressContactRow($rowData, $rowExistsInDatabase);
    }

    /**
     * @return \Sma\Db\DocumentHistoryCurrentTable
     */
    public static function getDocumentHistoryCurrentTable()
    {
        return DbContainer::getDocumentHistoryCurrentTable();
    }

    /**
     * @return \Sma\Db\DocumentHistoryCurrentRow
     */
    public static function buildDocumentHistoryCurrentRow(array $rowData = null, $rowExistsInDatabase = false)
    {
        return DbContainer::buildDocumentHistoryCurrentRow($rowData, $rowExistsInDatabase);
    }

    /**
     * @return \Sma\Db\InvoiceDocumentTable
     */
    public static function getInvoiceDocumentTable()
    {
        return DbContainer::getInvoiceDocumentTable();
    }

    /**
     * @return \Sma\Db\InvoiceDocumentRow
     */
    public static function buildInvoiceDocumentRow(array $rowData = null, $rowExistsInDatabase = false)
    {
        return DbContainer::buildInvoiceDocumentRow($rowData, $rowExistsInDatabase);
    }

    /**
     * @return \Sma\Db\TicketWithPollsTable
     */
    public static function getTicketWithPollsTable()
    {
        return DbContainer::getTicketWithPollsTable();
    }

    /**
     * @return \Sma\Db\TicketWithPollsRow
     */
    public static function buildTicketWithPollsRow(array $rowData = null, $rowExistsInDatabase = false)
    {
        return DbContainer::buildTicketWithPollsRow($rowData, $rowExistsInDatabase);
    }

    /**
     * @param string $schema
     * @return \Zend\Db\Adapter\Adapter
     */
    public static function getDbAdapter($schema = null)
    {
        return Zend::getDbAdapter($schema);
    }

    /**
     * @param string $dbKey
     * @return \Zend\Db\Adapter\Adapter
     */
    public static function getDbAdapterFromKey($dbKey = null)
    {
        return Zend::getDbAdapterFromKey($dbKey);
    }

}