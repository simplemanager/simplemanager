<?php
namespace Sma\Layout;

use Osf\Container\AbstractStaticContainer;
use Osf\View\Helper\Bootstrap\Addon\IconInterface;
use Osf\View\Helper\Bootstrap\Addon\ColorInterface;
use Osf\View\Helper\Bootstrap\Addon\StatusInterface;

/**
 * Layout quick helpers tools
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 2017
 * @package sma
 * @subpackage layout
 */
abstract class AbstractLayoutContainer
       extends AbstractStaticContainer
    implements IconInterface, ColorInterface, StatusInterface
{
}