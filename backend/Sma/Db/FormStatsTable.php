<?php
namespace Sma\Db;

use Osf\Config\OsfConfig as Config;
use Sma\Db\Generated\AbstractFormStatsTable;
use DB;

/**
 * Table model for table form_stats
 *
 * Use this class to complete AbstractFormStatsTable
 *
 * @version 1.0
 * @author Guillaume Ponçon - OpenStates Framework PHP Generator
 * @since OSF 2.0
 * @package osf
 * @subpackage generated
 */
class FormStatsTable extends AbstractFormStatsTable
{
    public function buildStats(?string $className = null, ?int $accountId = null)
    {
        $retVal = 0;
        $where = $className ? ['class' => $className] : [];
        if ($accountId) {
            $where['id_account'] = $accountId;
        }
        $forms = DB::getFormTable()->select($where);
        $values = [];
        
        // Construction des données
        /* @var $form \Sma\Db\FormRow */
        $newsLetterIds = [];
        foreach ($forms as $form) {
            $retVal++;
            $formValues = unserialize($form->getFormValues());
            if ($formValues['divers']['newsletter']) {
                $newsLetterIds[] = $form->getIdAccount();
            }
            $this->appendRow($this->getFormConfig($form->getClass()), $values, $formValues);
        }
        
        // S'il y a un account id, on retourne le résultat sans mettre ne base.
        if ($accountId) {
            return $className ? $values[$className] : $values;
        }
        
        // Envoi en base des statistiques calculées
        foreach ($values as $class => $classValues) {
            $sVals = serialize($classValues);
            $row = $this->select(['class' => $class])->current();
            if ($row instanceof FormStatsRow) {
                $row->setFormValues($sVals)->save();
            } else {
                $this->insert(['class' => $class, 'form_values' => $sVals]);
            }
        }
        
        /* @var $account \Sma\Db\AccountRow */
        $accounts = DB::getAccountTable()->buildSelect(['id in (' . implode(',', $newsLetterIds) . ')'])->execute();
        $mails = [];
        foreach ($accounts as $account) {
            $mails[] = '"' . $account->getEmail() . '","' . $account->getFirstname() . ' ' . $account->getLastname() . '"';
        }
        file_put_contents(sys_get_temp_dir() . '/sma-nl-mails.csv', "Email,Name\n" . implode("\n", $mails) . "\n");
        
        return $retVal;
    }
    
    /**
     * @staticvar array $configs
     * @param string $formConfigClass
     * @return \Osf\Config\OsfConfig
     * @throws ArchException
     */
    protected function getFormConfig($formConfigClass)
    {
        static $configs = [];
        
        if (!isset($configs[$formConfigClass])) {
            if (!@class_exists($formConfigClass)) {
                throw new ArchException('Class [' . $formConfigClass . '] not found');
            }
            $configs[$formConfigClass] = new $formConfigClass();
        }
        return $configs[$formConfigClass];
    }
    
    protected function appendRow(Config $formConfig, &$values, $formValues)
    {
        $config = $formConfig->getConfig();
        $formConfigClass = $config['config']['class'];
        foreach ($config['forms'] as $formKey => $fields) {
            if (!isset($values[$formConfigClass][$formKey])) {
                $values[$formConfigClass][$formKey]['title'] = $fields['title'];
            }
            foreach ($fields['options'] as $fieldKey => $params) {
                if (!isset($values[$formConfigClass][$formKey]['options'][$fieldKey])) {
                    $values[$formConfigClass][$formKey]['options'][$fieldKey] = $params;
                }
                $currentValues = &$values[$formConfigClass][$formKey]['options'][$fieldKey];
                if (!isset($currentValues['values'])) {
                    $currentValues['values'] = [];
                }
                if (!isset($formValues[$formKey][$fieldKey])) {
                    continue;
                }
                switch ($params['element']) {
                    case 'select' : 
                        if (isset($params['multiple']) && $params['multiple']) {
                            foreach ($formValues[$formKey][$fieldKey] as $formValue) {
                                $this->increment($currentValues, $formValue);
                            }
                        } else {
                            $this->increment($currentValues, $formValues[$formKey][$fieldKey]);
                        }
                        break;
                    case 'checkbox' : 
                        $this->increment($currentValues, $formValues[$formKey][$fieldKey]);
                        break;
                    case 'textarea' : 
                    case 'input' : 
                        $formValue = trim($formValues[$formKey][$fieldKey]);
                        if ($formValue !== '') {
                            $currentValues['values'][] = $formValue;
                        }
                }
            } 
        }
    }
    
    /**
     * @param int $baseArray
     * @param type $key
     * @return $this
     */
    protected function increment(&$baseArray, $key)
    {
        if ($key === null || $key === '') {
            return $this;
        }
        if (isset($baseArray['values'][$key])) {
            $baseArray['values'][$key]++;
        } else {
            $baseArray['values'][$key] = 1;
        }
        return $this;
    }
    
    /**
     * @param string $class
     * @return null|array
     */
    public function getStats(string $class)
    {
        $row = $this->select(['class' => $class])->current();
        if ($row instanceof FormStatsRow) {
            return unserialize($row->getFormValues());
        }
        return null;
    }
}