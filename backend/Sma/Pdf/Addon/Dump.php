<?php
namespace Sma\Pdf\Addon;

/**
 * Dump binaire d'un document
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 2017
 * @package osf
 * @subpackage pdf
 */
trait Dump
{
    protected $dump = '';
    
    /**
     * @param $dump string|null
     * @return $this
     */
    public function setDump($dump)
    {
        $this->dump = (string) $dump;
        return $this;
    }

    /**
     * @return string
     */
    public function getDump(): string
    {
        return $this->dump;
    }

    /**
     * @return string|null
     */
    public function getHash()
    {
        return $this->hash;
    }
}
