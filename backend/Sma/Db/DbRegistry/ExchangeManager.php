<?php
namespace Sma\Db\DbRegistry;

use Zend\Db\Adapter\Driver\ResultInterface;
use App\Product\Model\ProductDbManager as PDM;
use App\Recipient\Model\RecipientDbManager as RDM;

/**
 * Export CSV rapide (voir si on garde ou pas...)
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 2017
 * @package sma
 * @subpackage db
 * @deprecated since version 1.0
 */
trait ExchangeManager
{
    /**
     * Génère un flux CSV sur la sortie standard
     * @param ResultInterface $rows
     * @param array $legends
     */
    protected static function exportCsv(ResultInterface $rows, array $legends): void
    {
        echo "\xEF\xBB\xBF"; // UTF-8 BOM
        $out = fopen('php://output', 'w');
        fputcsv($out, array_values($legends));
        foreach ($rows as $row) {
            fputcsv($out, $row);
        }
        fclose($out);
    }
    
    protected static function getProductRows($onlyKeys = false)
    {
        $rows = [
//             'uid' => __("Id"),
            'code' => __("Code"),
            'title' => __("Nom"),
            'price' => __("Prix"),
            'price_type' => __("HT ou TTC"),
            'tax' => __("TVA"),
            'discount' => __("Remise"),
            'status' => __("Activé"),
            'description' => __("Description")
        ];
        return $onlyKeys ? array_keys($rows) : $rows;
    }
    
    /**
     * Génère un flux de produits sur la sortie standard
     * @param array $settings
     */
    public static function exportCsvProducts(array $settings): void
    {
        $rows = PDM::getProductsForTable($settings, self::getProductRows(true));
        self::exportCsv($rows, self::getProductRows());
    }
    
    protected static function getContactRows($onlyKeys = false)
    {
        $rows = [
            'company.id as id' => 'Id',
            'company.title as title' => __("Nom Société"),
            'company.tel as tel' => __("Téléphone"),
            'company.fax as fax' => __("Fax"),
            'company.email as email' => __("E-mail"),
            'company.url as url' => 'Url',
            'company.description as description' => __("Mots Clés"),
            'address.address as address' => __("Adresse"),
            'address.postal_code as postal_code' => __("Code Postal"),
            'address.city as city' => __("Ville"),
            'address.country as country' => __("Pays"),
            'contact.civility as civility' => __("Civilité"),
            'contact.firstname as firstname' => __("Prénom Contact"),
            'contact.lastname as lastname' => __("Nom Contact"),
            'contact.gsm as gsm' => __("GSM Contact"),
        ];
        
        return $onlyKeys ? array_keys($rows) : $rows;
    }
    
    public static function exportCsvContacts(array $settings)
    {
        $rows = RDM::getContactsForTable($settings, self::getContactRows(true, true));
        return self::exportCsv($rows, self::getContactRows());
    }
}
