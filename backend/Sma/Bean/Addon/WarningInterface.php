<?php
namespace Sma\Bean\Addon;

/**
 * Beans ayant un détecteur de warnings
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 2017
 * @package sma
 * @subpackage bean
 */
interface WarningInterface
{
    /**
     * Y a-t-il au moins un warning ?
     * @param bool $disallowSendWarnings
     * @param bool $withAdvices
     * @return bool
     */
    public function hasWarning(bool $disallowSendWarnings = false, bool $withAdvices = false): bool;
    
    /**
     * Retourne un warning si besoin (retards, manques, etc.), par ordre d'importance
     * @param bool $firstWarnOnly
     * @param bool $disallowSendWarnings uniquement les alertes qui empêchent un envoi
     * @param bool $withAdvices inclus des conseils non critiques
     * @param bool $html retourner la version HTML
     * @return array|null Texte du warning
     */
    public function getWarnings(bool $firstWarnOnly = false, bool $disallowSendWarnings = false, bool $withAdvices = false, bool $html = false): ?array;
}
