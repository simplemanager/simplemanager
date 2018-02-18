<?php
namespace Sma\Db;

use Osf\Exception\ArchException;
use Osf\Exception\DisplayedException;
use Osf\Stream\Text as T;
use Osf\Helper\Mysql;
use Osf\Application\OsfApplication as Application;
use Sma\Db\Generated\AbstractInvoiceTable;
use Sma\Bean\InvoiceBean;
use Sma\Session\Identity;
use Sma\Bean\InvoiceBean as IB;
use Sma\Db\InvoiceRow;
use Sma\Cache as SC;
use Sma\Log;
use App\Common\Container as C;
use DB, DateTime;

/**
 * Table model for table invoice
 *
 * Use this class to complete AbstractInvoiceTable
 *
 * @version 1.0
 * @author Guillaume Ponçon - OpenStates Framework PHP Generator
 * @since OSF 2.0
 * @package osf
 * @subpackage generated
 */
class InvoiceTable extends AbstractInvoiceTable
{
    use Addon\EventActions;
    use Addon\SafeActions;
    
    /**
     * Mise à jour d'un invoice
     * @param InvoiceBean $bean
     * @param string|null $eventComment
     * @return $this
     */
    public function saveInvoice(InvoiceBean $bean, ?string $eventComment = null)
    {
        // Mise à jour
        $bean->setDate();
        if ($bean->getIdInvoice()) {
            $row = $this->find($bean->getIdInvoice())->toArray();
            $this->update($this->buildRow($bean, $row), ['id' => $bean->getIdInvoice(), 'id_account' => $row['id_account']]);
            $this->registerEvent($bean->getIdDocument(), $bean->getIdDocumentHistory(), $bean::EVENT_UPDATE, $eventComment);
            C::getCacheSma()->cleanItem(SC::C_DOCUMENT, $bean->getIdDocument());
        } 
        
        // Création
        else {
            $this->insert($this->buildRow($bean));
            $invoiceId = $this->getLastInsertValue();
            $bean->setIdInvoice($invoiceId);
            $this->registerEvent($bean->getIdDocument(), $bean->getIdDocumentHistory(), $bean::EVENT_CREATION, $eventComment);
        }
        
        // Ajout dans le basket (désactivé, trop lent)
        //DB::getBasketTable()->updateBasket($bean);
        
        return $this;
    }
    
    /**
     * Construction du row à partir du bean
     * @param IB $bean
     * @param array $row
     * @return array
     * @throws ArchException
     */
    protected function buildRow(InvoiceBean $bean, array $row = []): array
    {
        // Calculs du nombre de produits et des totaux
        /* @var $product \Osf\Pdf\Document\Bean\ProductBean */
        $productCount = 0;
        $totalHt = 0;
        $totalTtc = 0;
        foreach ($bean->getProducts() as $product) {
            $productCount++;
            $totalHt += $product->getTotalPriceHT();
            $totalTtc += $product->getTotalPriceTTC();
        }
        
        // Tests de cohérence
        if (Application::isDevelopment() && $totalHt != $bean->getTotalHtWithDiscount()) {
            throw new ArchException(sprintf(__("Totaux incohérents [%s] [%s]"), $totalHt, $bean->getTotalHtWithDiscount()));
        }
        if (Application::isDevelopment() && $totalTtc != $bean->getTotalTtcWithDiscount()) {
            throw new ArchException(sprintf(__("Totaux incohérents [%s] [%s]"), $totalHt, $bean->getTotalHtWithDiscount()));
        }
        
        // Row
        $row['id_account'] = Identity::getIdAccount();
//        $row['status'] = $bean->getStatus();
//        $row['type'] = $bean->getType();
        $row['code'] = $bean->getCode();
        $row['product_count'] = $productCount;
        $row['total_ht'] = $bean->getTotalHtWithDiscount(true);
        $row['total_ttc'] = $bean->getTotalTtcWithDiscount(true);
        $row['description'] = $bean->getDescription();
        $row['id_provider'] = $bean->getProvider()->getIdCompany();
        $row['id_recipient'] = $bean->getRecipient()->getId();
        $row['id_document'] = $bean->getIdDocument();
        $row['id_document_history'] = $bean->getIdDocumentHistory();
        $row['bean'] = serialize($bean);
        $row['uid'] = isset($row['uid']) ? $row['uid'] : DB::getSequenceTable()->nextValue($bean->getType() . '_uid');
        
        return $row;
    }
    
    /**
     * @param string $type
     * @return \Iterator
     */
    public function getInvoices($type = null, array $settings = [])
    {
        $sorts = [
            'ca'  => 'invoice.code ASC',
            'cd'  => 'invoice.code DESC',
            'pa'  => 'invoice.total_ht ASC',
            'pd'  => 'invoice.total_ht DESC',
            'sa'  => 'document.status ASC',
            'sd'  => 'document.status DESC',
            'dca' => 'invoice.date_insert ASC',
            'dcd' => 'invoice.date_insert DESC',
            'dua' => 'invoice.date_update ASC',
            'dud' => 'invoice.date_update DESC',
        ];
        
        $sort = isset($settings['s']) && isset($sorts[$settings['s']]) ? $sorts[$settings['s']] : $sorts['cd'];
        
        $params = [Identity::getIdAccount()];
        $fields = 'document.id as id, invoice.uid, document.status, document.type, invoice.code, invoice.product_count, '
                . 'invoice.total_ht, invoice.total_ttc, invoice.description, invoice.date_insert, '
                . 'invoice.date_update, invoice.bean';
        $sql = 'SELECT ' . $fields . ' FROM invoice, document '
                . 'WHERE invoice.id_document=document.id '
                . 'AND invoice.id_account=? ';
        if ($type !== null) {
            $sql .= 'AND document.type=? ';
            $params[] = $type;
        }
        if (isset($settings['q']) && $settings['q'] !== '') {
            $sql .= 'AND invoice.description LIKE ? ';
            $params[] = Mysql::like($settings['q']);
        }
        if (isset($settings['f']) && $settings['f']) {
            $sql .= 'AND invoice.date_update >= ? ';
            $params[] = Mysql::dateToMysql($settings['f']);
        }
        if (isset($settings['t']) && $settings['t']) {
            $sql .= 'AND invoice.date_update <= ? ';
            $params[] = Mysql::dateToMysql($settings['t']) . ' 23:99:99';
        }
        if (isset($settings['pi']) && $settings['pi']) {
            $sql .= 'AND invoice.total_ht >= ? ';
            $params[] = $settings['pi'];
        }
        if (isset($settings['pa']) && $settings['pa']) {
            $sql .= 'AND invoice.total_ht <= ? ';
            $params[] = $settings['pa'];
        }
        if (isset($settings['st']) && $settings['st']) {
            $sql .= 'AND document.status = ? ';
            $params[] = $settings['st'];
        }
        
        $sql .= ' ORDER BY ' . $sort;
        return $this->prepare($sql)->execute($params);
    }
    
    /**
     * @return \Sma\Db\InvoiceBean
     */
    public function getInvoiceBean(int $id)
    {
        $bean = $this->find($id)->getBean();
        return $bean ? unserialize($bean) : null;
    }
    
    /**
     * Suppression d'un invoice depuis l'id du document
     * @param int $idDocument
     */
    public function deleteInvoiceFromIdDocument(int $idDocument, bool $forceDelete = false)
    {
        $params = ['id_document' => $idDocument, 'id_account' => Identity::getIdAccount()];
        $row = $this->select($params)->current();
        if ($row && $row['type'] == IB::TYPE_INVOICE) {
            throw new DisplayedException(__("Supprimer une facture est interdit. Créez un avoir ou une facture rectificative si nécessaire."));
        } else {
            $row->delete();
            C::getCacheSma()->cleanItem(SC::C_DOCUMENT, $idDocument);
        }
    }
    
    /**
     * @param int $idDocument
     * @return InvoiceBean
     * @throws ArchException
     */
    public function getInvoiceBeanFromIdDocument(int $idDocument): InvoiceBean
    {
        $sql = 'SELECT invoice.id, document.status, document.type, invoice.code, invoice.description, invoice.bean '
                . 'FROM invoice, document '
                . 'WHERE invoice.id_document=document.id AND invoice.id_document=? AND invoice.id_account=?';
        $result = $this->prepare($sql)->execute([$idDocument, Identity::getIdAccount()]);
        if ($result->count() !== 1) {
            throw new ArchException('Unable to found invoice for document ' . $idDocument . ' and account ' . Identity::getIdAccount());
        }
        $row = $result->current();
        
        /* @var $bean \Sma\Bean\InvoiceBean */
        $bean = unserialize($row['bean']);
        $bean->update($row);
        return $bean;
    }
    
    /**
     * Statistiques de ventes de l'utilisateur courant
     * @param DateTime $from
     * @param DateTime $to
     * @return \Zend\Db\Adapter\Driver\Mysqli\Result
     */
    public function getStats(DateTime $from, DateTime $to)
    {
        $strFrom = Mysql::dateToMysql($from);
        $strTo   = Mysql::dateToMysql($to);
        
        $sql = 'SELECT SUM(invoice.total_ht) as total, count(*) as count, document.type, document.status FROM invoice, document '
                . 'WHERE invoice.id_document=document.id '
                . 'AND invoice.id_account=? '
                . 'AND invoice.date_update >= ? '
                . 'AND invoice.date_update < ? '
                . 'GROUP BY document.type, document.status';
        
        $params = [Identity::getIdAccount(), $strFrom, $strTo];
        $rows = $this->prepare($sql)->execute($params);
        
        $stats = [
            'quote' => [
                'created'   => ['total' => '0 €', 'count' => 0],
                'sent'      => ['total' => '0 €', 'count' => 0],
                'processed' => ['total' => '0 €', 'count' => 0],
                'canceled'  => ['total' => '0 €', 'count' => 0]
            ],
            'order' => [
                'created'   => ['total' => '0 €', 'count' => 0],
                'sent'      => ['total' => '0 €', 'count' => 0],
                'processed' => ['total' => '0 €', 'count' => 0],
                'canceled'  => ['total' => '0 €', 'count' => 0]
            ],
            'invoice' => [
                'created'   => ['total' => '0 €', 'count' => 0],
                'sent'      => ['total' => '0 €', 'count' => 0],
                'processed' => ['total' => '0 €', 'count' => 0],
                'canceled'  => ['total' => '0 €', 'count' => 0]
            ],
        ];
        
        foreach ($rows as $row) {
            $stats[$row['type']][$row['status']] = ['total' => T::currencyFormat($row['total']), 'count' => (int) $row['count']];
        }

        return $stats;
    }
    
    /**
     * Vérifie si un code existe déjà (pour la contrainte d'unicité)
     * @param string $code
     * @param int|null $exceptDocumentId
     * @return bool
     */
    public function invoiceExists(string $code, ?int $exceptDocumentId = null): bool
    {
        $sql = 'SELECT id FROM ' . $this->getTableName() . ' WHERE id_account = ? AND code = ? ';
        $params = [Identity::getIdAccount(), $code];
        if ($exceptDocumentId !== null) {
            $sql .= ' AND id_document != ? ';
            $params[] = $exceptDocumentId;
        }
        return (bool) $this->prepare($sql)->execute($params)->current();
    }
}
