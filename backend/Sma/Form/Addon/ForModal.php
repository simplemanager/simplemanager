<?php
namespace Sma\Form\Addon;

use Sma\Container;

/**
 * Gestion de l'affichage du formulaire dans un modal
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 2017
 * @package sma
 * @subpackage form
 */
trait ForModal
{
    // null = auto détection
    protected $forModal = null;
    
    protected function autoDetectModal()
    {
        $this->forModal = Container::getRequest()->getParam('for') === 'modal';
    }
    
    public function isInModal(): bool
    {
        if ($this->forModal === null) {
            $this->autoDetectModal();
        }
        return $this->forModal;
    }
}
