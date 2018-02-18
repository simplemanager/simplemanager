<?php
namespace App\Guest;

use Osf\Exception\ArchException;
use Osf\Stream\Text;
use Sma\Controller\Json as JsonAction;
use Sma\Bean\InvoiceBean as IB;
use Sma\Session\Identity as I;
use Sma\Bean\NotificationBean;
use Sma\Bean\ContactBean;
use Sma\Bean\GuestBean;
use Sma\Db\DbRegistry;
use App\Recipient\Model\RecipientDbManager as DM;
use App\Guest\Form\FormContact;
use App\Common\Container;
use DB;

/**
 * Espace invité
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
    const I_KEY_ACL_ROLE     = 'acl_role';
    const I_KEY_CONTACT_BEAN = 'contact_bean';
    const I_KEY_GUEST_HASH   = 'guest_hash';
    
    public function indexAction()
    {
        if ($this->checkLogged()) {
            $this->redirect('/guest/home');
        }
    }
    
    public function homeAction()
    {
        if ($this->checkLogged()) {
            $this->pageTitle(trim(sprintf(__("Bonjour %s"), $this->getContactBean()->getFirstname())));
            $this->updateInfo();
        }
        return [];
    }
    
    public function invoicesAction()
    {
        if (!$this->checkLogged()) { return []; }
        $this->pageTitle(__("Mes Factures"));
        $this->updateInfo();
        $data = DbRegistry::getGuestInvoices(self::getContactBean());
        return ['data' => $data, 'guestBean' => self::getGuestBean()];
    }
    
    public function ordersAction()
    {
        if (!$this->checkLogged()) { return []; }
        $this->pageTitle(__("Mes Commandes"));
        $this->updateInfo();
        $data = DbRegistry::getGuestInvoices(self::getContactBean(), IB::TYPE_ORDER);
        return ['data' => $data, 'guestBean' => self::getGuestBean()];
    }
    
    public function quotesAction()
    {
        if (!$this->checkLogged()) { return []; }
        $this->pageTitle(__("Mes Devis"));
        $this->updateInfo();
        $data = DbRegistry::getGuestInvoices(self::getContactBean(), IB::TYPE_QUOTE);
        return ['data' => $data, 'guestBean' => self::getGuestBean()];
    }
    
    public function lettersAction()
    {
        if (!$this->checkLogged()) { return []; }
        $this->pageTitle(__("Mon Courrier"));
        $this->updateInfo();
        $data = DbRegistry::getGuestLetters(self::getContactBean());
        return ['data' => $data, 'guestBean' => self::getGuestBean()];
    }
    
    public function letterAction()
    {
        if (!$this->checkLogged()) { return []; }
        $this->updateInfo();
        $idh = (int) $this->getParam('idh');
        $bean = DbRegistry::getGuestLetterBean(self::getContactBean(), $idh);
        return ['bean' => $bean];
    }
    
    public function infoAction()
    {
        if (!$this->checkLogged()) { return []; }
        $this->pageTitle(__("Mes Informations"));
        $id = self::getContactBean()->getIdCompany();
        $form = new FormContact();
        $form->hydrate(DM::getContactForForm($id, false), null, true, true);
        if ($form->isPostedAndValid()) {
            $company = DM::updateContact($form->getValues(), $id, $id);
            I::set(self::I_KEY_CONTACT_BEAN, DB::getContactTable()->getBean(self::getContactBean()->getId(), false));
            if (!$company) {
                $this->alertWarning(__("Problème rencontré"), __("Un disfonctionnement a été détecté au moment de la mise à jour de vos informations. Veuillez nous excuser pour la gêne occasionnée, nos équipes de développement analysent le problème."));
            } else {
                $notification = (new NotificationBean())
                        ->setContent(sprintf(__("Votre contact %s à mis à jour ses informations personnelles."), $company->getEmail()))
                        ->setIcon('info')->setColor('blue')->setLink('/recipient/view/id/' . $id . '/frsh/' . random_int(10000, 99999));
                DbRegistry::notificationPush($notification, $company->getIdAccount(), false);
                $this->alertInfo(__("Modifications effectuées"), __("Vos informations personnelles ont été mises à jour, merci !"));
            }
        }
        $this->updateInfo();
        return ['form' => $form];
    }
    
    public function loginAction()
    {
        if (I::isLogged()) {
           $this->disableView();
           $this->alertWarning(__("Espace invité"), __("Cette fonctionnalité est certainement réservée à l'un de vos clients. Cet espace lui permet, entre autre, de consulter les documents et informations le concernant."));
           return;
        }
        
        if (self::login($this->getParam('k'))) {
            $this->updateRegistration();
            $this->redirect('/guest/home');
        } else {
            $this->alertWarning(__("Accès inexistant"), __("Les données que vous souhaitez consulter ne sont pas ou plus disponibles."));
            $this->redirect('/');
        }
    }
    
    public function logoutAction()
    {
        Container::getJsonRequest()->cleanRegistration();
        self::logout();
        $this->redirect('/');
    }
    
    /**
     * @return bool
     */
    public static function isLogged(): bool
    {
        return (bool) I::get(self::I_KEY_GUEST_HASH);
    }
    
    /**
     * @return ContactBean
     */
    public static function getContactBean(): ContactBean
    {
        return I::get(self::I_KEY_CONTACT_BEAN);
    }
    
    /**
     * @return GuestBean
     */
    public static function getGuestBean(): ?GuestBean
    {
        $contactBean = self::getContactBean();
        return $contactBean ? DbRegistry::getGuestBean($contactBean) : null;
    }
    
    /**
     * Connexion à l'espace invité avec le hash correspondant
     * @param string|null $key
     * @return void
     * @throws ArchException
     */
    public static function login(?string $key): bool
    {
        if (I::isLogged()) {
            throw new ArchException('On ne peut pas se logguer en invité quand on est déjà logué avec un compte client');
        }
        $bean = $key ? ContactBean::buildContactBeanFromCompanyHash($key, false, true) : null;
        if ($bean instanceof ContactBean) {
            I::hydrate([
                self::I_KEY_ACL_ROLE     => 'GUEST', 
                self::I_KEY_GUEST_HASH   => $key, 
                self::I_KEY_CONTACT_BEAN => $bean]);
            return true;
        }
        return false;
    }
    
    /**
     * Déconnexion de l'espace invité
     * @return void
     */
    public static function logout(): void
    {
        I::clean(self::I_KEY_ACL_ROLE);
        I::clean(self::I_KEY_CONTACT_BEAN);
        I::clean(self::I_KEY_GUEST_HASH);
    }
    
    protected function updateInfo(): bool
    {
        if (self::isLogged() && I::get(self::I_KEY_GUEST_HASH)) {
            I::set(self::I_KEY_CONTACT_BEAN, ContactBean::buildContactBeanFromCompanyHash(I::get(self::I_KEY_GUEST_HASH)));
            $guestBean = self::getGuestBean();
            if ($guestBean->getInvoicesToPayCount()) {
                Container::getJsonRequest()->getMenu()->getItem('guin')->addBadge($guestBean->getInvoicesToPayCount(), 'yellow');
            } else if ($guestBean->getCreditsToPayCount()) {
                Container::getJsonRequest()->getMenu()->getItem('guin')->addBadge($guestBean->getCreditsToPayCount(), 'blue');
            }
            if ($guestBean->getOrdersToSign()) {
                Container::getJsonRequest()->getMenu()->getItem('guor')->addBadge($guestBean->getOrdersToSign(), 'yellow');
            }
            if ($guestBean->getQuotesToConsult()) {
                Container::getJsonRequest()->getMenu()->getItem('guqu')->addBadge($guestBean->getQuotesToConsult(), 'yellow');
            }
            if ($guestBean->getLetterToRead()) {
                Container::getJsonRequest()->getMenu()->getItem('gule')->addBadge($guestBean->getLetterToRead(), 'aqua');
            }
            if (self::getContactBean()->hasWarning()) {
                Container::getJsonRequest()->getMenu()->getItem('guc')->addBadge('!', 'yellow');
            }
            $this->updateRegistration();
            return true;
        }
        return false;
    }
    
    /**
     * Mise à jour de l'état
     * @return void
     */
    protected function updateRegistration(): void
    {
        $guestBean = self::getGuestBean();
        $icon  = self::getContactBean()->getCompanyName() ? 'industry' : 'user';
        $amount = $guestBean->getInvoicesToPayAmountTtc() - $guestBean->getCreditsToPayAmountTtc();
        $contactWarn = self::getContactBean()->hasWarning();
        $color = $amount > 0 || $guestBean->getOrdersToSign() || $guestBean->getQuotesToConsult() || $contactWarn 
                ? 'yellow' 
                : ($amount < 0 || $guestBean->getLetterToRead() ? 'blue' : 'green');
        $info  = $amount > 0
                ? sprintf(__("%s à régler"), Text::currencyFormat($amount)) 
                : ($guestBean->getOrdersToSign()
                        ? sprintf(__("%d commande%s à signer"), $guestBean->getOrdersToSign(), $guestBean->getOrdersToSign() > 1 ? 's' : '') 
                        : ($guestBean->getQuotesToConsult()
                                ? sprintf(__("%d devis à consulter"), $guestBean->getQuotesToConsult())
                                : ($contactWarn
                                        ? __("Completez votre profil") 
                                        : ($guestBean->getLetterToRead()
                                                ? sprintf(__("%d lettre%s à lire"), $guestBean->getLetterToRead(), $guestBean->getLetterToRead() ? 's' : '')
                                                : ($amount < 0
                                                        ? sprintf(__("%s à percevoir"), Text::currencyFormat(-$amount))
                                                        : __("à jour"))))));
        Container::getJsonRequest()->updateRegistration(Text::crop(self::getContactBean()->getComputedTitle(), 20), $info, $color, $icon, null, null, '/guest/home');
    }
    
    /**
     * Vérifie si on est toujours en ligne
     * @return bool
     */
    protected function checkLogged(): bool
    {
        if (!self::isLogged()) {
            $this->alertWarning(
                    __("Session expirée"),
                    __("Pour vous reconnecter sur votre espace invité, utilisez à nouveau le lien envoyé par e-mail."));
            $this->disableView();
            $this->redirect('/');
            return false;
        }
        return true;    
    }
}
