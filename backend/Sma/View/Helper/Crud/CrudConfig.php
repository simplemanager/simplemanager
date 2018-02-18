<?php
namespace Sma\View\Helper\Crud;

use Osf\Stream\Text as T;
use Osf\Exception\ArchException;
use Osf\View\Helper\Bootstrap\Tools\Checkers;
use Sma\Container;
use App\Common\Form\FormCrudFilter;
use Sma\Session\Identity as I;

/**
 * Configuration à insérer dans le crud pour la génération
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 2017
 * @package osf
 * @subpackage view
 */
class CrudConfig
{
    const PAGINATION_MOBILE   = 10;
    const PAGINATION_NOMOBILE = 10;
    
    const MSG_TITLE   = 'title';
    const MSG_NOITEM  = 'noitem';
    const MSG_ADD     = 'add';
    const MSG_ADDURL  = 'addurl';
    const MSG_DEL     = 'del';
    const MSG_DELDESC = 'deldesc';
    const MSG_VIEW    = 'view';
    
    const DELETE_AUTO   = 0;
    const DELETE_MENU   = 1;
    const DELETE_BUTTON = 2;
    const DELETE_MODAL  = 3;
    
    protected $icon         = 'list';   // Code de l'icone à afficher
    protected $actionDelete = 'delete'; // Action pour supprimer un item
    protected $actionEdit   = 'edit';   // Action pour éditer un item
    protected $actionView   = 'view';   // Action pour visualiser un item
    protected $itemTemplate = null;     // Template à utiliser pour chaque item
    protected $pagination   = false;    // Automatique par défaut 
    protected $links        = [];       // Liste des liens pour chaque item
    protected $urlParams    = [];       // Paramètre complémentaire à ajouter dans les urls (du delete pour l'instant)
    protected $formFilter   = null;     // Formulaire pour le filtrage de la liste
    protected $buttons      = '';       // Boutons complémentaires à afficher en haut à droite
    protected $titleLinks   = [];       // Liens complémentaires à afficher après le titre
    protected $linkPattern  = null;     // Pattern du lien de chaque item pour la table
    protected $trAttrs      = [];       // Attributs des balises 'tr'
    protected $linkBlank    = false;    // Ouvrir la cible du lien dans une nouvelle fenêtre
    protected $titleAppend  = '';       // Affiché à la suite du titre...
    protected $fieldParams  = [];       // Paramètres des champs pour le helper Table
    protected $linkAdd      = null;     // Url pour ajouter un item
    protected $refresh      = false;    // Appel de rafraichissement d'une liste existante
    protected $delete       = 0;        // Type de suppression
    protected $delCallback  = null;     // Fonction de callback permettant de savoir s'il faut afficher le bouton suppression
    protected $msg = [                  // Messages configurables
        self::MSG_TITLE   => null,
        self::MSG_NOITEM  => null,
        self::MSG_ADD     => null,
        self::MSG_ADDURL  => null,
        self::MSG_DEL     => null,
        self::MSG_DELDESC => null,
        self::MSG_VIEW    => null,
    ];
    
    protected $realDelete;
    protected $callbackMode = false;    // Appel callback de la fonction de génération des actions
    
    public function __construct()
    {
        $formFilter = Container::getViewHelper()->get('formFilter');
        if ($formFilter instanceof FormCrudFilter) {
            $this->setFormFilter($formFilter);
        }
    }
    
    /**
     * @param string $icon
     * @param string $color
     * @return $this
     */
    public function setIcon(string $icon, string $color = null)
    {
        Checkers::checkIcon($icon);
        is_null($color) || Checkers::checkColor($color);
        if ($color === null) {
            $this->icon = $icon;
        } else {
            $this->icon = [
                'icon' => $icon,
                'color' => $color
            ];
        }
        return $this;
    }

    /**
     * @return string
     */
    public function getIcon(): string
    {
        return is_array($this->icon) ? $this->icon['icon'] : $this->icon;
    }
    
    /**
     * @return string|null
     */
    public function getIconColor()
    {
        return is_array($this->icon) ? $this->icon['color'] : null;
    }
    
    /**
     * Action pour supprimer un item
     * @param string $actionDelete
     * @return $this
     */
    public function setActionDelete(string $actionDelete)
    {
        $this->actionDelete = (string) $actionDelete;
        return $this;
    }

    /**
     * @return string
     */
    public function getActionDelete(): string
    {
        return $this->actionDelete;
    }
    
    /**
     * Action pour éditer un item
     * @param string $actionEdit
     * @return $this
     */
    public function setActionEdit(string $actionEdit)
    {
        $this->actionEdit = (string) $actionEdit;
        return $this;
    }

    /**
     * @return string
     */
    public function getActionEdit(): string
    {
        return $this->actionEdit;
    }
    
    /**
     * Action pour visualiser un item
     * @param string $actionView
     * @return $this
     */
    public function setActionView(string $actionView)
    {
        $this->actionView = (string) $actionView;
        return $this;
    }

    /**
     * @return string
     */
    public function getActionView(): string
    {
        return $this->actionView;
    }
    
    /**
     * Template à utiliser pour chaque item
     * @param string $itemTemplate
     * @return $this
     */
    public function setItemTemplate(string $itemTemplate)
    {
        $this->itemTemplate = $itemTemplate;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getItemTemplate()
    {
        if ($this->itemTemplate === null) {
            $ctrl   = T::ucFirst(Container::getRequest()->getController());
            $action = Container::getRequest()->getAction();
            return APPLICATION_PATH . '/App/' . $ctrl . '/View/' . $action . '_item.phtml';
        }
        return $this->itemTemplate;
    }
    
    /**
     * Nombre d'items (Automatique par défaut)
     * @param int|null $pagination
     * @return $this
     */
    public function setPagination(?int $pagination)
    {
        $this->pagination = $pagination === null ? null : (int) $pagination;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getPagination()
    {
        if ($this->pagination === false) {
            $this->setPagination(Container::getDevice()->isMobile() 
                    ? self::PAGINATION_MOBILE 
                    : self::PAGINATION_NOMOBILE);
        }
        return $this->pagination;
    }
    
    /**
     * Liste des liens pour chaque item
     * @param array $links
     * @return $this
     */
    public function setLinks(array $links = [])
    {
        $this->links = $links;
        return $this;
    }

    /**
     * @return array
     */
    public function getLinks()
    {
        return $this->links;
    }
    
    /**
     * Paramètre complémentaire à ajouter dans les urls (du delete pour l'instant)
     * @param array $urlParams
     * @return $this
     */
    public function setUrlParams(array $urlParams = [])
    {
        $this->urlParams = $urlParams;
        return $this;
    }

    /**
     * @return array
     */
    public function getUrlParams()
    {
        return $this->urlParams;
    }
    
    /**
     * Formulaire pour le filtrage de la liste
     * @param FormCrudFilter $formFilter
     * @return $this
     */
    public function setFormFilter(FormCrudFilter $formFilter)
    {
        $this->formFilter = $formFilter;
        return $this;
    }
    
    /**
     * @return FormCrudFilter
     */
    public function getFormFilter()
    {
        return $this->formFilter;
    }
    
    /**
     * Boutons complémentaires à afficher en haut à droite
     * @param string|null $buttons
     * @return $this
     */
    public function setButtons(string $buttons)
    {
        $this->buttons = (string) $buttons;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getButtons()
    {
        return $this->buttons;
    }
    
    /**
     * Liens complémentaires à afficher après le titre
     * @param array $titleLinks
     * @return $this
     */
    public function setTitleLinks(array $titleLinks = [])
    {
        $this->titleLinks = $titleLinks;
        return $this;
    }

    /**
     * @return array
     */
    public function getTitleLinks()
    {
        return $this->titleLinks;
    }
    
    /**
     * Pattern du lien de chaque item pour la table
     * @param string|null $linkPattern
     * @return $this
     */
    public function setLinkPattern($linkPattern, bool $blank = false)
    {
        $this->linkPattern = $linkPattern;
        $this->linkBlank = $blank;
        return $this;
    }

    /**
     * @return mixed|null
     */
    public function getLinkPattern()
    {
        return $this->linkPattern;
    }
    
    /**
     * @return bool
     */
    public function getLinkBlank(): bool
    {
        return $this->linkBlank;
    }
    /**
     * @return array
     */
    public function getParamsKeys()
    {
        return $this->paramsKeys;
    }
    
    /**
     * @param array $trAttrs
     * @return $this
     */
    public function setTrAttrs(array $trAttrs = [])
    {
        $this->trAttrs = $trAttrs;
        return $this;
    }

    /**
     * @return array
     */
    public function getTrAttrs()
    {
        return $this->trAttrs;
    }
    
    /**
     * Ajout d'un attribut aux balises TR
     * @param string $attrName
     * @param callback|string $attrValue
     * @return $this
     */
    public function setTrAttr(string $attrName, $attrValue)
    {
        $this->trAttrs[$attrName] = $attrValue;
        return $this;
    }
    
    /**
     * Affiché à la suite du titre...
     * @param string|null $titleAppend
     * @return $this
     */
    public function setTitleAppend($titleAppend)
    {
        $this->titleAppend = $titleAppend === null ? null : (string) $titleAppend;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getTitleAppend()
    {
        return $this->titleAppend;
    }
    
    /**
     * Paramètres des champs pour le helper Table
     * @param array $fieldParams
     * @return $this
     */
    public function setFieldParams(array $fieldParams = [])
    {
        $this->fieldParams = $fieldParams;
        return $this;
    }

    /**
     * @return array
     */
    public function getFieldParams()
    {
        return $this->fieldParams;
    }
    
    /**
     * Url pour ajouter un item
     * @param string|null $linkAdd
     * @return $this
     */
    public function setLinkAdd($linkAdd)
    {
        $this->linkAdd = $linkAdd === null ? null : (string) $linkAdd;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getLinkAdd()
    {
        return $this->linkAdd;
    }
    
    /**
     * Appel de rafraichissement d'une liste existante (ne raffiche que la liste)
     * @param bool $refresh
     * @return $this
     */
    public function setRefresh($refresh = true)
    {
        $this->refresh = (bool) $refresh;
        return $this;
    }

    /**
     * @return bool
     */
    public function isRefresh():bool
    {
        return $this->refresh;
    }
    
    /**
     * @param int|null $deleteType
     * @return $this
     */
    public function setDelete($deleteType = self::DELETE_AUTO)
    {
        $this->delete = $deleteType === null ? self::DELETE_AUTO : (int) $deleteType;
        if (!in_array($this->delete, [
            self::DELETE_AUTO,
            self::DELETE_MENU,
            self::DELETE_BUTTON,
            self::DELETE_MODAL
        ])) {
            throw new ArchException('Delete type [' . $deleteType . '] unknown');
        }
        $this->realDelete = null;
        return $this;
    }

    /**
     * @return int
     */
    public function getDelete()
    {
        if ($this->realDelete === null) {
            $this->realDelete = $this->delete !== self::DELETE_AUTO ? $this->delete : (
                I::isLevelExpert() 
                ? self::DELETE_MENU
                : self::DELETE_MODAL
            );
        }
        
        return $this->realDelete;
    }
    
    /**
     * @param callable|null $delCallback
     * @return $this
     */
    public function setDelCallback(?callable $delCallback)
    {
        $this->delCallback = $delCallback;
        return $this;
    }
    
    /**
     * @return callable|null
     */
    public function getDelCallback(): ?callable
    {
        return $this->delCallback;
    }
    
    /**
     * Le type de suppression est-il $deleteType ? 
     * @param int $deleteType
     * @return bool
     */
    public function isDelete(int $deleteType)
    {
        return $this->getDelete() === $deleteType;
    }
    
    /**
     * Définit un message (cf. constantes MSG_xxx)
     * @param string $key
     * @param string $value
     * @return $this
     */
    public function setMsg(string $key, $value = null)
    {
        if (!array_key_exists($key, $this->msg)) {
            throw new ArchException('No key [' . $key . '] for crud messages');
        }
        $this->msg[$key] = $value === null ? null : (string) $value;
        return $this;
    }
    
    /**
     * @param string $key
     * @return string|null
     */
    public function msg($key)
    {
        return isset($this->msg[$key]) ? $this->msg[$key] : null;
    }
    
    /**
     * Définit les messages (cf. constantes MSG_xxx)
     * @param array $msgs
     * @return $this
     */
    public function setMsgs(array $msgs)
    {
        foreach ($msgs as $key => $value) {
            $this->setMsg($key, $value);
        }
        return $this;
    }
    
    /**
     * @param bool $callbackMode
     * @return $this
     */
    public function setCallbackMode($callbackMode = true)
    {
        $this->callbackMode = (bool) $callbackMode;
        return $this;
    }

    /**
     * @return bool
     */
    public function getCallbackMode(): bool
    {
        return $this->callbackMode;
    }
}