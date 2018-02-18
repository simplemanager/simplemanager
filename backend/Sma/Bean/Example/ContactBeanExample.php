<?php
namespace Sma\Bean\Example;

use Sma\Bean\ContactBean;
use Sma\Bean\Addon\Example;

/**
 * Exemples de contacts
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 2017
 * @package sma
 * @subpackage bean
 */
class ContactBeanExample extends ContactBean
{
    use Example;
    
    public function __construct(int $exampleNo = 1)
    {
        $this->loadExample($exampleNo);
    }
    
    protected function loadExample1()
    {
        $address = new AddressBeanExample(2);
        $this->setCivility('M.')
                ->setFirstname('Jean-François')
                ->setLastname('Doe')
                ->setEmail('jfdoe@example.fr')
                ->setFunction('Gérant')
                ->setGsm('0601234567')
                ->setTel('0123456789')
                ->setFax('0987654321')
                ->setUrl('http://www.simplemanager.org')
                ->setCompanyName('Entreprise Cliente')
                ->setAddress($address);
    }
    
    protected function loadExample2()
    {
        $address = new AddressBeanExample(1);
        $this->setCivility('Mme')
                ->setFirstname('Clémence')
                ->setLastname('Martin')
                ->setEmail('clemencemartin@example.fr')
                ->setGsm('0601234567')
                ->setTel('0123456789')
                ->setFax('0987654321')
                ->setUrl('http://www.simplemanager.org')
                //->setCompanyName('Entreprise Cliente')
                ->setAddress($address);
    }
}
