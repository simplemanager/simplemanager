<?php
namespace App\Account\Model;

use Sma\Session\Identity as I;
use DB;

/**
 * Description of Auth
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright (c) 2014 , OpenStates
 * @since OFT 2.0 - 9 janv. 2014
 * @package name
 * @subpackage name
 */
class Identity {

    /**
     * Mise à jour des paramètres liés au statut juridique
     * @param type $email
     * @param type $password
     * @return 
     */
    public static function updateLegalStatusParams(string $legalStatus)
    {
        $params = I::getParams() ?? [];
        $taxFranch = (int) in_array($legalStatus, ['ae', 'ei']); // 'a', 
        $params['company']['taxfranch']  = $taxFranch;
//        $params['product']['withTax']    = $taxFranch;
//        $params['product']['defaultTax'] = $taxFranch ? 0 : 20;
        return self::updateParams($params);
    }
    
    /**
     * Mise à jour des paramètres dans la base et la session
     * @param array $values
     * @return int
     */
    public static function updateParams(array $values)
    {
        I::set('params', $values);
        return DB::getAccountTable()
            ->find(I::getIdAccount())
            ->setBean($values)
            ->save();
    }
}
