<?php
namespace App\Account;

use Osf\Application\OsfApplication as Application;
use Osf\Helper\Tab;
use Osf\Image\ImageHelper as Image;
use Osf\Crypt\Crypt;
use Osf\Exception\DisplayedException;
use Sma\Controller\Json as JsonAction;
use Sma\Db\DbRegistry;
use Sma\Session\Identity;
use Sma\Layout;
use Sma\Log;
use Sma\Config;
use Sma\Search\Indexer;
use App\Guest\Controller as GuestController;
use App\Account\Form\FormLogin;
use App\Account\Form\FormRegistration;
use App\Account\Form\FormContact;
use App\Account\Form\FormEmail;
use App\Account\Form\FormPassword;
use App\Account\Form\FormRetrievePassword;
use App\Account\Form\FormRetrievePasswordAsk;
use App\Account\Model\Auth;
use App\Common\Container;
use App\Event\Controller as EventController;
use App\Account\Model\Identity as IdentityModel;
use App\Account\Model\Registration;
use App\Account\Model\FeaturesValidator;
use Exception;
use H, DB, S;

/**
 * Identification
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 20 nov. 2013
 * @package common
 * @subpackage controllers
 */
class Controller extends JsonAction
{
    public function init()
    {
        Identity::isLogged() && H::layout()->addBreadcrumbLink(__("Options"), H::url('account'));
    }
    
    public function indexAction()
    {
        if (!Identity::isLogged()) {
            $this->redirect(H::url('account', 'login'));
        } else {
            $this->pageTitle(__("Mes Options"));
        }
    }
    
    public function loginAction()
    {
        // Changement d'adresse e-mail
        $key = $this->getParam('e');
        if ($key) {
            if ($info = Auth::checkEmail($key)) {
                $this->alertSuccess(__("E-mail modifié !"), sprintf(__("Vous pouvez désormais vous connecter à %s avec votre nouvelle adresse e-mail."), APP_NAME));
            } else {
                $this->alertDanger(__("Echec de validation d'adresse e-mail"), __("Le délai d'activation est dépassé, ou cette opération n'est pas valide."));
            }
        }

        // Activation d'un compte
        $key = $this->getParam('k');
        if ($key) {
            if ($info = Auth::checkRegistration($key)) {
                $this->alertSuccess(__("Activation effectuée !"), sprintf(__("%s, votre compte est actif. Vous pouvez maintenant vous identifier."), $info['firstname']));
            } else {
                $this->alertWarning(__("Problème d'activation"), __("Le délai d'activation est dépassé ou votre compte est déjà actif. Si vous n'arrivez pas à vous identifier, créez un nouveau compte."));
            }
        }

        // On délogue en cas de requête d'ouverture de compte ou de changement d'email
        if (($this->getParam('e') || $this->getParam('k')) && Identity::isLogged()) {
            Auth::logout();
            $this->redirect(H::url('account', 'login'));
            return;
        }
        
        // Déjà logué ?
        if (Identity::isLogged()) {
            return ['menu' => !$this->getParam('from')];
        }

        // Formulaire d'identification
        $form = new FormLogin();
        isset($info['email']) && $info['email'] && $form->getElement('email')->setValue($info['email']);
        if ($form->isPostedAndValid()) {
            $identity = Auth::login($form->getValue('email'), $form->getValue('password'));
            if ($this->checkIdentity($identity)) {
                
                // Déconnexion éventuelle de l'espace invité
                if (GuestController::isLogged()) {
                    GuestController::logout();
                }
                
                // identité + init
                Identity::hydrate(DbRegistry::getUserInfo(null, $identity->toArray()));
                Container::getJsonRequest()->setRenderType(Layout::RENDER_INIT);
                
                // MAJ indexation, tick & notifications
                Indexer::indexPages();
                EventController::registerTick();
                DbRegistry::notificationLoad();
            
                // Si la société n'est pas paramétrée, redirection sur le formulaire d'édition, 
                // sinon redirection vers la page d'accueil. 
                $params = Container::getFlashMessenger()->getRedirectToParams() ?? [];
                if (!$params && !Identity::getIdCompany()) {
                    $this->redirect(H::url('account', 'company'));
                    return [];
                } else {
                    $this->redirect(H::url(null, null, $params), false);
                }
                return ['redirect' => true];
            }
        } else {
            Container::getJsonRequest()->setRenderType(Layout::RENDER_INIT);
            if (isset($info) && isset($info['email'])) {
                $form->getElement('email')->setValue($info['email']);
            }
        }
        return ['form' => $form];
    }
    
    /**
     * Vérifie l'identité de l'utilisateur qui se logue
     * @param \Sma\Db\AccountRow $identity
     * @return boolean
     * @throws ArchException
     */
    protected function checkIdentity($identity)
    {
        if (!($identity instanceof \Sma\Db\AccountRow)) {
            $this->alertDanger(
                    __("Echec d'identification"),
                    __("Ce compte n'existe pas ou n'est pas encore actif, ou peut-être avez-vous fait une erreur de saisie ?"));
            return false;
        }
        
        switch ($identity->getStatus()) {
            case 'draft' : 
                $this->alertWarning(
                    __("Compte inactif"),
                    __("Vous ne pouvez pas encore vous identifier car votre compte n'est pas encore actif. Cliquez sur le lien d'activation dans l'email que nous vous avons envoyé ou réinitialisez votre mot de passe (bouton \"mot de passe oublié\")."));
                return false;
            case 'disabled' : 
                $this->alertDanger(
                    __("Compte désactivé"),
                    sprintf(__("Ce compte a été désactivé à cause d'une inactivité prolongée, d'une demande utilisateur ou d'une action frauduleuse. Conformément aux règles en vigueur, il sera supprimé de la base %s."), APP_NAME));
                return false;
            case 'suspended' : 
                $this->alertWarning(
                    __("Compte suspendu"), 
                    __("Vous ne pouvez pas vous connecter pour l'instant."));
                return false;
            case 'enabled' : 
                return true;
            default : 
                throw new ArchException('Unknown status ' . $identity->getStatus());
        }
    }
    
    public function logoutAction()
    {
        if (Identity::isLogged()) {
            $this->alertInfo(sprintf(__("Merci %s pour votre visite."), Identity::get('firstname')));
        }
        Auth::logout();
        $this->redirect(H::url('account', 'login'));
    }
    
    public function registrationAction()
    {
        $this->pageTitle(__("Création d'un compte"));
        $form = new FormRegistration();
        if ($form->isPostedAndValid()) {
            $idAccount = Auth::register($form);
            Registration::notifyRegistered($form, $idAccount);
            $this->disableView();
            S::set('registration', true);
            $this->redirect(H::url('account', 'registered'));
            return [];
        }
        return ['form' => $form];
    }
    
    public function registeredAction()
    {
        if (S::get('registration')) {
            $this->pageTitle(__("Merci !"));
            S::clean('registration');
        } else {
            $this->redirect(H::url());
        }
        return [];
    }
    
    public function editAction()
    {
        $my = $this->getParam('my');
        switch ($my) {
            case 'profile'   : return $this->editProfile();
            case 'email'     : return $this->editEmail();
            case 'pass'      : return $this->editPassword();
            case 'sequences' : return $this->editSequences();
            default: $this->notFound();
        }
    }
    
    protected function editProfile()
    {
        $this->pageTitle(__("Mon compte"));
        $form = new FormContact();
        $where = 'id_account=' . (int) Identity::get('id');
        $values = DB::getAddressContactTable()->select($where)->toArray();
        if (isset($values[0])) {
            $form->hydrate(Tab::reduce($values[0], $form->getElementKeys()), null, false);
        }
        if ($form->isPostedAndValid()) {
            if (!DB::getAddressContactTable()->updateCurrentUser($form->getValues())) {
                $this->alertDanger(__("Une erreur s'est produite à l'enregistrement des valeurs."));
            } else {
                Identity::hydrate(DbRegistry::getUserInfo(Identity::get('id')));
            }
            $this->dispatch(['controller' => 'account', 'action' => 'login', 'from' => 'edit']);
        }
        return ['form' => $form, 'menu' => !$form->isPosted()];
    }
    
    /**
     * Mise à jour de l'email
     * @return array
     */
    protected function editEmail()
    {
        $this->pageTitle(__("Mon compte"));
        $form = new FormEmail();
        if ($form->isPostedAndValid()) {
            Auth::updateEmail($form->getValue('enew'));
            $this->alertWarning(
                    __("Vous y êtes presque !"), 
                    __("Confirmez sans tarder votre changement d'adresse e-mail en cliquant sur le lien que vous allez recevoir par e-mail. Vous avez 15 minutes pour effectuer cette validation."));
            $this->dispatch([
                'controller' => 'account', 
                'action' => 'login', 
                'from' => 'email']
            );
        } else if (!$form->isPosted()) {
            $this->alertInfo(
                    __("E-mail : procédure de mise à jour"), 
                    __("Vous devez confirmer le changement d'adresse e-mail en cliquant sur le lien qui vous sera envoyé. Cela permet de s'assurer que l'e-mail saisi est correct. Vous disposez d'un délai de 15 minutes pour confirmer votre nouvelle adresse e-mail."));
        }
        return ['form' => $form, 'menu' => !$form->isPosted()];
    }
    
    /**
     * Mise à jour du mot de passe
     * @return array
     */
    protected function editPassword()
    {
        $this->pageTitle(__("Mon compte"));
        $form = new FormPassword();
        if ($form->isPostedAndValid()) {
            $newHash = Crypt::passwordHash($form->getElement('pnew')->getValue());
            DB::getAccountTable()->find(Identity::getIdAccount())->setPassword($newHash)->save();
            $this->alertSuccess(__("Mot de passe mis à jour avec succès"));
            $this->dispatch([
                'controller' => 'account', 
                'action' => 'login', 
                'from' => 'password']
            );
        }
        return ['form' => $form, 'menu' => !$form->isPosted()];
    }
    
    /**
     * Modification des séquences
     * @return array
     */
    protected function editSequences()
    {
        $this->pageTitle(__("Mon compte"));
        $form = new Form\FormSequences();
        if ($form->isPostedAndValid()) {
            [$seqQuote, $seqOrder, $seqInvoice] = array_values($form->getValues());
            DB::getSequenceTable()->initSequences($seqQuote - 1, $seqOrder - 1, $seqInvoice - 1);
            $this->alertSuccess(__("Séquences mises à jour avec succès"));
            $this->dispatch([
                'controller' => 'account', 
                'action' => 'login', 
                'from' => 'sequences']
            );
        } else {
            Identity::isLevelExpert() || $this->alertWarning(__("Attention à la législation"), __("Les numéros de factures doivent obligatoirement former une séquence sans interruption."));
            Identity::isLevelBeginner() && $this->alertInfo(__("Qu'est-ce qu'une séquence ?"), sprintf(__("Vos documents, en particulier les factures, doivent être obligatoirement numérotées pour former une séquence continue, sans interruption. Définissez ici, pour chaque type de document, le numéro à partir duquel %s va commencer sa numérotation. Ceci est utile si vous avez déjà émis des documents avant d'utiliser %s."), APP_NAME, APP_NAME));
        }
        return ['form' => $form, 'menu' => !$form->isPosted()];
    }
    
    /**
     * Réinitialisation du mot de passe
     * @return array
     */
    public function passwordAction()
    {
        $this->pageTitle(__("Mot de passe perdu"));
        
        // Pour cette action, on ne doit pas être logué
        if (Identity::isLogged()) {
            Auth::logout();
        }
        
        // Mot de passe 
        if ($key = $this->getParam('k')) {
            if (!($value = Auth::retrievePasswordCheck($key))) {
                $this->alertDanger(
                        __("Changement de mot de passe"), 
                        __("Requête invalide ou délai dépassé pour cette opération. Veuillez retenter l'opération en cliquant sur 'Mot de passe oublié' si vous souhaitez réinitialiser votre mot de passe."));
                $this->redirect(H::url('account', 'login'));
                return;
            }
            $form = new FormRetrievePassword();
            if ($form->isPostedAndValid()) {
                Auth::retrievePasswordCommit($key, $value['email'], $form->getValue('pnew'));
                $this->alertSuccess(
                        __("Mot de passe mis à jour !"),
                        __("Votre mot de passe a été réinitialisé avec succès. Vous pouvez maintenant vous connecter."));
                $this->redirect(H::url('account', 'login'));
                return;
            }
        }
        
        // Formulaire email
        else {
            $form = new FormRetrievePasswordAsk();
            if ($form->isPostedAndValid()) {
                try {
                    Auth::retrievePasswordQuery($form->getValue('email'));
                    $this->alertInfo(
                            __("Changement de mot de passe"), 
                            __("Un e-mail vous a été envoyé. Veuillez le consulter sans tarder pour réinitialiser votre mot de passe."));
                    $this->redirect(H::url('account', 'login'));
                } catch (DisplayedException $e) {
                    $this->disableView();
                    $this->clearResponse();
                    $this->alertWarning(__("Fonctionnalité momentanément indisponible"), $e->getMessage());
                }
                return;
            }
        }
        
        return ['form' => $form];
    }
    
    public function featuresAction()
    {
        $this->pageTitle(__("Mes paramètres"));
        H::layout()->addBreadcrumbLink(__("Paramètres"), H::url('account', 'features'));
        $level = $this->checkLevelFromPost();
        $form = (new Config())
                ->getForm($level !== 'expert')
                ->hydrate(Identity::getParams(), null, true, true)
                ->setFocusedElt($this->getParam('f'));
        $form->setTitle(__("Paramètres"), 'cog');
        $validator = new FeaturesValidator();
        if ($form->isPostedAndValid($validator)) {
            $values = array_replace_recursive(Identity::getParams(), $form->getValues());
            IdentityModel::updateParams($values);
            Container::getCacheSma()->cleanUserCache();
            $msg = __("Les paramètres sont mis à jour");
            $validator->hasWarnings()
                ? $this->alertWarning($msg, __("Cependant il existe quelques avertissements que vous pouvez consulter ci-dessous."))
                : $this->alertSuccess($msg);
            $validator->hasWarnings() || $this->redirectAuto();
        } else if ($form->isPosted()) {
            $this->alertDanger(__("Votre saisie comporte des erreurs"));
        }
        return ['form' => $form, 'menu' => !$form->isPosted()];
    }
    
    /**
     * Retourne le nouveau niveau le cas échéant
     * @return string
     */
    protected function checkLevelFromPost(): string
    {
        $interface = filter_input(INPUT_POST, 'interface', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
        $currentLevel = Identity::getParam('interface', 'level');
        if (!isset($interface['level']) || $interface['level'] === $currentLevel) {
            return $currentLevel;
        }
        if ($interface['level'] === 'expert') {
            $this->alertInfo(__("Vous êtes un expert..."), __("Vous venez d'activer le niveau 'expert'. Ce formulaire s'affiche sans les descriptions de champs et votre interface est maintenant optimisée pour la productivité et les performances. Certaines fonctionnalités pourront vous paraître un peu techniques."));
        }
        return $interface['level'];
    }
    
    public function companyAction()
    {
        H::layout()->addBreadcrumbLink(__("Société"), H::url('account', 'company'));
        $this->pageTitle(__("Ma société"));
        if ($this->getParam('edit') || !Identity::get('company')) {
            $form = new Form\FormCompany();
            if (Identity::get('company')) {
                $company = Identity::get('company');
                $form->hydrate(Tab::reduce($company, ['legal_status', 'title', 'tel', 'fax', 'email', 'description', 'url']), null, false);
                $form->hydrate(Tab::reduce($company['address'], ['address', 'postal_code', 'city', 'country']), null, false);
            } else {
                $contact = Identity::get('contact');
                if (is_array($contact)) {
                    $form->hydrate($contact, null, false, true);
                }
            }
            if ($form->isPostedAndValid()) {
                if (!DB::getCompanyTable()->updateCurrentCompany($form->getValues())) {
                    $this->alertDanger(__("Une erreur s'est produite à l'enregistrement des valeurs."));
                } else {
                    $firstTime = !Identity::get('company');
                    $oldCompany = Identity::getCompany();
                    Identity::hydrate(DbRegistry::getUserInfo());
                    
                    // Mise à jour des paramètres liés au statut juridique
                    if (!is_array($oldCompany) || 
                        !isset($oldCompany['legal_status']) || 
                        $oldCompany['legal_status'] !== Identity::get('company', 'legal_status')) {
                        IdentityModel::updateLegalStatusParams(Identity::get('company', 'legal_status'));
                        if (isset($oldCompany['legal_status'])) {
                            $this->alertWarning(__("Mise à jour du statut juridique"), __("Vos paramètres ont été ajustés pour tenir compte de votre statut juridique. Vérifiez la franchise en base de TVA et les paramètres liés aux produits dans [options] -> [mes paramètres]."));
                        }
                    }
                    
                    // Redirection éventuelle
                    $this->redirectAuto();
                    
                    return ['company' => Identity::get('company'), 'menu' => false, 'first_time' => $firstTime];
                }
            }
            return ['form' => $form, 'menu' => !$form->isPosted() && !Identity::get('company')];
        } else {
            return ['company' => Identity::get('company'), 'menu' => true];
        }
    }
    
    public function logoAction()
    {
        $this->disableLayout();
        $vars = [];
        if (isset($_FILES['logo'])) {
            $vars['check'] = Image::checkImageFromPostFile($_FILES['logo']);
            if ($vars['check']['img'] && !$vars['check']['error']) {
                try {
                    $idCompany = Identity::getIdCompany();
                    $row = DB::getImageTable()->insertImage($_FILES['logo'], 'logo', null, null, 1200, $vars['check']['img']);
                    DB::getCompanyTable()->setCompanyLogo($idCompany, $row['id']);
                    $company = Identity::getCompany();
                    $company['id_logo'] = $row['id'];
                    Identity::set(Identity::SECTION_COMPANY, $company);
                    Identity::set(Identity::SECTION_LOGO_COLOR, isset($row['color']) && $row['color'] ? '#' . $row['color'] : null);
                } catch (DisplayedException $e) {
                    Log::error("Insertion logo displayable: " . $e->getMessage(), 'DB', $e);
                    $vars['check']['error'] = $e->getMessage();
                } catch (Exception $e) {
                    Log::error("Update logo: " . $e->getMessage(), 'DB', $e);
                    $vars['check']['error'] = __("Votre logo n'a pu être enregistré en l'état. Tentez de changer le type et la taille du fichier.");
                }
            }
        }
        $vars['lid'] = DB::getCompanyTable()->find(Identity::getIdCompany())->getIdLogo();
        return $vars;
    }
    
    /**
     * Vérification reCaptcha via CURL
     * @return boolean
     * @throws ArchException
     */
    protected function reCaptchaCheck()
    {
        // Pas en production, on passe
        if (!Application::isProduction()) {
            return true;
        }
        
        // Récupération des infos, fail si pas trouvé
        $response = filter_input(INPUT_POST, 'g-recaptcha-response');
        $remoteIp = filter_input(INPUT_SERVER, 'REMOTE_ADDR');
        if (!$response || !$remoteIp) {
            Log::error("Pas de réponse de recaptcha ou d'IP", 'CAPTCHA', ['r' => $response, 'ip' => $remoteIp]);
            return false;
        }
        
        // Requête curl à google pour vérifier
        $url = 'https://www.google.com/recaptcha/api/siteverify';
        $data = [
            'secret' => '6Ld8GjIUAAAAAJj4S-prIdWR7U5U9VtYqFm4mHnQ',
            'response' => $response,
            'remoteip' => $remoteIp
        ];
        $params = [
            CURLOPT_URL => $url,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($data),
            CURLOPT_RETURNTRANSFER => true
        ];
        
        // Envoi de la requête
        $ch = curl_init();
        curl_setopt_array($ch, $params);
        $apiResponse = json_decode(curl_exec($ch), true);
        var_dump($apiResponse);
        curl_close($ch);
        
        // Analyse de la réponse
        if (!is_array($apiResponse) || !isset($apiResponse['success'])) {
            $msg = 'reCaptcha API response failed';
            Log::error($msg, 'CAPTCHA', $apiResponse);
            throw new ArchException($msg);
        }
        Log::info('reCaptcha query', 'CAPTCHA', ['q' => $params, 'r' => $apiResponse]);
        return (bool) $apiResponse['success'];
    }
}

// Implémentation recaptcha à réaliser dans registrationAction
//        $this->alertWarning("Formulaire d'inscription en travaux, ne vous inscrivez pas ce matin (26/9).");
//            if (!$this->reCaptchaCheck()) {
//                $this->alertDanger(__("Validation en echec"), __("Nous ne pouvons pas créer votre compte..."));
//                return [];
//            }
