<?php
namespace Sma\Layout\Admin;

use Sma\Container;
use Osf\Session\Mock as SessionMock;
use Osf\Test\Runner as OsfTest;

/**
 * Admin menu test
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 2017
 * @package osf
 * @subpackage test
 */
class Test extends OsfTest
{
    public static function run()
    {
        self::reset();
        
        $sessionMock = new SessionMock();
        Container::setMockNamespace('mock');
        Container::cleanMocks();
        Container::registerMock($sessionMock, '\Osf\Session\AppSession', [], 'jslayout');
        
        $layout = Container::getJsonRequest();
        self::assert($layout->getMenu() instanceof \Osf\Navigation\Item);
        self::assertEqual($layout->getMenu()->getItem('lin')->getLabel(), 'Connexion');
        $layout->warning('warning message');
        self::assertEqual($layout->render(), '{"w":{"sidebar":{"menu":null,"registration":null},"header":{"buttons":{"msg":{"type":"messages"},"ntf":{"type":"notifications"},"alr":{"type":"alerts"}},"user":null,"settings":null},"page":{"title":null,"links":[],"alerts":[{"title":null,"message":"warning message","status":"warning","closable":true}],"content":null,"scripts":null},"footer":{"content":null,"links":[]}}}');
        
        return self::getResult();
    }
}
