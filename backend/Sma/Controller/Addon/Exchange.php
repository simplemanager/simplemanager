<?php
namespace Sma\Controller\Addon;

use Sma\Db\DbRegistry\ExchangeManagement;
use Sma\Db\DbRegistry\Exchangeable;
use Osf\Office\Spreadsheet;

/**
 * Code commun aux opérations d'import / export
 * 
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 2017
 * @package sma
 * @subpackage controller
 */
trait Exchange
{
    /**
     * Action d'export commune
     * @param Exchangeable $table
     * @param string|null $title
     * @param array $settings
     * @return $this
     */
    protected function export(Exchangeable $table, ?string $title = null, ?array $settings = null)
    {
        $uri = filter_input(INPUT_SERVER, 'REQUEST_URI');
        $format = substr($uri, strpos($uri, '.') + 1);
        $doc = Spreadsheet::newSpreadsheet();
        $title && $doc->getActiveSheet()->setTitle($title);
        $file = ExchangeManagement::export($table, $format, $settings ?? [], $doc);
        $this->readfile($file, $format, preg_replace('#^.*/([^/]+)$#', '$1', $uri));
        return $this;
    }
}