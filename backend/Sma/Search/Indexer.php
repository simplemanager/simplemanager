<?php
namespace Sma\Search;

use Osf\Helper\Tab;
use Osf\Controller\Cli;
use Osf\Exception\ArchException;
use App\Recipient\Model\RecipientDbManager as RM;
use App\Product\Model\ProductDbManager as PM;
use Sma\Session\Identity as I;
use Sma\Container;
use Sma\Search;
use DB, H, ACL;

/**
 * Indexations
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 2017
 * @package sma
 * @subpackage search
 */
class Indexer
{
    /**
     * @param int|null $idAccount
     */
    public static function indexAll($idAccount)
    {
        self::indexProducts($idAccount);
        self::indexRecipients($idAccount);
        self::indexPages($idAccount);
        self::indexDocuments($idAccount);
    }
    
    /**
     * Indexation des produits
     * @param int $idAccount
     */
    public static function indexProducts($idAccount = null)
    {
        self::checkIdAccount($idAccount);
        PM::updateAllProductsForSearchEngine($idAccount);
    }
    
    /**
     * Indexation des contacts
     * @param int $idAccount
     */
    public static function indexRecipients($idAccount = null)
    {
        self::checkIdAccount($idAccount);
        RM::updateAllRecipientsForSearchEngine($idAccount);
    }
    
    /**
     * Indexation des pages de l'application
     * @param int $idAccount
     */
    public static function indexPages($idAccount = null)
    {
        // Check et récupération des infos
        self::checkIdAccount($idAccount);
        $apps = include APPLICATION_PATH . '/App/Common/Generated/apps.php';
        $menu = include APPLICATION_PATH . '/App/Common/Generated/menu.php';
        $idAccount = $idAccount ?? I::getIdAccount();
        $accountEmail = !Cli::isCli() && $idAccount === I::getIdAccount() 
                ? I::get('email') 
                : (DB::getAccountTable()->find($idAccount) ?? false);
        if (!$accountEmail) {
            throw new ArchException('Account [' . $idAccount . '] not found');
        }
        $accountEmail = is_object($accountEmail) ? $accountEmail->getEmail() : $accountEmail;
        $role = ACL::getRoleFromEmail($accountEmail);
        $acl = Container::getAcl();
        $tags = [Search::TAG_PAGE];
        
        // Nettoyage préliminaire
        self::search()->clean(Search::TAG_PAGE, $idAccount);

        // Construction des pages
        $appsDone = [];
        foreach ($menu as $item) {
            
            // Filtrage
            if (!isset($item['label']) || 
                !isset($item['params']['controller'])) {
                continue;
            }
            $controller = $item['params']['controller'];
            $action = isset($item['params']['action']) ? $item['params']['action'] : null;
            if (!$acl->isAllowedParams($controller, $action, $role, $accountEmail)) {
                continue;
            }
            
            // Informations sur l'application
            $app = [];
            if (isset($item['app']) && isset($apps['app'][$item['app']]) && !in_array($item['app'], $appsDone)) {
                $app = $apps['app'][$item['app']];
                $appsDone[] = $item['app'];
            }
            
            // Récupération des infos
            $title  = $item['label']; // . (isset($app['title']) && isset($app['title']['medium']) ? ' - ' . $app['title']['medium'] : '');
            $level  = $action === null ? 12 : 11;
            $params = $item['params'];
            unset($params['controller']);
            unset($params['action']);
            $url    = H::url($controller, $action, $params);
            $doc    = $item['label']
                    . (isset($app['title']) && isset($app['title']['short'])  ? ' ' . $app['title']['short']  : '')
                    . (isset($app['title']) && isset($app['title']['medium']) ? ' ' . $app['title']['medium'] : '')
                    . (isset($app['title']) && isset($app['title']['long'])   ? ' ' . $app['title']['long']   : '');

            // Indexation de la page
            self::search()->index($title, $level, $item['params'], $url, $doc, $tags, $idAccount);

            // Ajout des pages supplémentaires 
            if (isset($app['pages'])) {
                foreach ($app['pages'] as $page) {
                    $params = isset($page['params']) ? $page['params'] : [];
                    $controller = isset($params['controller']) ? $params['controller'] : $controller;
                    $action = isset($params['action']) ? $params['action'] : null;
                    if ($acl->isAllowedParams($controller, $action, $role)) {
                        $params = Tab::reduce($params, [], ['controller', 'action']);
                        $url = H::url($controller, $action, $params);
                        $level = isset($page['level']) ? (int) $page['level'] : 10;
                        $keywords = isset($page['keywords']) ? $page['keywords'] : null;
                        self::search()->index($page['title'], $level, $page, $url, $keywords, $tags, $idAccount);
                    }
                }
            }
        }
    }
    
    /**
     * Indexation des documents (factures, lettres...)
     * @param int $idAccount
     */
    public static function indexDocuments($idAccount = null)
    {
        self::checkIdAccount($idAccount);
        DB::getDocumentTable()->indexAllDocumentsForSearchEngine($idAccount);
//        RM::updateAllRecipientsForSearchEngine($idAccount);
    }
    
    /**
     * Vérification de l'idAccount, null = current id account
     * @param int $idAccount
     */
    protected static function checkIdAccount(&$idAccount)
    {
        if ($idAccount !== null) {
            if (is_numeric($idAccount)) {
                $idAccount = (int) $idAccount;
            } else {
                throw new ArchException('Bad id account type');
            }
        } else {
            $idAccount = I::getIdAccount();
        }
        if ($idAccount === null || !$idAccount) {
            throw new ArchException('No id account found');
        }
    }
    
    /**
     * @return \Sma\Search
     */
    protected static function search()
    {
        return Container::getSearch();
    }
}
