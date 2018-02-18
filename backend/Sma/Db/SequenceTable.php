<?php
namespace Sma\Db;

use Sma\Db\Generated\AbstractSequenceTable;
use Sma\Session\Identity as I;
use Osf\Exception\ArchException;

/**
 * Table model for table sequence
 *
 * Use this class to complete AbstractSequenceTable
 *
 * @version 1.0
 * @author Guillaume Ponçon - OpenStates Framework PHP Generator
 * @since OSF 2.0
 * @package osf
 * @subpackage generated
 */
class SequenceTable extends AbstractSequenceTable
{
    const FIRST_VALUE = 1;

    /**
     * Prochaine valeur de séquence
     * @param string $name
     * @param int|null $idAccount
     * @param bool $commit
     * @return int
     * @throws ArchException
     */
    public function nextValue(string $name, ?int $idAccount = null, bool $commit = true): int
    {
        // Vérification de la syntaxe du nom de séquence
        if (!preg_match('/^[a-zA-Z0-9_-]{1,20}$/', $name)) {
            throw new ArchException('Bad name syntax [' . $name . ']');
        }
        $idAccount = $idAccount ?: I::getIdAccount();

        // Effectue l'opération et retourne la prochaine valeur
        return $this->commitSequenceValue($name, $idAccount, $commit);
    }
    
    /**
     * Réinitialisation des séquences
     * @param int $seqQuote
     * @param int $seqOrder
     * @param int $seqInvoice
     * @return void
     */
    public function initSequences(int $seqQuote = 0, int $seqOrder = 0, int $seqInvoice = 0): void
    {
        $idAccount = I::getIdAccount();
        $this->commitSequenceValue('quote',   $idAccount, true, $seqQuote);
        $this->commitSequenceValue('order',   $idAccount, true, $seqOrder);
        $this->commitSequenceValue('invoice', $idAccount, true, $seqInvoice);
    }
    
    /**
     * @param string $name
     * @param int $idAccount
     * @param bool $commit
     * @param int|null $sequenceValue
     * @return int
     */
    protected function commitSequenceValue(string $name, int $idAccount, bool $commit = true, ?int $sequenceValue = null): int
    {
        // Récupération de l'enregistrement correspondant à la séquence
        $row = $this->find(['id_account' => $idAccount, 'name' => $name]);
        if (!$row && $commit) {
            $value = $sequenceValue ?? self::FIRST_VALUE;
            $this->insert([
                'id_account' => $idAccount,
                'name' => $name,
                'value' => $value
            ]);
            return (int) $value;
        }

        // Incrémentation de la valeur et mise à jour si $commit est vrai
        $nextValue = $sequenceValue ?? (($row ? $row->getValue() : 0) + 1);
        if ($commit) {
            $row->setValue($nextValue)->save();
        }
        return (int) $nextValue;
    }
}
