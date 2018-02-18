<?php
namespace Sma\Bean;

/**
 * Interface commune pour les lettres, factures, etc. à indexer
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 2017
 * @package sma
 * @subpackage bean
 */
interface ExchangeableBeanInterface
{
    /**
     * @return array
     */
    public function exportToArray(): array;
    
    /**
     * @return array
     */
    public static function exportLegend(): array;
}