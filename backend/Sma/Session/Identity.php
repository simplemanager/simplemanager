<?php
namespace Sma\Session;

use Osf\Session\Container;
use Osf\Helper\Tab;
use Sma\Bean\ContactBean;

/**
 * Identity of current user
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 2017
 * @package sma
 * @subpackage session
 */
abstract class Identity extends Container
{
    const SECTION_CONTACT    = 'contact'; // Infos personnelles de l'utilisateur courant
    const SECTION_COMPANY    = 'company'; // Entreprise de l'utilisateur courant
    const SECTION_ACCOUNT    = 'account'; // Paramètres du compte (id, mail...)
    const SECTION_PARAMS     = 'params';  // Paramètres complémentaires (profile/xxx.yml)
    const SECTION_LOGO_COLOR = 'color';   // Couleur dominante du logo
    
    protected static $namespace = 'smai';
    
    /**
     * Is current user logged ?
     * @return bool
     */
    public static function isLogged(): bool
    {
        return (bool) self::get('id');
    }
    
    /**
     * Id du compte courant SMA
     * @return int
     */
    public static function getIdAccount()
    {
        return self::get('id');
    }
    
    /**
     * Contact lié au compte courant
     * @return array
     */
    public static function getContact()
    {
        return self::get('contact');
    }
    
    /**
     * Identity contact bean lazy loaded
     * @return ContactBean
     * @throws ArchException
     */
    public static function getContactBean()
    {
        $bean = self::get('contact_bean');
        
        if (!$bean) {
            $bean = ContactBean::buildContactBeanFromCompanyId(self::getIdCompany(), true);
            self::set('contact_bean', $bean);
        }
        
        if (!($bean instanceof ContactBean)) {
            throw new ArchException('Contact bean not found in user session');
        }
        
        return $bean;
    }
    
    /**
     * En cas de changement potentiel dans les informations contact, on supprime le bean de la session
     * @return \Osf\Session\AppSession
     */
    public static function resetContactBean()
    {
        return self::clean('contact_bean');
    }
    
    /**
     * @param array $values
     * @return \Osf\Session\AppSession
     */
    public static function hydrate(array $values) {
        self::resetContactBean();
        return parent::hydrate($values);
    }

    /**
     * Id de la table contact lié au compte courant
     * @return int
     */
    public static function getIdContact()
    {
        $contact = self::getContact();
        return is_array($contact) && isset($contact['id_contact']) ? (int) $contact['id_contact'] : null;
    }
    
    /**
     * Entreprise liée au compte courant
     * @return array
     */
    public static function getCompany()
    {
        return self::get('company');
    }
    
    /**
     * Id de l'entreprise liée au compte courant
     * @return int
     */
    public static function getIdCompany()
    {
        $company = self::getCompany();
        return is_array($company) && isset($company['id']) ? (int) $company['id'] : null;
    }
    
    /**
     * Construit et retourne le nom complet de l'utilisateur courant
     * @return string
     */
    public static function getFullname()
    {
        return trim(self::get('contact', 'civility') . ' ' . trim(self::get('firstname') . ' ' . self::get('lastname')));
    }
    
    /**
     * User params
     * @param string $category
     * @return array|null
     */
    public static function getParams(string $category = null)
    {
        $params = self::get('params');
        if ($category !== null) {
            if (is_array($params) && array_key_exists($category, $params)) {
                return $params[$category];
            }
            return null;
        }
        return $params;
    }
    
    /**
     * Valeur d'un paramètre de l'utilisateur courant
     * @param string $category
     * @param string $key
     * @return mixed
     */
    public static function getParam(string $category, string $key, $valueIfNull = null)
    {
        $params = self::getParams();
        return is_array($params) && isset($params[$category][$key]) 
               ? $params[$category][$key] 
               : $valueIfNull;
    }
    
    /**
     * Calcule et retourne la couleur alternative à utiliser pour les documents
     * @param string $default
     * @return string|null
     * @throws \Osf\Exception\ArchException
     */
    public static function getColor(string $default = null)
    {
        switch (self::getParam('document', 'color', 'logo')) {
            case 'nb' : 
                return null;
            case 'alt' :
                return self::getParam('document', 'coloralt', $default);
            case 'logo' :
                return self::get(self::SECTION_LOGO_COLOR) ?: $default;
            default : 
                throw new \Osf\Exception\ArchException('Unknown color type');
        }
    }
    
    /**
     * La TVA s'applique-t-elle à l'entreprise courante ?
     * i.e. l'entreprise facture avec la franchise en base de TVA
     * @return bool
     */
    public static function hasTax():bool
    {
        return !self::getParam('company', 'taxfranch');
    }
    
    /**
     * Timestamp de la dernière action effectuée
     * @return int
     */
    public static function getTimestampLast()
    {
        return self::get('ts_last');
    }
    
    /**
     * Le warning d'inactivité a-t-il été envoyé ?
     */
    public static function getTimestampWarn()
    {
        return self::get('ts_warn');
    }
    
    /**
     * Le warning d'inactivité a-t-il été envoyé ?
     * @param bool $warn
     */
    public static function setTimestampWarn(bool $warn = true)
    {
        self::set('ts_warn', $warn);
    }
    
    /**
     * Mise à jour du timestamp permettant de calculer la durée d'inactivité
     */
    public static function updateTimestamp()
    {
        self::set('ts_last', time());
        self::setTimestampWarn(false);
    }
    
    /**
     * @return bool
     */
    public static function isLevelExpert()
    {
        return self::getParam('interface', 'level') === 'expert';
    }
    
    /**
     * @return bool
     */
    public static function isLevelBeginner()
    {
        return self::getParam('interface', 'level') === 'easy';
    }
    
    /**
     * Liste des fonctionnalités optionnelles de l'utilisateur courant
     * @return array
     */
    public static function getFeatures()
    {
        $features = [];
        $featuresFromParams = self::getParams('features');
        if (!$featuresFromParams) {
            return [];
        }
        foreach ($featuresFromParams as $key => $activated) {
            if ($activated) {
                $features[] = $key;
            }
        }
        return $features;
    }
    
    /**
     * Calcule et renvoit le pourcentage de remplissage du compte courant
     * @param string $section
     * @param bool $privateData intégrer les données privées dans le calcul
     * @return int
     * @throws \Osf\Exception\ArchException
     */
    public static function getCompletion(string $section = null, bool $privateData = false)
    {
        $vals = [];
        $totalCount = 0;
        if ($section !== null && !in_array($section, [self::SECTION_CONTACT, self::SECTION_COMPANY])) {
            throw new \Osf\Exception\ArchException('Bad section [' . $section . ']');
        }
        
        if ($section === null || $section === self::SECTION_CONTACT) {
            $keys = ['civility', 'firstname', 'lastname', 'gsm', 'email', 'function'];
            if ($privateData) {
                $keys = array_merge($keys, ['tel', 'fax', 'address', 'city', 'country']);
            }
            $vals[self::SECTION_CONTACT]['c'] = count($keys);
            $vals[self::SECTION_CONTACT]['p'] = 
                    is_array(self::get(self::SECTION_CONTACT)) ? 
                    Tab::getPercentage(self::get(self::SECTION_CONTACT), $keys) : 
                    0;
            $totalCount += $vals[self::SECTION_CONTACT]['c'];
        }
        
        if ($section === null || $section === self::SECTION_COMPANY) {
            $keys = ['title', 'tel', 'fax', 'email', 
                'description', 'url', 'id_address', 'url', 'id_logo'];
            $vals[self::SECTION_COMPANY]['c'] = count($keys);
            $vals[self::SECTION_COMPANY]['p'] = 
                    is_array(self::get(self::SECTION_COMPANY)) ? 
                    Tab::getPercentage(self::get(self::SECTION_COMPANY), $keys) : 
                    0;
            $totalCount += $vals[self::SECTION_COMPANY]['c'];
        }
        
        if ($totalCount === 0) {
            return 0;
        }
        
        $completion = 0;
        foreach ($vals as $val) {
            $completion += $val['p'] * ($val['c'] / $totalCount);
        }
        
        return max(0, min(100, floor($completion)));
    }
    
    public static function getAclRole()
    {
        return self::get('acl_role');
    }
}
