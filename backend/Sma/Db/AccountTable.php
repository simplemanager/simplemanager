<?php
namespace Sma\Db;

use Osf\Stream\Text as T;
use Sma\Db\Generated\AbstractAccountTable;
use Sma\Bean\ContactBean;
use Sma\Image;
use DB;

/**
 * Table model for table account
 *
 * Use this class to complete AbstractAccountTable
 *
 * @version 1.0
 * @author Guillaume Ponçon - OpenStates Framework PHP Generator
 * @since OSF 2.0
 * @package osf
 * @subpackage generated
 */
class AccountTable extends AbstractAccountTable
{
    /**
     * Liste des comptes pour la ligne de commandes
     * @param string|null $filter
     * @param bool $formatString
     * @return mixed
     */
    public function getList($filter = null, bool $formatString = true)
    {
        $sql = 'SELECT id, email, firstname, lastname, status '
                . 'FROM ' . $this->getTableName() . ' WHERE 1 ';
        $params = [];
        if ($filter !== null) {
            $sql .= 'AND (email LIKE ? ';
            $params[] = '%' . $filter . '%';
            $sql .= 'OR lower(firstname) LIKE ? ';
            $params[] = '%' . $filter . '%';
            if (is_numeric($filter)) {
                $sql .= 'OR id=? ';
                $params[] = $filter;
            }
            $sql .= 'OR lower(lastname) LIKE ?) ';
            $params[] = '%' . $filter . '%';
        }
        $sql .= 'ORDER BY id DESC LIMIT 100';
        $rows = $this->prepare($sql)->execute($params);
        
        if (!$formatString) {
            return $rows;
        }
        
        $retVal = '';
        foreach ($rows as $row) {
            $str = "| %4d | %4s | %'.50s | %'.50s |\n";
            $retVal .= vsprintf($str, [
                $row['id'],
                T::toUpper(substr($row['status'], 0, 4)),
                T::toIso(T::crop($row['email'], 50)),
                T::toIso(T::crop($row['firstname'] . ' ' . $row['lastname'], 50))
            ]);
        }
        return T::toUnicode($retVal);
    }
    
    /**
     * Statistiques sur un compte donné
     * @param int $idAccount
     * @return array|null
     */
    public function buildInfo(int $idAccount): ?array
    {
        /* @var $account \Sma\Db\AccountRow */
        /* @var $company \Sma\Db\CompanyRow */
        /* @var $contact \Sma\Bean\ContactBean */
        
        $account = $this->find($idAccount);
        $company = DB::getCompanyTable()->buildSelect(['type' => 'mine', 'id_account' => $idAccount])->execute()->current();
        $contact = $company ? DB::getContactTable()->getBean($company->getIdContact(), false) : null;
        $imgUrl  = $company && $company->getIdLogo() ? Image::getImageUrl($company->getIdLogo()) : null;
        
        return [
            $account,
            $company,
            $contact,
            $imgUrl
        ];
    }
}
