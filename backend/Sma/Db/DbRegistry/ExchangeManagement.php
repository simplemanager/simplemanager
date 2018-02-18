<?php
namespace Sma\Db\DbRegistry;

use PhpOffice\PhpSpreadsheet\Spreadsheet as PssSpreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Osf\Exception\ArchException;
use Osf\Office\Spreadsheet;
use Sma\Bean\ExchangeableBeanInterface;

/**
 * Import / Export
 * 
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 2017
 * @package sma
 * @subpackage db
 */
class ExchangeManagement
{
    const FORMAT_XLS  = 'xls';
    const FORMAT_XLSX = 'xlsx';
    const FORMAT_ODT  = 'ods';
    const FORMAT_CSV  = 'csv';
    
    const FORMATS = [
        self::FORMAT_XLS,
        self::FORMAT_XLSX,
        self::FORMAT_ODT,
        self::FORMAT_CSV,
    ];
    
    /**
     * Exporte les données demandées dans un document
     * @param \Sma\Db\DbRegistry\Exchangeable $table
     * @param string $format
     * @param array $settings
     * @param string|null $file
     * @return string nom du fichier
     * @throws ArchException
     */
    public static function export(Exchangeable $table, string $format, array $settings = [], PssSpreadsheet $doc = null, ?string $file = null): string
    {
        self::checkFormat($format);
        
        $doc = $doc ?? Spreadsheet::newSpreadsheet();
        $sheet = $doc->getActiveSheet();
        $first = true;
        $line = 2;
        foreach ($table->getBeans($settings) as $bean) {
            if (!($bean instanceof ExchangeableBeanInterface)) {
                throw new ArchException('Not an exchangeable bean');
            } 
            if ($first) {
                $sheet->fromArray(array_values($bean::exportLegend()));
                $first = false;
            }
            $sheet->fromArray($bean->exportToArray(), null, 'A' . $line++);
        }
        
        $file = $file ?? tempnam(sys_get_temp_dir(), 'ex-export-');
        $writer = IOFactory::createWriter($doc, ucfirst($format));
        $writer->save($file);
        return $file;
    }
    
    /**
     * @param string $format
     * @return void
     * @throws ArchException
     */
    public static function checkFormat(string $format): void
    {
        if (!in_array($format, self::FORMATS)) {
            throw new ArchException('Unknown format [' . $format . ']');
        }
    }
}
