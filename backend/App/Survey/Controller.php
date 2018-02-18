<?php
namespace App\Survey;

use Sma\Controller\Json as JsonAction;
use Sma\Session\Identity;
use App\Survey\Model\SurveyConfig;
use Sma\Form\ConfigForm;
use DB;

/**
 * Sondages
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
    /**
     * Sondage pour les testeurs
     */
    public function indexAction()
    {
        $configForm = new ConfigForm(new SurveyConfig());
        $surveyConfigClass = $configForm->getConfig('class');
        if ($configForm->dispatch($this)) {
            DB::getFormTable()->updateForm($surveyConfigClass, $configForm->getForm()->getValues());
        } else {
            $form = $configForm->getForm();
            $values = DB::getFormTable()->getFormFromClass($surveyConfigClass);
            if (is_array($values) && is_array($values['form_values'])) {
                $form->hydrate($values['form_values'], null, true, true);
                $this->alertInfo(__("Vous avez déjà répondu"), __("Cependant vous pouvez modifier vos réponses si vous le souhaitez."));
            } else if (!$form->isPosted()) {
                $this->alertInfo(__("Un petit sondage"), sprintf(__("Prenez 10 minutes, %s, pour répondre à ce questionnaire suite à vos manipulations. Dites-nous en particulier ce qui est important pour vous."), Identity::get('firstname')));
            }
            return ['form' => $form, 'posted' => $form->isPosted()];
        }
        return [];
    }
    
    public function resultAction()
    {
        $surveyConfigClass = '\App\Survey\Model\SurveyConfig';
        $this->pageTitle(__("Résultats du sondage"));
        $users = DB::getFormTable()->getFormParticipants($surveyConfigClass);
        $idAccount = $this->getParam('uid');
        $stats = is_numeric($idAccount)
                ? DB::getFormStatsTable()->buildStats($surveyConfigClass, $idAccount) 
                : DB::getFormStatsTable()->getStats($surveyConfigClass);
        return ['users' => $users, 'stats' => $stats, 'user' => $idAccount];
    }
}
