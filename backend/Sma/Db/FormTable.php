<?php
namespace Sma\Db;

use Sma\Db\Generated\AbstractFormTable;
use Sma\Session\Identity;

/**
 * Table model for table form
 *
 * Use this class to complete AbstractFormTable
 *
 * @version 1.0
 * @author Guillaume Ponçon - OpenStates Framework PHP Generator
 * @since OSF 2.0
 * @package osf
 * @subpackage generated
 */
class FormTable extends AbstractFormTable
{
    /**
     * @param string $class
     * @param int $idAccount
     * @return array|false
     */
    public function getFormFromClass($class, $idAccount = null)
    {
        $idAccount = $idAccount ?? Identity::getIdAccount();
        $params = ['id_account' => $idAccount, 'class' => $class];
        $row = $this->select($params)->current();
        if ($row instanceof \Sma\Db\FormRow) {
            $row = $row->toArray();
            if ($row['form_values']) {
                $row['form_values'] = unserialize($row['form_values']);
            }
        }
        return $row;
    }
    
    /**
     * @param string $class
     * @param string $formValues
     * @param int $idAccount
     */
    public function updateForm(string $class, array $formValues, $idAccount = null)
    {
        $idAccount = $idAccount ?? Identity::getIdAccount();
        $params = ['id_account' => $idAccount, 'class' => $class];
        $update = (bool) $this->select($params)->current();
        $serializedFormValues = serialize($formValues);
        if ($update) {
            return $this->update(['form_values' => $serializedFormValues], $params);
        } 
        $values = $params;
        $values['form_values'] = $serializedFormValues;
        return $this->insert($values);
    }
    
    /**
     * @param string $class
     * @return array
     */
    public function getFormParticipants(string $class): array
    {
        $sql = 'SELECT account.id as id_account, concat(account.lastname, concat(\' \', concat(account.firstname, concat(\' (\', concat(account.email, \')\'))))) as name '
                . 'FROM ' . DB_SCHEMAS['admin'] . '.account, ' . DB_SCHEMAS['common'] . '.form '
                . 'WHERE account.id=form.id_account '
                . 'AND form.class = ? '
                . 'ORDER BY account.lastname, account.firstname, account.email';
        $results = $this->prepare($sql)->execute([$class]);
        $vals = ['all' => __("Tous les comptes")];
        foreach ($results as $row) {
            $vals[$row['id_account']] = $row['name'];
        }
        return $vals;
    }
}
