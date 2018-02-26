<?php
namespace Sma\Db\DbRegistry;

use Zend\Db\ResultSet\AbstractResultSet;
use Osf\Crypt\Crypt;
use Osf\Stream\Text;
use Sma\Session\Identity;
use Sma\Log;
use Sma\Config;
use Exception;
use Osf\Exception\DisplayedException;
use ZipArchive;
use DB;

/**
 * Requêtes liées aux comptes
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 2017
 * @package sma
 * @subpackage db
 */
trait AccountManagement
{
    /**
     * Get user info to put in session
     * @param int $idAccount
     */
    public static function getUserInfo($idAccount = null, array $data = null):array
    {
        $idAccount = (int) ($idAccount ? $idAccount : ($data['id'] ?: Identity::getIdAccount()));
        $data = $data ?: DB::getAccountTable()->find($idAccount)->toArray();
        $contact = DB::getAddressContactTable()->select(['is_account' => $idAccount])->current()->toArray();
        $company = $contact && $contact['id_company'] 
                 ? DB::getCompanyTable()->find($contact['id_company']) 
                 : null;
        if ($company && $company['id_address']) {
            $company['address'] = DB::getAddressTable()->find($company['id_address'])->toArray();
        }
        $logoColor = $company && $company['id_logo'] 
                   ? DB::getImageTable()->find($company['id_logo'])->getColor() 
                   : null;
        
        $defaultParams = (new Config())->getValues();
        $params = $data['bean'] ? unserialize($data['bean']) : null;
        $params = is_array($params) ? array_replace_recursive($defaultParams, $params) : $defaultParams;
        
        $data[Identity::SECTION_CONTACT] = $contact;
        $data[Identity::SECTION_COMPANY] = $company ? $company->toArray() : null;
        $data[Identity::SECTION_PARAMS] = $params;
        $data[Identity::SECTION_LOGO_COLOR] = $logoColor ? '#' . $logoColor : null;
        unset($data['bean']);
        
        return $data;
    }
    
    /**
     * Check if it is the current user account
     * @param int $idAccount
     * @param string $msg context message
     * @throws Exception
     */
    public static function checkIdAccount($idAccount, string $msg = '')
    {
        if ($idAccount !== Identity::getIdAccount()) {
            $msg = 'Account ' . Identity::getIdAccount() . ' wants to alter account ' . $idAccount;
            $msg .= $msg ? ': ' . $msg : '';
            Log::hack($msg);
            throw new Exception($msg);
        }
    }
    
    public static function backupAccount(int $idAccount, $backupDir = null)
    {
        // Paramètres
        $params = [
            'version' => 1,
            'account' => $idAccount,
            'time'    => time(),
            'server'  => gethostname()
        ];
        $counts = [];
        
        // Compte
        $rs = DB::getAccountTable()->select(['id' => $idAccount]);
        if (!$rs->count()) {
            Log::error('Tentative de backup du compte inexistant ' . $idAccount);
            throw new DisplayedException(sprintf(__("Aucun compte n°%s"), $idAccount));
        }
        
        /* @var $accountInfo \Sma\Db\AccountRow */
        $accountInfo = $rs->current();

        // Répertoire de travail + dump du compte
        $workDir = sys_get_temp_dir() . '/' . Crypt::getRandomHash();
        mkdir($workDir, 0750);
        $counts['account'] = self::dumpFromQuery($rs, $workDir . '/account.dump');
        
        /* @var $table \Osf\Db\Table\AbstractTableGateway */
        foreach (self::getTables() as $tableName => $table) {
            if ($tableName === 'account') {
                continue;
            }
            $rs = DB::getTable($tableName)->select(['id_account' => $idAccount]);
            $counts[$tableName] = self::dumpFromQuery($rs, $workDir . '/' . $tableName . '.dump');
        }
        
        // Paramètres
        $fileName = date('Ymd-His-') . sprintf("%'06s", $idAccount) 
                . '-' . Text::toLower($accountInfo->getEmail()) 
                . '-' . Text::getAlpha($accountInfo->getFirstname()) 
                . '-' . Text::getAlpha($accountInfo->getLastname());
        $params['key'] = $fileName;
        $comment = json_encode($params);
        $params['counts'] = $counts;
        file_put_contents($workDir . '/info.txt', print_r($params, true));
        
        // Finalisation
        $defaultBackupDir = self::getBackupDir() . '/accounts/' . $idAccount;
        if (!is_dir($defaultBackupDir)) {
            mkdir($defaultBackupDir, 0755, true);
        }
        $backupDir = $backupDir ?? $defaultBackupDir;
        $filePath = $backupDir . '/' . $fileName . '.zip';
        $zip = new ZipArchive();
        if ($zip->open($filePath, ZipArchive::CREATE) !== true) {
            throw new Exception('Unable to open file ' . $filePath);
        }
        $zip->addGlob($workDir . '/*', 0, ['remove_all_path' => true, 'add_path' => $fileName . '/']);
        $zip->setArchiveComment($comment);
        if (!$zip->close()) {
            throw new DisplayedException(__("Enregistrement du fichier de sauvegarde impossible. Veuillez nous excuser pour la gêne occasionnée."));
        }
        
        // Lien symbolique dans le répertoire utilisateur si sauvegarde générale
        if ($defaultBackupDir !== $backupDir) {
            symlink($filePath, $defaultBackupDir . '/' . $fileName . '.zip');
        }
        
        // Nettoyage
        foreach (glob($workDir . '/*') as $dumpFile) {
            unlink($dumpFile);
        }
        rmdir($workDir);
        
        return $filePath;
    }
    
    /**
     * @param string $backupFile
     * @param int $idAccount
     * @return int idAccount
     * @throws Exception
     */
    public static function recoverAccount(string $backupFile, int $idAccount = null)
    {
        // Vérifications et ouverture du fichier dump
        $zip = self::openDump($backupFile, $idAccount);
        $comment = json_decode($zip->getArchiveComment(), true);
        $idAccount = $idAccount ?? $comment['account'];
        
        // Mettre ici les conversions entre les versions
        
        // Récupération...
        //$connection = DB::getDbAdapter()->getDriver()->getConnection();
        //$connection->beginTransaction();
        try {
            
            // Delete
            self::truncateAccountWithoutTransaction($idAccount);
            //$connection->commit();
            //$connection->beginTransaction();
            
            // Insert
            /* @var $table \Osf\Db\Table\AbstractTableGateway */
            foreach (array_reverse(self::getTables(), true) as $tableName => $table) {
                // echo 'A: ' . $tableName . "\n";
                $data = unserialize($zip->getFromName($comment['key'] . '/' . $tableName . '.dump'));
                if ($data) {
                    foreach ($data as $row) {
                        // echo '.';
                        $table->insert($row);
                    }
                }
            }
            
            // Renvoit l'id du compte
            return $idAccount;
            
            //$connection->commit();
        } catch (Exception $e) {
            //$connection->rollback();
            Log::error("Erreur à la restauration d'un compte : " . $e->getMessage(), 'RESTORE', $e);
            throw $e;
        }
    }
    
    protected static function getTables()
    {
        // Ignoré : form_stat, log, search, search_tag, ticket, ticket_log
        return [
            'document_event'   => DB::getDocumentEventTable(),
            'letter_template'  => DB::getLetterTemplateTable(),
            'form'             => DB::getFormTable(),
            'ticket_poll'      => DB::getTicketPollTable(),
            'basket'           => DB::getBasketTable(),
            'sequence'         => DB::getSequenceTable(),
            'product'          => DB::getProductTable(),
            'event'            => DB::getEventTable(),
            'invoice'          => DB::getInvoiceTable(),
            'document_history' => DB::getDocumentHistoryTable(),
            'document'         => DB::getDocumentTable(),
            'company'          => DB::getCompanyTable(),
            'contact'          => DB::getContactTable(),
            'address'          => DB::getAddressTable(),
            'image'            => DB::getImageTable(),
            'payment'          => DB::getPaymentTable(),
            'account'          => DB::getAccountTable(),
        ];
    }
    
    public static function truncateAccount($idAccount)
    {
        $connection = DB::getDbAdapter()->getDriver()->getConnection();
        $connection->beginTransaction();
        try {
            self::truncateAccountWithoutTransaction($idAccount);
            $connection->commit();
        } catch (Exception $e) {
            $connection->rollback();
            Log::error("Erreur à la suppression d'un compte : " . $e->getMessage(), 'TRUNCATE', $e);
            throw $e;
        }
    }
    
    protected static function truncateAccountWithoutTransaction($idAccount)
    {
        $tables = self::getTables();
        
        /* @var $table \Osf\Db\Table\AbstractTableGateway */
        foreach ($tables as $tableName => $table) {
            $row = $tableName === 'account' ? 'id' : 'id_account'; 
            $table->delete([$row => $idAccount]);
        }
        
        // Nettoyage des données de recherche
        DB::getSearchTagTable()->delete(['id_account' => $idAccount]);
        DB::getSearchTable()->delete(['id_account' => $idAccount]);
    }
    
    /**
     * @param string $backupFile
     * @param int $idAccount
     * @return ZipArchive
     * @throws DisplayedException
     * @task faire une vérification sur le checksum du fichier
     */
    protected static function openDump(string $backupFile, int $idAccount = null)
    {
        $zip = new ZipArchive();
        $opened = $zip->open($backupFile, ZipArchive::CHECKCONS);
        if ($opened !== true) {
            Log::error('Fichier de backup illisible', 'BACKUP', ['errno' => $opened, 'file' => $backupFile, 'account' => $idAccount]);
            throw new DisplayedException(__("Impossible de lire le fichier de sauvegarde"));
        }
        $comment = json_decode($zip->getArchiveComment(), true);
        if (!$comment || !is_array($comment) || array_keys($comment) !== ['version', 'account', 'time', 'server', 'key'] ||
            !is_int($comment['version']) || !is_int($comment['account']) || !is_int($comment['time']) || 
            !is_string($comment['server']) || !is_string($comment['key'])) {
            Log::error('Commentaire de backup inconsistant', 'BACKUP', ['file' => $backupFile, 'account' => $idAccount, 'comment' => $comment]);
            throw new DisplayedException(__("Fichier de sauvegarde inconsistant"));
        }
        $account = unserialize($zip->getFromName($comment['key'] . '/account.dump'));
        if (!is_array($account) || count($account) !== 1 || !isset($account[0]['id']) || $account[0]['id'] !== $comment['account']) {
            Log::error('Identifiant de compte inconsistant', 'BACKUP', ['file' => $backupFile, 'account' => $idAccount, 'comment' => $comment, 'account_row' => $account]);
            throw new DisplayedException(__("Fichier de sauvegarde inconsistant"));
        }
        return $zip;
    }
    
    public static function dumpFromQuery(AbstractResultSet $query, string $file)
    {
        file_put_contents($file, serialize($query->toArray()));
        return $query->count();
    }
    
    public static function getBackupDir()
    {
        return APP_PATH . '/var/backup';
    }
}
