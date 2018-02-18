<?php
namespace Sma\Db\DbRegistry;

use Osf\Db\Table\TableGatewayInterface;
use Sma\Bean\BeanCollection;

/**
 * Méthodes à implémenter pour les tables compatibles import/export
 * 
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 2017
 * @package sma
 * @subpackage db
 */
interface Exchangeable extends TableGatewayInterface
{    
    /**
     * @return BeanCollection
     */
    public static function getBeans(array $settings = []): BeanCollection;
}
