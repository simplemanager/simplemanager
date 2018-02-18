<?php 
namespace Sma\View\Helper;

use Osf\View\Helper\Bootstrap\AbstractViewHelper as AVH;
use Osf\View\Helper\Bootstrap\Addon\DropDownMenu;
use Osf\View\Helper\Bootstrap\Icon;
use Osf\View\Helper\Bootstrap\Box;
use Osf\View\Table;
use Osf\Application\OsfApplication as Application;
use Osf\View\Component;
use Sma\View\Helper\Crud\CrudConfig;
use Sma\Session\Identity as I;
use App\Common\Container;
use H;

/**
 * Crud
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 2017
 * @package osf
 * @subpackage view
 */
class Crud extends AVH
{
    /**
     * @var CrudConfig
     */
    protected $config;
    
    /**
     * @var Box
     */
    protected $box;
    
    /**
     * @param Box $box
     * @return $this
     */
    public function setBox(Box $box)
    {
        $this->box = $box;
        return $this;
    }
    
    /**
     * @return Box|null
     */
    public function getBox(): ?Box
    {
        return $this->box;
    }
    
    /**
     * @param CrudConfig $config
     * @return \Sma\View\Helper\Crud
     */
    public function __invoke(CrudConfig $config)
    {
        $this->config = $config;
        $this->box = null;
        return $this;
    }

    /**
     * Render the box at the end of configuration
     * @return string
     */
    public function render()
    {
        $controller     = Container::getRequest()->getController();
//        $mobileOrTablet = Container::getDevice()->isMobileOrTablet();
        $operation      = Container::getRequest()->getParam('do');
        $c              = $this->config;
        $hasDel         = $c->msg($c::MSG_DEL) && $c->msg($c::MSG_DELDESC);
        $title          = $c->msg($c::MSG_TITLE);
        $fieldParams    = $c->getFieldParams();
        $html           = '';

        // Pagination

        // Désactivation du layout pour la pagination
        $c->isRefresh() && Container::getApplication()->setDispatchStep(Application::RENDER_LAYOUT, false);

        // Si le layout est désactivé, alors c'est du HTML qui est directement renvoyé
        $c->isRefresh() && Container::getResponse()->setTypeHtml();

        // Premier appel : génération des modals et des boutons
        if (!$c->isRefresh()) {

            // Modal du formulaire de saisie
            if ($c->msg($c::MSG_ADD) && !$c->getLinkAdd()) {
                $html .= H::modal(
                    'mform',
                    $c->msg($c::MSG_ADD), 
                    H::htmlCached('crud_medit', '', 'div', ['id' => 'medit']),
                    clone H::button(__("Fermer"))->statusDefault()->setAttribute('data-dismiss', 'modal') . 
                    clone H::button(__("Enregistrer"))
                            ->setAttribute('type', 'submit')
                            ->setAttribute('onclick', "\$.ajaxCall(\$('#fedit'),'#medit',false,true);")
                            ->statusPrimary());
            }

            // Visualisation
            if ($c->msg($c::MSG_VIEW)) {
                $html .= H::modal(
                    'mview', 
                    $c->msg($c::MSG_VIEW), 
                    H::htmlCached('crud_mvc', '', 'div', ['id' => 'mvc']),
                    clone H::button(__("Fermer"))->statusDefault()->setAttribute('data-dismiss', 'modal'));
            }

            // Ecran de confirmation de suppression
            if ($hasDel && $c->isDelete($c::DELETE_MODAL)) {
                $html .= H::modal(
                    'mdel', 
                    $c->msg($c::MSG_DEL),
                    H::html($c->msg($c::MSG_DELDESC)),
                    clone H::button(__("Annuler"))->statusDefault()->setAttribute('data-dismiss', 'modal') . 
                    clone H::button(__("Supprimer"))
                            ->setAttribute('onclick', "$('#mdel').modal('hide');$.ajaxCall('" . H::url($controller, $c->getActionDelete()) . "/id/' + window.dId + '/tp/' + window.dTp,'#clist',false,true);")
                            ->statusDanger())
                        ->statusDanger()
                        ->setSizeSmall();
            }

            if ($c->getFormFilter() && I::getParam('interface', 'lform') !== 'no') {
                $html .= H::form($c->getFormFilter())->setTargetDefault();
            }
        }

        // Bouton d'ajout 
        if ($c->msg($c::MSG_ADD)) {
            //$elts[] = (string) H::button()
            $button = H::button(null, $c->getLinkAdd())
                    ->icon('plus')
                    ->setTooltip($c->msg($c::MSG_ADD))
                    //->marginBottom()
                    //->addCssClass('pull-right')
                    ->marginLeft()
                    ->statusPrimary();
            $addLink = H::html(__("ajouter"), 'a');
            if (!$c->getLinkAdd()) {
                $jsAdd = "$('#mform').modal('show');$('#medit').html('');$.ajaxCall('" . H::url($controller, $c->getActionEdit(), ['for' => 'modal']) . "','#medit',false,true);";
                $button->setAttribute('onclick', $jsAdd);
                $addLink->setAttribute('onclick', $jsAdd)->setAttribute('href', '#');
            } else {
                $addLink->setAttribute('href', $c->getLinkAdd());
            }
            // $titleLinks[] = (string) $addLink;
            // $buttons .= $button;
            $title .= (string) $button;
        }
        if ($operation === 'add') {
            Container::getJsonRequest()->appendScripts($jsAdd);
        }

        // Ajouts après le titre
        $title .= $c->getTitleAppend();

        // Génération de la table
        if (H::get('data')->current()) {
            
            // Construction des données
            if (!isset($fieldParams['id'][Table::FP_DISPLAY])) {
                $fieldParams['id'][Table::FP_DISPLAY] = false;
            }
            $dataTable = new Table(H::get('data'));
            $dataTable
                ->setPaginate(true, $c->getPagination())
                ->setFieldParams($fieldParams);
            if ($c->getLinkPattern() !== null) {
                $dataTable->setLinkPattern($c->getLinkPattern(), 'id', null, null, $c->getLinkBlank());
            }
            $dataTable->setTrAttrs($c->getTrAttrs());
            
            // Actions
            if ($c->getCallbackMode()) {
                $dataTable->setActionCallback(function ($c, $row) { return self::buildRowMenu($c, $row); }, [$c]);
            } else {
                $action = self::buildRowMenu($c);
                $action && $dataTable->setAction($action);
            }
        }

        // Pas d'item
        else {
            unset($dataTable);
            $noItemMsg = $c->getFormFilter() && $c->getFormFilter()->hasValue() ? __("Votre recherche ne donne aucun résultat.") : H::html($c->msg($c::MSG_NOITEM));
        }

        // Affichage de la table (200px de margin bottom pour laisser de la place aux menus déroulants)
        if (!$c->isRefresh()) { $html .= '<div id="clist" style="margin-bottom: 350px">'; }
        $titleLinksStr = $c->getTitleLinks() ? H::html(' [ ' . implode(' | ', $c->getTitleLinks()) . ' ]')->escape(false)->mobileExclude() : '';
        
        /* @var $box Box */
        $box = $this->getBox() ?? H::box('');
        $box->setTitle($box->getTitle() ?: $title . $titleLinksStr)
            ->setContent($box->getContent() ?: (isset($noItemMsg) ? $noItemMsg : null))
                ->icon($c->getIcon(), $c->getIconColor())
                ->setLargeHeader();
        if ($c->getFormFilter() && $c->getFormFilter()->hasFilledElt()) {
            $box->addCssClass('filled');
        }
        if (isset($dataTable)) {
            $htmlTable = H::table($dataTable)
                ->setResponsive(false)
                ->setItemTemplate($c->getItemTemplate())
                ->setHeader($c->getButtons());
            $box->addTable($htmlTable); //->coloredTitleBox();
        } else {
            $box->setHeader($c->getButtons());
        }

        $html .= $box;
        
        // On réactive les événements VueJS en cas de pagination...
        if ($c->isRefresh()) {
            $html .= H::html(Component::getVueJs()->getAjaxScripts(), 'script')->escape(false);
        }
        
        if (!$c->isRefresh()) { $html .= '</div>'; }
        
        return $html;
    }
    
    public static function buildRowMenu(CrudConfig $c, ?array $row = null): string
    {
        $cm = $c->getCallbackMode();
        $idTag = $cm ? $row['id'] : '{{id}}';
        $hasDel = $c->msg($c::MSG_DEL) && $c->msg($c::MSG_DELDESC) && (!$cm || ($c->getCallbackMode() && $c->getDelCallback() ? $c->getDelCallback()($row) : true));
        $controller = Container::getRequest()->getController();
        $mobileOrTablet = Container::getDevice()->isMobileOrTablet();
        
        $tp = Container::getRequest()->getParam('tp') ?: 1;
        $jsView = $c->msg($c::MSG_VIEW) ? "$('#mview').modal('show');$('#mvc').html('');$.ajaxCall('" . H::url($controller, $c->getActionView(), ['for' => 'modal', 'id' => $idTag]) . "','#mvc',false,true);" : null;
        $jsEdit = $c->msg($c::MSG_ADD) && !$c->getLinkAdd() ? "$('#mform').modal('show');$('#medit').html('');$.ajaxCall('" . H::url($controller, $c->getActionEdit(), ['for' => 'modal', 'id' => $idTag, 'tp' => $tp]) . "','#medit',false,true);" : null;
        $jsDel  = $hasDel && !$c->isDelete($c::DELETE_BUTTON) ? ($c->isDelete($c::DELETE_MENU) 
                ? "$.ajaxCall('" . H::url($controller, $c->getActionDelete(), array_merge($c->getUrlParams(), ['id' => $idTag, 'tp' => $tp])) . "');"
                : "window.dId=" . $idTag . ";window.dTp=" . $tp . ";$('#mdel').modal('show');"
            ) : null;
        $delLabel = $jsDel ? (I::isLevelExpert() ? __("Supprimer définitivement") : __("Supprimer")) : '';
        $hasAction = $jsView || $jsEdit || $jsDel || $c->getLinks();

        if (!$hasAction) {
            return '';
        }

        // Il y a des liens complémentaires
        $hasLinks = (bool) $c->getLinks();

        // Menu de confirmation de suppression (alternative à l'écran de confirmation)
        if ($hasDel && $c->isDelete($c::DELETE_BUTTON)) {
            $supprDd = $c->msg($c::MSG_DEL) ? (new DropDownMenu())
                ->alignRight()
                ->addHeader(__("Êtes-vous sûr ?"))
                ->addSeparator()
                ->addLink(__("Oui, supprimer définitivement"), '#', false, ['onclick' => $jsDel, 'style' => 'color: red']) : null;
        }

        // Construction du menu principal 

        // Liens sous forme de liste déroulante (petit écran)
        $dd = (new DropDownMenu())->alignRight();
        $jsView && $dd->addLink(H::iconCached('eye')    . __("Détails"),  '#', false, ['onclick' => $jsView]);
        $jsEdit && $dd->addLink(H::iconCached('pencil') . __("Modifier"), '#', false, ['onclick' => $jsEdit]);
        $hasLinks && $jsView && $jsEdit && $dd->addSeparator();
        $links = [];
        foreach ($c->getLinks() as $link) {
            
            // Séparateur
            if (!is_array($link)) { 
                if (self::callableOrNot($link, $row, $cm) !== false) {
                    $dd->addSeparator();
                    $links[] = '';
                }
                continue;                
            }
            
            // Callback de filtrage (affichage ou non)
            if (isset($link['filter']) && !self::callableOrNot($link['filter'], $row, $cm)) {
                continue;
            }
            
            // Elements du lient
            $icon = (string) H::iconCached(
                    self::callableOrNot($link['icon'], $row, $cm), 
                    isset($link['status']) ? self::callableOrNot($link['status'], $row, $cm) : null, 
                    isset($link['color']) ? self::callableOrNot($link['color'], $row, $cm) : null);
            $label = $icon . H::html(self::callableOrNot($link['label'], $row, $cm));
            $link['label'] = $label;
            $link['url'] = self::callableOrNot($link['url'], $row, $cm);
            $link['attrs'] = self::callableOrNot($link['attrs'], $row, $cm);
            $links[] = $link;
            
            // Ajout du lien dans le menu
            $dd->addLink($label,$link['url'], false, $link['attrs']);
        }
        ($jsView || $jsEdit || $c->getLinks()) && $jsDel && $dd->addSeparator();
        $jsDel && $dd->addLink(H::iconCached('times', 'danger') . $delLabel, '#', false, ['onclick' => $jsDel, 'style' => 'color: red']);
        $action = H::html((string) clone H::button()->setMenu($dd), 'span')->escape(false);
        $mobileOrTablet || $action->addCssClass('visible-xs');

    // Liens sous forme de boutons
//                $buttons  = (string) clone H::button()->icon('eye')->setAttribute('onclick', $jsView)->statusPrimary()->marginLeft();
//                $buttons .= (string) clone H::button()->icon('pencil')->setAttribute('onclick', $jsEdit)->statusPrimary()->marginLeft();
//                $buttons .= (string) clone H::button()->icon('times')->statusDanger()->marginLeft();
//                $action .= H::html($buttons, 'span')->escape(false)->addCssClass('hidden-xs');

        // Liens sous forme d'icônes (grands écrans)
        if (!$mobileOrTablet) {

            // Menu d'actions complémentaires
            if ($hasLinks || $jsDel) {
                $linksDd = (new DropDownMenu())->alignRight();
                foreach ($links as $link) {
                    if (!is_array($link)) { $linksDd->addSeparator(); continue; }
                    $link['css'] = isset($link['css']) ? $link['css'] : [];
                    $linksDd->addLink($link['label'], $link['url'], false, $link['attrs'], $link['css']);
                }
                $hasLinks && $jsDel && $linksDd->addSeparator();
                $jsDel && $linksDd->addLink(H::iconCached('times', 'danger') . $delLabel, '#', false, ['onclick' => $jsDel, 'style' => 'color: red'], ['crud-del']);
            } else {
                $linksDd = null;
            }

            $actions = [];
            $jsView && $actions[] = (string) self::icon('eye')
                    ->padding(6)
                    ->statusPrimary()
                    ->setTooltip(__("Détails"))
                    ->addCssClass('clickable')
                    ->setAttribute('onclick', $jsView);
            $jsEdit && $actions[] = (string) self::icon('pencil')
                    ->padding(6)
                    ->statusPrimary()
                    ->setTooltip(__("Modifier"))
                    ->addCssClass('clickable')
                    ->setAttribute('onclick', $jsEdit);
//            foreach ($links as $link) {
//                if (!is_array($link)) { continue; }
//                $actions[] = (string) clone H::icon($link['icon'])
//                    ->padding(6)
//                    ->statusPrimary()
//                    ->appendStyle('cursor: pointer;')
//                    ->url($link['url'])
//                    ->setTooltip($link['label'])
//                    ->setAttributes($link['attrs']);
//            }
            $linksDd && $actions[] = (string) self::icon('bars')
                    ->padding(6)
                    ->statusPrimary()
                    ->addCssClass('clickable')
                    ->setMenu($linksDd);

            $hasDel && $c->isDelete($c::DELETE_BUTTON) && $actions[] = (string) self::icon('times')
                    ->padding(6)
                    ->statusDanger()
                    ->appendStyle('cursor: pointer;')
                    ->setMenu($supprDd);

            $action .= H::html(implode('', $actions), 'span')->escape(false)->addCssClass('hidden-xs');
        } 
        return $action;
    }
    
    /**
     * Execute la fonction s'il s'agit d'un callback
     * @param mixed $data
     * @param array $row
     * @param bool $callableMode
     * @return mixed
     */
    protected static function callableOrNot($data, ?array $row, bool $callableMode)
    {
        if ($callableMode && !is_string($data) && is_callable($data)) {
            return $data($row);
        }
        return $data;
    }
    
    /**
     * Limitation de l'invocation des icones
     * @return Icon
     */
    protected static function icon($type): Icon
    {
        return H::icon($type);
    }
}
