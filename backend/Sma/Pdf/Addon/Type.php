<?php
namespace Sma\Pdf\Addon;

use Osf\Exception\ArchException;

/**
 * Type de document : lettre, facture, formulaire, ...
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 2017
 * @package osf
 * @subpackage pdf
 */
trait Type
{
    protected $type;
    
    /**
     * @param $type string
     * @return $this
     */
    public function setType(string $type): self
    {
        if (!in_array($type, ['form', 'invoice', 'quote', 'order', 'letter'])) {
            throw new ArchException('Bad document type [' . $type . ']');
        }
        $this->type = $type;
        return $this;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }
}
