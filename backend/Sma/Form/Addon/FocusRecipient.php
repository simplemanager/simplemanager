<?php
namespace Sma\Form\Addon;

use Osf\View\Helper\Bootstrap\Tools\Checkers;
use Osf\Form\Element\ElementSelect;
use Osf\Exception\ArchException;
use App\Common\Container as C;

/**
 * Gestion du focus sur le destinataire des formulaires de création de documents
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 2017
 * @package sma
 * @subpackage form
 */
trait FocusRecipient
{
    protected $focusRecipient = false;
    
    /**
     * @param bool $focusRecipient
     * @return $this
     */
    public function setFocusRecipient($focusRecipient = true)
    {
        $this->focusRecipient = (bool) $focusRecipient;
        return $this;
    }

    /**
     * @return bool
     */
    public function getFocusRecipient(): bool
    {
        return $this->focusRecipient;
    }
    
    /**
     * Création du script de focus sur l'élément passé en paramètre
     * @staticvar boolean $registered
     * @param ElementSelect $elt
     * @return $this
     * @throws ArchException
     */
    public function registerFocusRecipient(ElementSelect $elt)
    {
        static $registered = false;
        
        if ($registered) {
            Checkers::notice('Element already registered');
        }
        if (!$elt->getId()) {
            throw new ArchException('Id is required for focused autocomplete element');
        }
        if ($this->focusRecipient) {
            C::getJsonRequest()->appendScripts("\$(document).ready(function(){\$('#" . $elt->getId() . "')[0].selectize.focus();});");
        }
        $registered = true;
        return $this;
    }
}
