<?php
namespace Sma\Db;

use Sma\Db\Generated\AbstractCompanyRow;
use H;

/**
 * Row model for table company
 *
 * Use this class to complete AbstractCompanyRow
 *
 * @version 1.0
 * @author Guillaume Ponçon - OpenStates Framework PHP Generator
 * @since OSF 2.0
 * @package osf
 * @subpackage generated
 */
class CompanyRow extends AbstractCompanyRow
{

    /**
     * Put filters, validators and data cleaners here
     */
    public function set($field, $value)
    {
        return parent::set($field, $value);
    }

    /**
     * Put filters here
     */
    public function get($field)
    {
        return parent::get($field);
    }
    
    /**
     * Construit l'url pour se connecter à l'espace guest de l'entreprise
     * @return string
     */
    public function buildLoginUrl(): string
    {
        return (string) H::baseUrl(H::url('guest', 'login', ['k' => $this->getHash()]), true);
    }
}