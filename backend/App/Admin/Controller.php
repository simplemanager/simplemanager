<?php
namespace App\Admin;

use Osf\Exception\ArchException;
use Sma\Controller\Json as JsonAction;
use Sma\Bean\NotificationBean;
use Sma\Db\DbRegistry;
use Sma\Log;
use App\Admin\Model\AccountDbManager as ADM;
use App\Admin\Form\FormNotification;
use H, DB;

/**
 * Home page
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 13 sept. 2013
 * @package company
 * @subpackage controllers
 */
class Controller extends JsonAction
{
    const ACTION_STATUS_ENABLE  = 'senabled';
    const ACTION_STATUS_DISABLE = 'sdisabled';
    const ACTION_STATUS_DRAFT   = 'sdraft';
    const ACTION_STATUS_SUSPEND = 'ssuspended';
    const ACTION_SAVE           = 'save';
    const ACTION_DELETE         = 'delete';
    
    const ACTIONS = [
        self::ACTION_STATUS_ENABLE,
        self::ACTION_STATUS_DISABLE,
        self::ACTION_STATUS_DRAFT,
        self::ACTION_STATUS_SUSPEND,
        self::ACTION_SAVE,
        self::ACTION_DELETE
    ];
    
    const STATUS_LABELS = [
        'enabled' => 'activé',
        'disabled' => 'désactivé',
        'suspended' => 'suspendu',
        'draft' => 'brouillon',
    ];
    
    public function init()
    {
        H::layout()->setPageTitle('Administration')
                   ->addBreadcrumbLink('Admin', H::url('admin'));
    }
    
    public function indexAction()
    {
        //Container::getViewHelper()->addLink(Container::getViewHelper()->link('Bonjour', 'account'), 'Général');
        //return array('description' => Vendor::getRedis()->get('test'));
        $this->disableView();
        echo H::msg('Accueil administration (à venir)');
    }
    
    public function boardAction()
    {
        $this->executeActions();
        return [];
    }
    
    public function appsAction()
    {
        $this->disableView();
        echo H::msg('Gestion des fonctionnalités (à venir). Cf. ' . H::link('play', 'play'));
    }
    
    public function logsAction()
    {
        $form = new Form\FormFilterLog();
        $form->isPostedAndValid();
        $settings = $form->getValues();
        $settings['trace'] = (int) $this->getParam('trace');
        $data = DB::getLogTable()->getLogForTable($settings);
        return ['data' => $data, 'formFilter' => $form];
    }
    
    public function logAction()
    {
        $id = (int) $this->getParam('id');
        $log = DB::getLogTable()->find($id);
        $account = $log && $log->getIdAccount() ? DB::getAccountTable()->find($log->getIdAccount()) : null;
        return ['log' => $log, 'account' => $account];
    }
    
    public function actionsAction()
    {
    }
    
    public function notifAction()
    {
        $form = new FormNotification();
        if ($form->isPostedAndValid()) {
            $values = $form->getValues();
            $allVals = $values;
            unset($values['account_ids']);
            unset($values['broadcast']);
            $notification = (new NotificationBean())->populate($values);
            DbRegistry::notificationPushMultiple($notification, (bool) $allVals['broadcast'], $allVals['account_ids']);
            $this->alertSuccess(__("Notifications envoyées"));
        }
        return ['form' => $form, 'posted' => $form->isPosted()];
    }
    
    public function securityAction()
    {
        $this->todo();
    }
    
    /**
     * Execute des actions demandées par l'administrateur
     * @throws ArchException
     */
    protected function executeActions()
    {
        $idAccount = (int) $this->getParam('touch');
        $action = (string) $this->getParam('a');
        if (!$idAccount && !$action) {
            return false;
        }
        if (!$idAccount || !in_array($action, self::ACTIONS)) {
            Log::error(__("Pas d'id account ou mauvaise action"), 'ADMIN', [$idAccount, $action]);
            throw new ArchException('No account or bad action');
        }
        switch ($action) {
            
            // Modification de statut
            case self::ACTION_STATUS_ENABLE : 
            case self::ACTION_STATUS_DISABLE : 
            case self::ACTION_STATUS_DRAFT : 
            case self::ACTION_STATUS_SUSPEND :
                $status = substr($action, 1);
                DB::getAccountTable()->find($idAccount)->setStatus($status)->save();
                // $this->alertInfo(sprintf(__("Compte %d défini comme %s."), $idAccount, self::STATUS_LABELS[$status]));
                break;
            
            // Sauvegarde
            case self::ACTION_SAVE : 
                $file = DbRegistry::backupAccount($idAccount);
                $this->alertInfo(
                        sprintf(__("Compte %d sauvegardé"), $idAccount), 
                        sprintf(__("Sauvegarde effectuée dans %s"), $file)
                    );
                break;
            
            // Suppression
            case self::ACTION_DELETE : 
                $file = DbRegistry::backupAccount($idAccount);
                DbRegistry::truncateAccount($idAccount);
                $this->alertInfo(
                        sprintf(__("Compte %d supprimé"), $idAccount), 
                        sprintf(__("Sauvegarde effectuée avant suppression dans %s"), $file)
                    );
                break;
            
            // Action inconnue
            default : 
                $this->alertDanger(sprintf(__("Action %s inconnue"), $action));
        }
        return true;
    }
    
    /**
     * Informations sur un compte
     */
    public function infoAction()
    {
        $info = DB::getAccountTable()->buildInfo((int) $this->getParam('id'));
        return ['info' => $info];
    }
    
    /**
     * Autocomplete des accounts
     */
    public function acAction()
    {
        $this->json(ADM::searchForAutocomplete($this->getParam('account')));
    }
}
