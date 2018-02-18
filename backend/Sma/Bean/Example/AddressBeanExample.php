<?php
namespace Sma\Bean\Example;

use Osf\Pdf\Document\Bean\AddressBean;
use Sma\Bean\Addon\Example;

/**
 * Exemples d'adresses
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 2017
 * @package sma
 * @subpackage bean
 */
class AddressBeanExample extends AddressBean
{
    use Example;
    
    public function __construct(int $exampleNo = 1)
    {
        $this->loadExample($exampleNo);
    }
    
    protected function loadExample1()
    {
        $this->setAddress("23 rue de l'exemple\nQuartier Jean Jaures")
            ->setPostalCode('44000')
            ->setCity('Nantes CEDEX 02');
    }
    
    protected function loadExample2()
    {
        $this->setAddress("45 boulevard de la démonstration")
            ->setPostalCode('44000')
            ->setCity('Nantes')
            ->setCountry('France');
    }
}
