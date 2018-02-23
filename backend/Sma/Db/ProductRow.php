<?php
namespace Sma\Db;

use Sma\Db\Generated\AbstractProductRow;
use Osf\Pdf\Document\Bean\ProductBean;
use Osf\Helper\Tab;

/**
 * Row model for table product
 *
 * Use this class to complete AbstractProductRow
 *
 * @version 1.0
 * @author Guillaume Ponçon - OpenStates Framework PHP Generator
 * @since OSF 2.0
 * @package osf
 * @subpackage generated
 */
class ProductRow extends AbstractProductRow
{
    /**
     * Unité litérale
     * @return string
     */
    public function getUnitStr(): string
    {
        $units = [
            'd' => __("jour"),
            'h' => __("heure"),
            't' => __("tonne")
        ];
        return (string) isset($units[$this->getUnit()]) ? $units[$this->getUnit()] : $this->getUnit();
    }
    
    /**
     * @return \Osf\Pdf\Document\Bean\ProductBean
     */
    public function getProductBean()
    {
        $bean = parent::getBean();
        if ($bean instanceof \Osf\Pdf\Document\Bean\ProductBean) {
            return $bean;
        }
        $bean = new ProductBean();
        $bean->populate(Tab::reduce($this->toArray(), [], ['id_account', 'bean_type', 'bean', 'date_insert', 'date_update']));
        return $bean;
    }
}