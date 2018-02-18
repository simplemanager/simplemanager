<?php
namespace Sma\Bean\Addon;

use Osf\Exception\ArchException;

/**
 * Gestion des exemples (dossier Example)
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 2017
 * @package sma
 * @subpackage bean
 */
trait Example
{
    /**
     * @param int $exampleNo
     * @return $this
     */
    protected function loadExample(int $exampleNo)
    {
        $method = 'loadExample' . $exampleNo;
        if (!method_exists($this, $method)) {
            throw new ArchException('Example [' . $exampleNo . '] not found');
        }
        return $this->$method();
    }
}
