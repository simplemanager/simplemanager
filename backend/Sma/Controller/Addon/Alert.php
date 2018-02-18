<?php
namespace Sma\Controller\Addon;

use App\Common\Container;

/**
 * Alerte JSON via FlashMessenger
 * 
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 2017
 * @package sma
 * @subpackage controller
 */
trait Alert
{
    /**
     * Register a message to display on top of the page
     * @param string $title
     * @param string $message
     * @param bool $closable
     * @return $this
     */
    public function alertInfo(?string $title = null, ?string $message = null, bool $closable = true)
    {
        Container::getFlashMessenger()->msgInfo($message, $title, $closable);
        return $this;
    }
    
    /**
     * Register a message to display on top of the page
     * @param string $title
     * @param string $message
     * @param bool $closable
     * @return $this
     */
    public function alertDanger(?string $title = null, ?string $message = null, bool $closable = true)
    {
        Container::getFlashMessenger()->msgDanger($message, $title, $closable);
        return $this;
    }
    
    /**
     * Register a message to display on top of the page
     * @param string $title
     * @param string $message
     * @param bool $closable
     * @return $this
     */
    public function alertWarning(?string $title = null, ?string $message = null, bool $closable = true)
    {
        Container::getFlashMessenger()->msgWarning($message, $title, $closable);
        return $this;
    }
    
    /**
     * Register a message to display on top of the page
     * @param string $title
     * @param string $message
     * @param bool $closable
     * @return $this
     */
    public function alertSuccess(?string $title = null, ?string $message = null, bool $closable = true)
    {
        Container::getFlashMessenger()->msgSuccess($message, $title, $closable);
        return $this;
    }
}
