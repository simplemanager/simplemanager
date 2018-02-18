<?php
namespace Www\View;

/**
 * App additional helpers
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 23 sept. 2013
 * @package www
 * @subpackage helpers
 */
class Helper extends \Common\View\Helper
{
    // Ajouter ici les helpers
    public function init()
    {
        if ($this->initialized) {
            return;
        }
        parent::init();
        //$this->registerHelpers(array());
    }
}