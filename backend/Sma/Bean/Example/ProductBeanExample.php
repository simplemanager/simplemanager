<?php
namespace Sma\Bean\Example;

use Osf\Pdf\Document\Bean\ProductBean;
use Sma\Bean\Addon\Example;

/**
 * Exemples de produits
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 2017
 * @package sma
 * @subpackage bean
 */
class ProductBeanExample extends ProductBean
{
    use Example;
    
    public function __construct(int $exampleNo = 1)
    {
        $this->loadExample($exampleNo);
    }
    
    protected function loadExample1()
    {
        $this->setCode('ORD0001')
                ->setTitle('Ordinateur Portable LDX34')
                ->setPrice(1299)
                ->setDescription("* Disque SSD 500go PCIe\n* Processeur 3.8Gz, Cache 4Mo")
                ->setDiscount(5)
                ->setQuantity(1)
                ->setTax(20);
    }
    
    protected function loadExample2()
    {
        $this->setCode('PER0001')
                ->setTitle('Batterie supplémentaire 6 Cellules')
                ->setPrice(89)
                ->setQuantity(1)
                ->setDiscount(10)
                ->setTax(20);
    }
    
    protected function loadExample3()
    {
        $this->setCode('SAV0001')
                ->setTitle('Support technique')
                ->setPrice(39.5)
                ->setQuantity(3)
                ->setUnit('h')
                ->setTax(20);
    }
    
    protected function loadExample4()
    {
        $this->setCode('ENV0001')
                ->setTitle("Frais d'emballage et de port")
                ->setDescription('Livraison du jour pour le lendemain')
                ->setPrice(29)
                ->setTax(0);
    }
}
