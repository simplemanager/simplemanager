<?php
namespace Sma\Bean;

use Osf\Helper\DateTime as DT;
use Osf\Bean\AbstractBean;
use Osf\Helper\Mysql;
use DateTime;

/**
 * Notification
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 2017
 * @package sma
 * @subpackage bean
 */
class NotificationBean extends AbstractBean
{
    protected $id;
    protected $idAccount;
    protected $icon;
    protected $color;
    protected $link;
    protected $content;
    protected $date;
    protected $dateEnd;
    
    /**
     * @param int|null $id
     * @return $this
     */
    public function setId(?int $id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }
    
    /**
     * @param int $idAccount
     * @return $this
     */
    public function setIdAccount(int $idAccount)
    {
        $this->idAccount = $idAccount;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getIdAccount(): ?int
    {
        return $this->idAccount;
    }
    
    /**
     * @param string|null $icon
     * @return $this
     */
    public function setIcon(?string $icon)
    {
        $this->icon = $icon;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getIcon(): ?string
    {
        return $this->icon;
    }
    
    /**
     * @param string|null $color
     * @return $this
     */
    public function setColor(?string $color)
    {
        $this->color = $color;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getColor(): ?string
    {
        return $this->color;
    }
    
    /**
     * @param string|null $link
     * @return $this
     */
    public function setLink(?string $link)
    {
        $this->link = $link;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getLink(): ?string
    {
        return $this->link;
    }
    
    /**
     * @param string $content
     * @return $this
     */
    public function setContent(string $content)
    {
        $this->content = $content;
        return $this;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }
    
    /**
     * @param string $dateInsert
     * @return $this
     */
    public function setDateInsert(string $dateInsert)
    {
        $this->setDate(Mysql::mysqlToDateTime($dateInsert));
        return $this;
    }
    
    /**
     * @param DateTime $date
     * @return $this
     */
    public function setDate(DateTime $date)
    {
        $this->date = $date;
        return $this;
    }
    
    /**
     * @return DateTime|null
     */
    public function getDate(): ?DateTime
    {
        return $this->date;
    }
    
    /**
     * @param mixed $dateEnd
     * @return $this
     */
    public function setDateEnd($dateEnd)
    {
        $this->dateEnd = DT::buildDate($dateEnd);
        return $this;
    }
    
    /**
     * @return DateTime|null
     */
    public function getDateEnd(): ?DateTime
    {
        return $this->dateEnd;
    }
    
    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            $this->getContent(),
            $this->getLink(),
            $this->getIcon(),
            $this->getColor(),
            $this->getId(),
            date('d/m', $this->getDate()->getTimestamp())
        ];
    }
}
