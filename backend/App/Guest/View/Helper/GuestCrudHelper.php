<?php
namespace App\Guest\View\Helper;

use Sma\View\Helper\Crud\CrudConfig;
use Sma\View\Helper\Crud;
use App\Common\Container;
use H;

/**
 * Opérations communes aux vues guest crud
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 2017
 * @package app
 * @subpackage guest
 */
class GuestCrudHelper
{
    /**
     * Génération du crud pour les lettres, devis, commandes, factures
     * @param string $title
     * @param string $msgNoItem
     * @param string $icon
     * @param callable $linkPattern
     * @param string|null $status
     * @param CrudConfig|null $config
     * @return Crud
     */
    public static function buildCrud(string $title, string $msgNoItem, string $icon, callable $linkPattern, ?string $status, ?CrudConfig $config = null): Crud
    {
        // Mise à jour partielle de la page ?
        $refresh = (bool) Container::getRequest()->getParam('tp');
        $refreshUrl = H::url(Container::getRequest()->getController(),
                             Container::getRequest()->getAction(),
                             Container::getRequest()->getParams());

        $config = ($config ?? new CrudConfig());
        $config->setRefresh($refresh)
               ->setMsg(CrudConfig::MSG_NOITEM, $msgNoItem)
               ->setMsg('title', $title)
               ->setIcon($icon)
               ->setTrAttr('onclick', "\$.ajaxCall('" . $refreshUrl . "');")
               ->setPagination(30)
               ->setLinkPattern($linkPattern, true);

        // Affichage du tableau CRUD
        return H::crud($config)
                ->setBox(clone H::box('')
                        ->coloredTitleBox()
                        ->status($status)
                        ->setAppend((string) H::html(H::link(H::button(__("Actualiser"))->sizeLarge()->status($status ?? 'default'), 
                                Container::getRequest()->getController(), 
                                Container::getRequest()->getAction())->setIsAjaxLink(), 'div')
                                    ->escape(false)
                                    ->addCssClass('text-center'))
                        );
    }
}
