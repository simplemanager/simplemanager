<?php
namespace Sma\Bean;

use Zend\Db\Adapter\Driver\ResultInterface;
use Osf\Bean\AbstractBean;
use Iterator;

/**
 * Simule une collection de beans à partir d'un résultat en base
 * 
 * L'itération se fait en liaison avec la BD pour y gagner en performances et économiser des ressources
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 2017
 * @package sma
 * @subpackage bean
 */
class BeanCollection implements Iterator
{
    /**
     * @var ResultInterface
     */
    protected $queryResult;
    
    /**
     * @var AbstractBean
     */
    protected $baseBean;

    public function __construct(ResultInterface $queryResult, AbstractBean $baseBean = null)
    {
        $this->queryResult = $queryResult;
        $this->baseBean = $baseBean;
    }

    public function current()
    {
        if ($this->baseBean) {
            $bean = clone $this->baseBean;
            $bean->populate($this->queryResult->current());
            return $bean;
        }
        $row = $this->queryResult->current();
        if (isset($row['bean']) && $row['bean']) {
            return unserialize($row['bean']);
        }
        return null;
    }

    public function key()
    {
        return $this->queryResult->key();
    }

    public function next()
    {
        return $this->queryResult->next();
    }

    public function rewind()
    {
        return $this->queryResult->rewind();
    }

    public function valid()
    {
        return $this->queryResult->valid();
    }
}