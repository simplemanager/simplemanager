<?php
namespace Sma\Db\Generated;

use Osf\Db\AbstractDbContainerProxy;

/**
 * Table & View models container (NOT WRITABLE)
 *
 * @version 1.0
 * @author Guillaume Ponçon - OpenStates Framework PHP Generator
 * @since OSF 3.0.0
 * @package osf
 * @subpackage generated
 */
abstract class AbstractDbContainer extends AbstractDbContainerProxy
{

    protected static $instances = [
        
    ];

    protected static $mockNamespace = 'real';

    /**
     * @return \Sma\Db\AccountTable
     */
    public static function getAccountTable()
    {
        return self::buildTableObject('\Sma\Db\AccountTable');
    }

    /**
     * @return \Sma\Db\AccountRow
     */
    public static function buildAccountRow(array $rowData = null, $rowExistsInDatabase = false)
    {
        return self::buildRowObject('\Sma\Db\AccountRow', $rowData, $rowExistsInDatabase);
    }

    /**
     * @return \Sma\Db\CampaignTable
     */
    public static function getCampaignTable()
    {
        return self::buildTableObject('\Sma\Db\CampaignTable');
    }

    /**
     * @return \Sma\Db\CampaignRow
     */
    public static function buildCampaignRow(array $rowData = null, $rowExistsInDatabase = false)
    {
        return self::buildRowObject('\Sma\Db\CampaignRow', $rowData, $rowExistsInDatabase);
    }

    /**
     * @return \Sma\Db\FormulaTable
     */
    public static function getFormulaTable()
    {
        return self::buildTableObject('\Sma\Db\FormulaTable');
    }

    /**
     * @return \Sma\Db\FormulaRow
     */
    public static function buildFormulaRow(array $rowData = null, $rowExistsInDatabase = false)
    {
        return self::buildRowObject('\Sma\Db\FormulaRow', $rowData, $rowExistsInDatabase);
    }

    /**
     * @return \Sma\Db\PaymentTable
     */
    public static function getPaymentTable()
    {
        return self::buildTableObject('\Sma\Db\PaymentTable');
    }

    /**
     * @return \Sma\Db\PaymentRow
     */
    public static function buildPaymentRow(array $rowData = null, $rowExistsInDatabase = false)
    {
        return self::buildRowObject('\Sma\Db\PaymentRow', $rowData, $rowExistsInDatabase);
    }

    /**
     * @return \Sma\Db\AddressTable
     */
    public static function getAddressTable()
    {
        return self::buildTableObject('\Sma\Db\AddressTable');
    }

    /**
     * @return \Sma\Db\AddressRow
     */
    public static function buildAddressRow(array $rowData = null, $rowExistsInDatabase = false)
    {
        return self::buildRowObject('\Sma\Db\AddressRow', $rowData, $rowExistsInDatabase);
    }

    /**
     * @return \Sma\Db\BasketTable
     */
    public static function getBasketTable()
    {
        return self::buildTableObject('\Sma\Db\BasketTable');
    }

    /**
     * @return \Sma\Db\BasketRow
     */
    public static function buildBasketRow(array $rowData = null, $rowExistsInDatabase = false)
    {
        return self::buildRowObject('\Sma\Db\BasketRow', $rowData, $rowExistsInDatabase);
    }

    /**
     * @return \Sma\Db\CompanyTable
     */
    public static function getCompanyTable()
    {
        return self::buildTableObject('\Sma\Db\CompanyTable');
    }

    /**
     * @return \Sma\Db\CompanyRow
     */
    public static function buildCompanyRow(array $rowData = null, $rowExistsInDatabase = false)
    {
        return self::buildRowObject('\Sma\Db\CompanyRow', $rowData, $rowExistsInDatabase);
    }

    /**
     * @return \Sma\Db\ContactTable
     */
    public static function getContactTable()
    {
        return self::buildTableObject('\Sma\Db\ContactTable');
    }

    /**
     * @return \Sma\Db\ContactRow
     */
    public static function buildContactRow(array $rowData = null, $rowExistsInDatabase = false)
    {
        return self::buildRowObject('\Sma\Db\ContactRow', $rowData, $rowExistsInDatabase);
    }

    /**
     * @return \Sma\Db\DocumentTable
     */
    public static function getDocumentTable()
    {
        return self::buildTableObject('\Sma\Db\DocumentTable');
    }

    /**
     * @return \Sma\Db\DocumentRow
     */
    public static function buildDocumentRow(array $rowData = null, $rowExistsInDatabase = false)
    {
        return self::buildRowObject('\Sma\Db\DocumentRow', $rowData, $rowExistsInDatabase);
    }

    /**
     * @return \Sma\Db\DocumentEventTable
     */
    public static function getDocumentEventTable()
    {
        return self::buildTableObject('\Sma\Db\DocumentEventTable');
    }

    /**
     * @return \Sma\Db\DocumentEventRow
     */
    public static function buildDocumentEventRow(array $rowData = null, $rowExistsInDatabase = false)
    {
        return self::buildRowObject('\Sma\Db\DocumentEventRow', $rowData, $rowExistsInDatabase);
    }

    /**
     * @return \Sma\Db\DocumentHistoryTable
     */
    public static function getDocumentHistoryTable()
    {
        return self::buildTableObject('\Sma\Db\DocumentHistoryTable');
    }

    /**
     * @return \Sma\Db\DocumentHistoryRow
     */
    public static function buildDocumentHistoryRow(array $rowData = null, $rowExistsInDatabase = false)
    {
        return self::buildRowObject('\Sma\Db\DocumentHistoryRow', $rowData, $rowExistsInDatabase);
    }

    /**
     * @return \Sma\Db\EventTable
     */
    public static function getEventTable()
    {
        return self::buildTableObject('\Sma\Db\EventTable');
    }

    /**
     * @return \Sma\Db\EventRow
     */
    public static function buildEventRow(array $rowData = null, $rowExistsInDatabase = false)
    {
        return self::buildRowObject('\Sma\Db\EventRow', $rowData, $rowExistsInDatabase);
    }

    /**
     * @return \Sma\Db\FormTable
     */
    public static function getFormTable()
    {
        return self::buildTableObject('\Sma\Db\FormTable');
    }

    /**
     * @return \Sma\Db\FormRow
     */
    public static function buildFormRow(array $rowData = null, $rowExistsInDatabase = false)
    {
        return self::buildRowObject('\Sma\Db\FormRow', $rowData, $rowExistsInDatabase);
    }

    /**
     * @return \Sma\Db\FormStatsTable
     */
    public static function getFormStatsTable()
    {
        return self::buildTableObject('\Sma\Db\FormStatsTable');
    }

    /**
     * @return \Sma\Db\FormStatsRow
     */
    public static function buildFormStatsRow(array $rowData = null, $rowExistsInDatabase = false)
    {
        return self::buildRowObject('\Sma\Db\FormStatsRow', $rowData, $rowExistsInDatabase);
    }

    /**
     * @return \Sma\Db\ImageTable
     */
    public static function getImageTable()
    {
        return self::buildTableObject('\Sma\Db\ImageTable');
    }

    /**
     * @return \Sma\Db\ImageRow
     */
    public static function buildImageRow(array $rowData = null, $rowExistsInDatabase = false)
    {
        return self::buildRowObject('\Sma\Db\ImageRow', $rowData, $rowExistsInDatabase);
    }

    /**
     * @return \Sma\Db\InvoiceTable
     */
    public static function getInvoiceTable()
    {
        return self::buildTableObject('\Sma\Db\InvoiceTable');
    }

    /**
     * @return \Sma\Db\InvoiceRow
     */
    public static function buildInvoiceRow(array $rowData = null, $rowExistsInDatabase = false)
    {
        return self::buildRowObject('\Sma\Db\InvoiceRow', $rowData, $rowExistsInDatabase);
    }

    /**
     * @return \Sma\Db\LetterTemplateTable
     */
    public static function getLetterTemplateTable()
    {
        return self::buildTableObject('\Sma\Db\LetterTemplateTable');
    }

    /**
     * @return \Sma\Db\LetterTemplateRow
     */
    public static function buildLetterTemplateRow(array $rowData = null, $rowExistsInDatabase = false)
    {
        return self::buildRowObject('\Sma\Db\LetterTemplateRow', $rowData, $rowExistsInDatabase);
    }

    /**
     * @return \Sma\Db\LogTable
     */
    public static function getLogTable()
    {
        return self::buildTableObject('\Sma\Db\LogTable');
    }

    /**
     * @return \Sma\Db\LogRow
     */
    public static function buildLogRow(array $rowData = null, $rowExistsInDatabase = false)
    {
        return self::buildRowObject('\Sma\Db\LogRow', $rowData, $rowExistsInDatabase);
    }

    /**
     * @return \Sma\Db\NotificationTable
     */
    public static function getNotificationTable()
    {
        return self::buildTableObject('\Sma\Db\NotificationTable');
    }

    /**
     * @return \Sma\Db\NotificationRow
     */
    public static function buildNotificationRow(array $rowData = null, $rowExistsInDatabase = false)
    {
        return self::buildRowObject('\Sma\Db\NotificationRow', $rowData, $rowExistsInDatabase);
    }

    /**
     * @return \Sma\Db\ProductTable
     */
    public static function getProductTable()
    {
        return self::buildTableObject('\Sma\Db\ProductTable');
    }

    /**
     * @return \Sma\Db\ProductRow
     */
    public static function buildProductRow(array $rowData = null, $rowExistsInDatabase = false)
    {
        return self::buildRowObject('\Sma\Db\ProductRow', $rowData, $rowExistsInDatabase);
    }

    /**
     * @return \Sma\Db\SearchTable
     */
    public static function getSearchTable()
    {
        return self::buildTableObject('\Sma\Db\SearchTable');
    }

    /**
     * @return \Sma\Db\SearchRow
     */
    public static function buildSearchRow(array $rowData = null, $rowExistsInDatabase = false)
    {
        return self::buildRowObject('\Sma\Db\SearchRow', $rowData, $rowExistsInDatabase);
    }

    /**
     * @return \Sma\Db\SearchTagTable
     */
    public static function getSearchTagTable()
    {
        return self::buildTableObject('\Sma\Db\SearchTagTable');
    }

    /**
     * @return \Sma\Db\SearchTagRow
     */
    public static function buildSearchTagRow(array $rowData = null, $rowExistsInDatabase = false)
    {
        return self::buildRowObject('\Sma\Db\SearchTagRow', $rowData, $rowExistsInDatabase);
    }

    /**
     * @return \Sma\Db\SequenceTable
     */
    public static function getSequenceTable()
    {
        return self::buildTableObject('\Sma\Db\SequenceTable');
    }

    /**
     * @return \Sma\Db\SequenceRow
     */
    public static function buildSequenceRow(array $rowData = null, $rowExistsInDatabase = false)
    {
        return self::buildRowObject('\Sma\Db\SequenceRow', $rowData, $rowExistsInDatabase);
    }

    /**
     * @return \Sma\Db\TicketTable
     */
    public static function getTicketTable()
    {
        return self::buildTableObject('\Sma\Db\TicketTable');
    }

    /**
     * @return \Sma\Db\TicketRow
     */
    public static function buildTicketRow(array $rowData = null, $rowExistsInDatabase = false)
    {
        return self::buildRowObject('\Sma\Db\TicketRow', $rowData, $rowExistsInDatabase);
    }

    /**
     * @return \Sma\Db\TicketLogTable
     */
    public static function getTicketLogTable()
    {
        return self::buildTableObject('\Sma\Db\TicketLogTable');
    }

    /**
     * @return \Sma\Db\TicketLogRow
     */
    public static function buildTicketLogRow(array $rowData = null, $rowExistsInDatabase = false)
    {
        return self::buildRowObject('\Sma\Db\TicketLogRow', $rowData, $rowExistsInDatabase);
    }

    /**
     * @return \Sma\Db\TicketPollTable
     */
    public static function getTicketPollTable()
    {
        return self::buildTableObject('\Sma\Db\TicketPollTable');
    }

    /**
     * @return \Sma\Db\TicketPollRow
     */
    public static function buildTicketPollRow(array $rowData = null, $rowExistsInDatabase = false)
    {
        return self::buildRowObject('\Sma\Db\TicketPollRow', $rowData, $rowExistsInDatabase);
    }

    /**
     * @return \Sma\Db\AddressContactTable
     */
    public static function getAddressContactTable()
    {
        return self::buildTableObject('\Sma\Db\AddressContactTable');
    }

    /**
     * @return \Sma\Db\AddressContactRow
     */
    public static function buildAddressContactRow(array $rowData = null, $rowExistsInDatabase = false)
    {
        return self::buildRowObject('\Sma\Db\AddressContactRow', $rowData, $rowExistsInDatabase);
    }

    /**
     * @return \Sma\Db\DocumentHistoryCurrentTable
     */
    public static function getDocumentHistoryCurrentTable()
    {
        return self::buildTableObject('\Sma\Db\DocumentHistoryCurrentTable');
    }

    /**
     * @return \Sma\Db\DocumentHistoryCurrentRow
     */
    public static function buildDocumentHistoryCurrentRow(array $rowData = null, $rowExistsInDatabase = false)
    {
        return self::buildRowObject('\Sma\Db\DocumentHistoryCurrentRow', $rowData, $rowExistsInDatabase);
    }

    /**
     * @return \Sma\Db\InvoiceDocumentTable
     */
    public static function getInvoiceDocumentTable()
    {
        return self::buildTableObject('\Sma\Db\InvoiceDocumentTable');
    }

    /**
     * @return \Sma\Db\InvoiceDocumentRow
     */
    public static function buildInvoiceDocumentRow(array $rowData = null, $rowExistsInDatabase = false)
    {
        return self::buildRowObject('\Sma\Db\InvoiceDocumentRow', $rowData, $rowExistsInDatabase);
    }

    /**
     * @return \Sma\Db\TicketWithPollsTable
     */
    public static function getTicketWithPollsTable()
    {
        return self::buildTableObject('\Sma\Db\TicketWithPollsTable');
    }

    /**
     * @return \Sma\Db\TicketWithPollsRow
     */
    public static function buildTicketWithPollsRow(array $rowData = null, $rowExistsInDatabase = false)
    {
        return self::buildRowObject('\Sma\Db\TicketWithPollsRow', $rowData, $rowExistsInDatabase);
    }
}