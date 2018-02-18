<?php
namespace Sma\Generator;

use Osf\Generator\OsfGenerator;

/**
 * Sma helpers generator
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 2017
 * @package sma
 * @subpackage generator
 */
class SmaGenerator extends OsfGenerator
{
    protected $namespaces = [
            [
                'name' => "\\Sma\\View\\Helper\\",
                'prefix' => '',
                'pattern' => '/Sma/View/Helper/*.php'
            ],
        ];
    
    protected $blackList = [
            'Test',
            'AbstractHelper',
            'AbstractViewHelper',
            'Crud'
        ];
    
    protected $namespace = "Sma\\View\\Generated";
    protected $classFile = __DIR__ . '/../View/Generated/AbstractGeneratedViewHelper.php';
    protected $classUses = ['Osf\View\Generated\AbstractGeneratedViewHelper as OsfAbstractGeneratedViewHelper'];
    protected $classExtends = 'OsfAbstractGeneratedViewHelper';
    protected $staticClassFile = __DIR__ . '/../View/Generated/StaticGeneratedViewHelper.php';
    protected $staticClassUses = ['Sma\View\Helper\AbstractStaticViewHelper'];
    protected $staticClassExtends = 'AbstractStaticViewHelper';
    
    public function getBasePath()
    {
        return dirname(dirname(__DIR__));
    }
}