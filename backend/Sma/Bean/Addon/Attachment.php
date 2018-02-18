<?php
namespace Sma\Bean\Addon;

/**
 * Id du document à attacher dans la table des documents
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 2017
 * @package sma
 * @subpackage bean
 */
trait Attachment
{
    protected $attachmentId;
    
    /**
     * @param int|null $attachmentId
     * @return $this
     */
    public function setAttachmentId($attachmentId): self
    {
        $this->attachmentId = $attachmentId ? (int) $attachmentId : null;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getAttachmentId(): ?int
    {
        return $this->attachmentId;
    }
}
