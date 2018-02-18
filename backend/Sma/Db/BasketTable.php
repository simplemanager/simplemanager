<?php
namespace Sma\Db;

use Sma\Bean\InvoiceBean;
use Sma\Session\Identity;
use Sma\Db\Generated\AbstractBasketTable;

/**
 * Table model for table basket
 *
 * Use this class to complete AbstractBasketTable
 *
 * @version 1.0
 * @author Guillaume Ponçon - OpenStates Framework PHP Generator
 * @since OSF 2.0
 * @package osf
 * @subpackage generated
 */
class BasketTable extends AbstractBasketTable
{
    /**
     * Mise à jour des produits d'une facture
     * @param \Sma\Db\InvoiceBean $bean
     * @return $this
     */
    public function updateBasket(InvoiceBean $bean)
    {
        // Suppression des produits
        $where = [
            'id_account' => Identity::getIdAccount(), 
            'id_invoice' => $bean->getIdInvoice()
        ];
        $this->delete($where);
        
        // Insertion
        /* @var $product \Osf\Pdf\Document\Bean\ProductBean */
        foreach ($bean->getProducts() as $product) {
            $this->insert([
                'id_account' => Identity::getIdAccount(),
                'id_invoice' => $bean->getIdInvoice(),
                'id_product' => $product->getId(),
                'quantity'   => $product->getQuantity(),
                'discount'   => $product->getDiscount()
            ]);
        }
        
        return $this;
    }
}