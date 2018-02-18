<?php
namespace Sma\Bean\Example;

use Sma\Bean\LetterBean;
use Sma\Bean\Addon\Example;

/**
 * Exemple de lettre
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 2017
 * @package sma
 * @subpackage bean
 */
class LetterBeanExample extends LetterBean
{
    use Example;
    
    public function __construct(int $exampleNo = 1)
    {
        parent::__construct();
        $this->loadExample($exampleNo);
    }
    
    protected function loadExample1()
    {
        
    }
}