<?php 
namespace App\Common\View;

/**
 * Additional application helpers
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 23 sept. 2013
 * @package common
 * @subpackage helpers
 */
class Helper extends \Osf\View\Helper
{
    // Ajouter ici les helpers à utiliser dans l'ensemble des applications
    public function init()
    {
        if ($this->initialized) {
            return;
        }
        parent::init();
        $this->registerHelpers(array(
//            'addBox'      => __NAMESPACE__ . '\Helper\AddBox',
            'addLink'     => __NAMESPACE__ . '\Helper\AddLink',
 //           'adminLayout' => __NAMESPACE__ . '\Helper\AdminLayout',
 //           'sidebarMenu' => __NAMESPACE__ . '\Helper\SidebarMenu',
            )
        );
    }
}
