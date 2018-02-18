<?php
namespace Sma\Db;

use Osf\Exception\ArchException;
use Sma\Db\Generated\AbstractCompanyTable;
use Sma\Session\Identity;
use Sma\Bean\ContactBean;
use Sma\Log;
use DB;

/**
 * Table model for table company
 *
 * Use this class to complete AbstractCompanyTable
 *
 * @version 1.0
 * @author Guillaume Ponçon - OpenStates Framework PHP Generator
 * @since OSF 2.0
 * @package osf
 * @subpackage generated
 */
class CompanyTable extends AbstractCompanyTable
{
    use Addon\Hash;
    use Addon\SafeActions;
    
    const STATUS_TITLES_SHORT = [
        'a'    => 'Association',
        'ae'   => 'Auto Entrepreneur',
        'ei'   => 'Entreprise Individuelle',
        'eurl' => 'EURL',
        'sarl' => 'SARL',
        'sa'   => 'SA',
        'sas'  => 'SAS',
        'sasu' => 'SASU',
        'sci'  => 'SCI',
        'scp'  => 'SCP',
        'scm'  => 'SCM',
    ];
    
    const STATUS_TITLES_LONG = [
        'a'    => 'Association',
        'ae'   => 'Auto Entrepreneur',
        'ei'   => 'Entreprise Individuelle',
        'eurl' => 'EURL (Entreprise Unipersonnelle à Responsabilité Limitée)',
        'sarl' => 'SARL (Société à Responsabilité Limitée)',
        'sa'   => 'SA (Société Anonyme)',
        'sas'  => 'SAS (Société par Actions Simplifiée)',
        'sasu' => 'SASU (Société par Actions Simplifiée Unipersonnelle)',
        'sci'  => 'SCI (Société Civile Immobilière)',
        'scp'  => 'SCP (Société Civile Professionnelle)',
        'scm'  => 'SCM (Société Civile de Moyens)',
    ];
    
    /**
     * @param array $values
     * @return boolean
     */
    public function updateCurrentCompany(array $values)
    {
        $currentUserId = Identity::get('id');
        $contact = DB::getContactTable()->select('is_account=' . (int) $currentUserId)->current();
        
        //$contact = new ContactRow();
        $company = $contact->getIdCompany() ? DB::getCompanyTable()->find($contact->getIdCompany()) : null;
        //$company = new CompanyRow();

        $dataAddress = $values['a'];
        $dataAddress['id_account'] = $currentUserId;
        $dataCompany = $values['c'];
        $dataCompany['id_account'] = $currentUserId;
        $dataCompany['type'] = 'mine';
        $dataCompany['id_contact'] = $contact->getId();
        
        //$this->beginTransaction();
//        try {

            // Address
            if ($company && $company->getIdAddress()) {
                DB::getAddressTable()->update($dataAddress, 'id=' . (int) $company->getIdAddress());
            } else {
                DB::getAddressTable()->insert($dataAddress);
                $dataCompany['id_address'] = DB::getAddressTable()->lastInsertValue;
            }

            // Company
            if ($company) {
                $company->populate(array_merge($company->toArray(), $dataCompany), true)->save();
                $idCompany = $company->getId();
            } else {
                $dataCompany['hash'] = $this->generateHash();
                DB::getCompanyTable()->insert($dataCompany);
                $idCompany = DB::getCompanyTable()->lastInsertValue;
                $contact->setIdCompany($idCompany)->save();
            }
            
            // Mise à jour du bean
            $bean = ContactBean::buildContactBeanFromCompanyId($idCompany, true);
            DB::getCompanyTable()->find($idCompany)->setBean($bean)->save();
            
            //$this->commit();
//        } catch (\Exception $e) {
            //$this->rollback();
//            return false;
//        }
        return true;
    }
    
    public function getCompanyLogoId($idCompany)
    {
        $company = $this->find($idCompany);
        return $company ? $company->getIdLogo() : null;
    }
    
    /**
     * Update company logo id
     * @param int $idCompany
     * @param int $idImage
     * @param bool $checkAccount
     * @return $this
     * @throws ArchException
     */
    public function setCompanyLogo(int $idCompany, int $idImage, bool $checkAccount = true, bool $cleanImages = true)
    {
        $company = $this->find($idCompany);
        if (!$company) {
            throw new ArchException('Unknown company ' . $idCompany);
        }
        if ($checkAccount && $company->getIdAccount() !== Identity::getIdAccount()) {
            Log::hack('Current user tries to update company of other user', 'DB', [$idCompany, $idImage]);
            throw new ArchException('Current user tries to update company of other user');
        }
        if (!$idImage) {
            $company->setIdLogo(null)->save();
        } else if ($company->getIdLogo() !== $idImage) {
            $company->setIdLogo($idImage)->save();
        }
        $cleanImages && DB::getImageTable()->cleanAccountLogo();
        return $this;
    }
    
    /**
     * ContactBean depuis la base
     * @param int $id
     * @param bool $safe
     * @return ContactBean|null
     */
    public function getContactBean(int $id, bool $safe = true): ?ContactBean
    {
         $row = $safe ? $this->findSafe($id) : $this->find($id);
         return $this->getContactBeanFromRow($row, $safe);
    }
    
    /**
     * ContactBean depuis le hash de l'entreprise
     * @param string $hash
     * @param bool $safe
     * @return ContactBean|null
     */
    public function getContactBeanFromHash(string $hash, bool $safe = true): ?ContactBean
    {
        $row = $this->buildSelect(['hash' => $hash])->execute()->current();
        return $this->getContactBeanFromRow($row, $safe);
    }
    
    /**
     * @param \Sma\Db\CompanyRow|null $row
     * @return ContactBean|null
     */
    protected function getContactBeanFromRow(?CompanyRow $row, bool $safe = true): ?ContactBean
    {
         if ($row instanceof CompanyRow) {
             return DB::getContactTable()->getBean($row->getIdContact(), $safe);
         }
         return null;
    }
}
