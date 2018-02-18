<?php
namespace Sma\Bean;

use Osf\Bean\AbstractBean;
use Osf\Helper\Tab;

/**
 * Tout type de produit
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 2017
 * @package sma
 * @subpackage bean
 */
class ProductBean extends AbstractBean implements ExchangeableBeanInterface
{
    protected $row;
    
    /**
     * @param array $row
     * @return $this
     */
    public function populate(array $data, bool $noError = false)
    {
        $this->row = $data;
        return $this;
    }
    
    // IMPORT / EXPORT
    
    public static function exportLegend(): array
    {
        return [
            'code'        => __("Code"),
            'title'       => __("Nom"),
            'price'       => __("Prix"),
            'price_type'  => __("HT ou TTC"),
            'tax'         => __("TVA (%)"),
            'discount'    => __("Remise (%)"),
            'status'      => __("Activé"),
            'description' => __("Description")
        ];
    }

    public function exportToArray(): array 
    {
        $row = Tab::reduce($this->row, array_keys(self::exportLegend()));
        $row['status'] = $row['status'] ? __("oui") : __("non");
        return $row;
    }
}