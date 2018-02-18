<?php
namespace Sma\Bean\Addon;

/**
 * Template lié au bean
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 2017
 * @package sma
 * @subpackage bean
 */
trait Template
{
    protected $template;
    
    /**
     * @param string|null $template
     * @return $this
     */
    public function setTemplate($template)
    {
        $this->template = $template === null ? null : (string) $template;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getTemplate()
    {
        return $this->template;
    }
}
