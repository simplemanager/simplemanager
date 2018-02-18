<?php
namespace Sma\Db;

use Sma\Db\Generated\AbstractProductTable;
use Sma\Db\DbRegistry\Exchangeable;
use Sma\Bean\BeanCollection;
use Sma\Bean\ProductBean;
use App\Product\Model\ProductDbManager as PDM;

/**
 * Table model for table product
 *
 * Use this class to complete AbstractProductTable
 *
 * @version 1.0
 * @author Guillaume Ponçon - OpenStates Framework PHP Generator
 * @since OSF 2.0
 * @package osf
 * @subpackage generated
 */
class ProductTable extends AbstractProductTable implements Exchangeable
{
    /**
     * @param array $settings
     * @return BeanCollection
     */
    public static function getBeans(array $settings = []): BeanCollection
    {
        return new BeanCollection(PDM::getProductsForTable($settings, array_keys(ProductBean::exportLegend())), new ProductBean());
    }
}