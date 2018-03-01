<?php
namespace App\Account\Model;

use Osf\Crypt\Crypt;
use Osf\Session\AppSession as Session;
use Osf\Helper\Tab;
use Osf\Exception\DisplayedException;
use Sma\Db\DbContainer;
use Sma\Session\Identity;
use Sma\Db\DbRegistry;
use App\Common\Container;
use Sma\Mail;
use App\Account\Form\FormRegistration;
use Sma\Log;
use DB, C, H;

/**
 * Description of Auth
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright (c) 2014 , OpenStates
 * @since OFT 2.0 - 9 janv. 2014
 * @package name
 * @subpackage name
 */
class Auth
{
    const PASSWORD_DELAY = 3600;
    
    /**
     * Login
     * @param type $email
     * @param type $password
     * @return \Sma\Db\AccountRow
     */
    public static function login($email, $password)
    {
        $row = DbContainer::getAccountTable()
                ->select(['email' => $email])
                ->current();
        return $row && Crypt::passwordVerify($password, $row->getPassword()) ? $row : false;
    }
    
    /**
     * Logout
     */
    public static function logout()
    {
        // On affiche plus les notifications
        DbRegistry::notificationUnload();
        
        // On affiche plus le nom de l'utilisateur et l'icone de déconnexion
        Container::getJsonRequest()->cleanRegistration()->cleanHeaderUser();
        
        // On supprime l'identité de l'utilisateur de la session
        Identity::cleanAll();
        Session::destroy();
    }
    
    /**
     * Effectue l'enregistrement
     * @param FormRegistration $form
     * @return int Id account
     */
    public static function register(FormRegistration $form)
    {
        // Insertion des valeurs
        $form->getElement('password')->setValue(Crypt::passwordHash($form->getValue('password')));
        $id = $form->insertValues(['firstname', 'lastname', 'email', 'password']);
        
        // Mise en cache d'un hash de vérification
        $verifKey = Crypt::hash(microtime() . rand(1000, 1000000));
        $verifVal = Tab::reduce($form->getValues(), ['firstname', 'email']);
        $verifVal['id'] = $id;
        C::set('REG::' . $verifKey, $verifVal, 24 * 3600);
        
        // Envoi du mail de confirmation
        $noReply = Container::getConfig()->getConfig('mail', 'noreply');
        (new Mail())
            ->addTo($form->getValue('email'), trim($form->getValue('firstname') . ' ' . $form->getValue('lastname')))
            ->addFrom($noReply['mail'], $noReply['name'])
            ->setSubject(sprintf(__("[%s] Ouverture de compte %s"), APP_SNAM, APP_NAME))
            ->addTitle(sprintf(__("Bienvenue %s !"), H::html($form->getValue('firstname'))))
            ->addParagraph(sprintf(__("Activez votre compte en cliquant sur le lien suivant. Vous verrez que %s vous simplifiera la vie."), APP_NAME))
            ->addLinkBullet(__("J'active mon compte"), H::baseUrl(H::url('account', 'login', ['k' => $verifKey]), true))
            ->addTitle(sprintf(__("Pour bien commencer sur %s"), APP_NAME))
            ->addParagraph(__("En tant de nouvel arrivant, vous serez guidé dans les étapes de configuration de votre compte. Vous aurez besoin d'informations administratives relatives à votre entreprise, celles-ci vous seront demandées qu'une seule fois."))
            ->addTitle(__("Délai d'activation"))
            ->addParagraph(sprintf(__("Vous avez jusqu'au %s (24 heures) pour activer votre compte. Passé ce délai, vous pouvez à tout moment"), date(__("d/m/Y à H:i"), time() + (3600 * 24))) . ' <a href="' . H::baseUrl(H::url('account', 'registration'), true) . '" target="_blank">' . __("renouveller votre inscription") . '</a>' . '.', false)
            ->send();

        return $id;
    }
    
    /**
     * Vérifie l'enregistrement et active le compte
     * @param string $key
     * @return false|array
     */
    public static function checkRegistration($key)
    {
        $info = C::get('REG::' . $key);
        if ($info && is_array($info)) {
            DB::getAccountTable()->find($info['id'])->setStatus('enabled')->save();
            C::clean('REG::' . $key);
            Log::info(__("Activation de compte effectuée."), 'REGISTRATION', $info);
            (new Mail())->addToAdmin()
                ->setSubject(sprintf(__("[%s] Compte #%d activé"), APP_SNAM, $info['id']))
                ->addParagraph(sprintf(__("L'utilisateur %s vient d'activer son compte #%d avec succès."), $info['email'], $info['id']))
                ->addLinkBullet(__("Gérer le compte"), H::baseUrl(H::url('admin', 'board', ['id' => $info['id']]), true))
                ->sendDeferred();
            return $info;
        }
        Log::warning(__("Tentative infructueuse d'activation de compte."), 'REGISTRATION');
        return false;
    }
    
    /**
     * Changement du login
     * @param string $newEmail
     */
    public static function updateEmail($newEmail)
    {
        // Mise en cache d'un hash de vérification
        $verifKey = Crypt::hash(microtime() . rand(1000, 1000000));
        $verifVal = [
            'idAccount' => Identity::getIdAccount(),
            'idContact' => Identity::getIdContact(),
            'email' => $newEmail];
        C::set('CEM::' . $verifKey, $verifVal, 15 * 60);
        
        // Envoi du mail de confirmation
        (new Mail())
                ->addTo($newEmail, trim(Identity::getFullname()))
                ->setSubject(sprintf(__("[%s] Confirmez votre nouvelle adresse e-mail"), APP_SNAM))
                ->addParagraph(sprintf(__("Pour confirmer la nouvelle adresse e-mail '%s' de votre compte %s, cliquez sur le lien suivant :"), $newEmail, APP_NAME))
                ->addLinkBullet(__("Je confirme mon adresse e-mail"), H::baseUrl(H::url('account', 'login', ['e' => $verifKey]), true))
                ->addParagraph(sprintf(__("Note : vous avez jusqu'au %s pour valider cette opération. Merci pour la confiance que vous témoignez à %s et à très bientôt !"), date(__("d/m/Y à H:i"), time() + (59 * 15)), APP_NAME))
                ->send();
    }
    
    /**
     * Valide le changement d'e-mail
     * @param string $key
     * @return boolean
     */
    public static function checkEmail($key)
    {
        $info = C::get('CEM::' . $key);
        if ($info && is_array($info)) {
            DB::getAccountTable()->find($info['idAccount'])->setEmail($info['email'])->save();
            DB::getContactTable()->find($info['idContact'])->setEmail($info['email'])->save();
            C::clean('CEM::' . $key);
            return $info;
        }
        return false;
    }
    
    
    /**
     * Réinitialisation du mot de passe
     * @param string $email
     */
    public static function retrievePasswordQuery($email)
    {
        // Jeton pour éviter qu'un utilisateur demande 1000 fois son mot de passe
        $token = self::getRpaToken($email);
        if (C::get($token)) {
            throw new DisplayedException(__("Une demande de renouvellement de mot de passe a déjà été demandée récemment. Veuillez attendre de recevoir l'email de réinitialisation ou faites une nouvelle demande plus tard."));
        }
        
        // Mise en cache d'un hash de vérification
        $verifKey = Crypt::hash(microtime() . rand(1000, 1000000));
        $verifVal = ['email' => $email];
        C::set('RPA::' . $verifKey, $verifVal, self::PASSWORD_DELAY);
        
        // Envoi du mail de confirmation
        (new Mail())
                ->addTo($email)
                ->setSubject(sprintf(__("[%s] Réinitialisation du mot de passe %s"), APP_SNAM, APP_NAME))
                ->addParagraph(__("Si vous avez demandé à réinitialiser votre mot de passe, cliquez sur le lien suivant :"))
                ->addLinkBullet(__("Réinitialiser mon mot de passe"), H::baseUrl(H::url('account', 'password', ['k' => $verifKey]), true))
                ->addParagraph(sprintf(__("Vous avez jusqu'au %s (1 heure) pour valider cette opération. Si vous n'avez pas demandé cette réinitialisation, vous pouvez ignorer ce message. Merci pour la confiance que vous témoignez à %s et à très bientôt !"), date(__("d/m/Y à H:i"), time() + (3540)), APP_NAME))
                ->send();
        
        // Log et mise en place du jeton
        Log::info(sprintf(__("Demande de renouvellement de mot de passe par %s."), $email), 'PASSWORD');
        C::set($token, true, self::PASSWORD_DELAY);
    }
    
    /**
     * Jeton pour éviter qu'un utilisateur demande 1000 fois son mot de passe
     * @param string $email
     * @return string
     */
    protected static function getRpaToken(string $email)
    {
        return 'RPA::TOK::' . $email;
    }
    
    /**
     * Valide le changement d'e-mail
     * @param string $key
     * @return boolean
     */
    public static function retrievePasswordCheck($key)
    {
        // Vérifie la clé et donne un délai supplémentaire de 5 minutes
        if ($value = C::get('RPA::' . $key)) {
            C::set('RPA::' . $key, $value, 5 * 60);
            return $value;
        }
        return false;
    }
    
    /**
     * Valide le changement d'e-mail
     * @param string $key
     * @return boolean
     */
    public static function retrievePasswordCommit($key, $email, $newPassword)
    {
        /* @var $row \Sma\Db\AccountRow */
        $row = DB::getAccountTable()->select(['email' => $email, 'status' => ['enabled', 'draft']])->current();
        if (!$row) {
            throw new DisplayedException(__("Ce compte n'existe pas ou a été désactivé. Impossible de modifier le mot de passe."));
        }
        if (!$row->setPassword(Crypt::passwordHash($newPassword))->setStatus('enabled')->save()) {
            throw new DisplayedException(__("Impossible de réinitialiser votre mot de passe."));
        }
        C::clean('RPA::' . $key);
        C::clean(self::getRpaToken($email));
        return true;
    }
}
