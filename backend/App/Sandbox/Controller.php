<?php
namespace App\Sandbox;

use Sma\Controller\Json as JsonAction;
use App\Account\Form\FormLogin;
use App\Sandbox\Form\FormTest;
use DB, L;

/**
 * Espace administration
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 16 nov. 2013
 * @package company
 * @subpackage controllers
 */
class Controller extends JsonAction
{
    const TMP_SERIALIZED_FILE = '/tmp/serialized.txt';
    
    public function init()
    {
        $this->layout()->setPageTitle('Tests techniques');
    }
    
    public function indexAction()
    {
    }
    
    public function uiAction()
    {
        $loginForm = new FormLogin();
        $testForm = new FormTest();
        if ($testForm->isPostedAndValid()) {
            echo "OK";
        }
        return ['loginForm' => $loginForm, 
                'testForm'  => $testForm];
    }
    
    public function formsAction()
    {
        L::setPageTitle('Form generation from DB');
        $tables = glob(__DIR__ . '/../../Sma/Db/*Table.php');
        $forms = [];
        foreach ($tables as $file) {
            $table = basename($file, '.php');
            $getter = 'get' . $table;
            $form = DB::$getter()->getForm()
                    ->setPrefix(DB::$getter()->getTableName())
                    ->displayLabels(false)
                    ->setTitle(DB::$getter()->getTableName(), 'pencil');
            if ($form->isPostedAndValid()) {
                \H::layout()->addAlert(null, 'Ok good !', L::STATUS_SUCCESS);
            }
            $forms[] = $form;
        }
        
        return ['forms' => $forms];
    }
    
    public function kitchenAction()
    {
    }
}
