<?php
namespace Sma\Controller\Cli;

use Osf\Controller\Cli\AbstractDeferredAction;
use Sma\Log;
use DB, C;

/**
 * Basket updater (A REVISER !)
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 2017
 * @package sma
 * @subpackage controller
 */
class DeferredBasketProcessing extends AbstractDeferredAction
{
    public function getName(): string
    {
        return "Basket updates [" . C::getRedis()->lLen('BASKET') . '] command(s)';
    }

    public static function registerAction(int $idAccount, int $idInvoice, array $row = null)
    {
        return C::getRedis()->lPush('BASKET', [
            'id_invoice' => $idInvoice,
            'id_account' => $idAccount,
            'row'        => $row
        ]);
    }
    
    /**
     * TODO
     * @return bool
     * @throws ArchException
     */
    public function execute(): bool
    {
        // Nettoyages
        $toInsert = [];
        $toDelete = [];
        while($data = C::getRedis()->lPop('BASKET')) {
            if (isset($data['row'])) {
                if ($data['id_account'] !== $data['row']['id_account']) {
                    $msg = 'Inconsistant data in basket postprocessing';
                    Log::hack($msg, $data);
                    throw new ArchException($msg);
                }
                $toInsert[$data['id_invoice']] = $data['row'];
                unset($toDelete[$data['id']]);
            } else {
                $toDelete[$data['id_invoice']] = $data['id_account'];
                unset($toInsert[$data['id']]);
            }
        }
        
        // Suppressions
        foreach ($toDelete as $idInvoice => $idAccount) {
            try {
                DB::getBasketTable()->delete([
                    'id_account' => $idAccount, 
                    'id_invoice' => $idInvoice
                ]);
            } catch (\Exception $e) {
                $this->error($e->getMessage());
                file_put_contents(sys_get_temp_dir() . '/basket_delete_error_' . md5($row), print_r($e, true) . "\n\n" . $row);
            }
        }
        
        // Insertions
        foreach ($toInsert as $row) {
            try {
                DB::getBasketTable()->insert($row);
            } catch (\Exception $e) {
                $this->error($e->getMessage());
                file_put_contents(sys_get_temp_dir() . '/basket_insert_error_' . md5($row), print_r($e, true) . "\n\n" . $row);
            }
        }
        
        return true;
    }
}