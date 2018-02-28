<?php
namespace Sma\Layout\Admin;

use Osf\Session\Mock as SessionMock;
use Osf\Test\Runner as OsfTest;
use Sma\Session\Identity;
use Sma\Container;
use Sma\Layout;

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
        
        Container::cleanMocks();
        Container::setMockNamespace(Container::MOCK_ENABLED);
        Container::registerMock(new SessionMock(), '\Osf\Session\AppSession', [], Layout::LAYOUT_NAMESPACE);
        Container::registerMock(new SessionMock(), '\Osf\Session\AppSession', [], Identity::IDENTITY_NAMESPACE);
        self::assert(Container::getSession(Layout::LAYOUT_NAMESPACE) instanceof SessionMock, 'Not a session mock');
        self::assert(Container::getSession(Identity::IDENTITY_NAMESPACE) instanceof SessionMock, 'Not a session mock');
        
        $layout = Container::getJsonRequest();
        self::assert($layout->getMenu() instanceof \Osf\Navigation\Item);
        self::assertEqual($layout->getMenu()->getItem('lin')->getLabel(), 'Connexion');
        $layout->warning('warning message');
        self::assertEqual($layout->render(null, false), '{"w":{"sidebar":{"menu":null,"registration":null},"header":{"buttons":{"msg":{"type":"messages"},"ntf":{"type":"notifications"},"alr":{"type":"alerts"}},"user":null,"settings":null},"page":{"title":null,"links":[],"alerts":[{"title":null,"message":"warning message","status":"warning","closable":true}],"content":null,"scripts":null},"footer":{"content":null,"links":[]}}}');
        
        return self::getResult();
    }
}
