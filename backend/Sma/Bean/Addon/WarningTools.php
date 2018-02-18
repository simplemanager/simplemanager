<?php
namespace Sma\Bean\Addon;

use Osf\View\Helper\Bootstrap\AbstractViewHelper as AVH;
use Osf\View\Helper\Bootstrap\Tools\Checkers;
use Sma\Controller\Json;
use H;

/**
 * Warning build tools
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 2017
 * @package sma
 * @subpackage bean
 */
trait WarningTools
{
    /**
     * Y a-t-il au moins un warning ?
     * @param bool $disallowSendWarnings
     * @param bool $withAdvices
     * @return bool
     */
    public function hasWarning(bool $disallowSendWarnings = false, bool $withAdvices = false): bool
    {
        return (bool) $this->getWarnings(true, $disallowSendWarnings, $withAdvices);
    }
    
    /**
     * Retourne un warning si besoin (retards, manques, etc.), par ordre d'importance
     * @param bool $firstWarnOnly
     * @param bool $disallowSendWarnings uniquement les alertes qui empêchent un envoi
     * @param bool $withAdvices inclus des conseils non critiques
     * @param bool $html retourner la version HTML
     * @return array|null Texte du warning
     */
    abstract public function getWarnings(bool $firstWarnOnly = false, bool $disallowSendWarnings = false, bool $withAdvices = false, bool $html = false): ?array;
    
    /**
     * Générer un nouveau warning
     * @param string $title
     * @param string $icon
     * @param bool $error
     * @return array
     */
    protected function newWarn(
            string $title, string $icon = 'warning', string $status = null, 
            bool $html = false, ?string $controller = null, ?string $action = null, array $params = [])
    {
        if ($html && $controller) {
            $params[Json::REDIRECT_AUTO_PARAM] = Json::encodedCurrentUri();
            $title = (string) H::link($title, $controller, $action, $params);
        }
        $status && Checkers::checkStatus($status, 'warning');
        return [
            'status' => $status ?? AVH::STATUS_WARNING,
            'icon'   => $icon,
            'title'  => $title,
        ];
    }    
}
