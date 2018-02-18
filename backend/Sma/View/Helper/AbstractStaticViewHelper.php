<?php
namespace Sma\View\Helper;

use Osf\View\Generated\StaticGeneratedViewHelper;
use Sma\Container;

/**
 * Classe mère du static view helper généré pour les appels aux helpers locaux en plus des helpers osf
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 2017
 * @package sma
 * @subpackage view
 */
abstract class AbstractStaticViewHelper extends StaticGeneratedViewHelper
{
    /**
     * @return \Osf\View\AbstractHelper
     */
    protected static function getViewHelper()
    {
        if (!self::$viewHelper) {
            self::$viewHelper = Container::getViewHelper();
        }
        return self::$viewHelper;
    }
}
