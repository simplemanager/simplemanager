<?php
namespace App\Product;

use Sma\Controller\Json as JsonAction;
use App\Product\Model\ProductDbManager as DM;
use H, L, DB;

/**
 * Espace administration
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
    const PRODUCT_SETTINGS = 'prsettings';
    
    public function init()
    {
        //$this->layout()->setPageTitle(__("Produits & Services"));
    }
    
    public function indexAction()
    {
        $this->redirect(H::url('product', 'list'));
    }
    
    public function listAction()
    {
        $form = new Form\FormFilter();
        $form->isPostedAndValid();
        return ['data' => DM::getProductsForTable($form->getValues()), 'formFilter' => $form];
    }
    
    public function viewAction()
    {
        $this->layout()->setPageTitle('');
        $withDecorations = $this->getParam('for') !== 'modal';
        $withDecorations || $this->disableLayout();
        return ['id' => $this->getParam('id'), 'decorations' => $withDecorations];
    }
    
    public function editAction()
    {
        $form = new Form\FormProduct();
        if ($id = $this->getParam('id')) {
            $product = DM::getProductForForm($id);
            $form->oldCodeValue($product['code']);
            $form->hydrate($product, null, true, true);
        }
        if ($form->isPostedAndValid()) {
            if ((!$id && DM::addProduct($form->getValues())) ||
                 ($id && DM::updateProduct($form->getValues(), $id))) {
                
                $tp = $this->getParam('tp') ?: 1;
                if ($id) {
                    if ($this->hasParam('tp')) {
                        $ajaxCall = "\$.ajaxCall('" . H::url('product', 'list', ['tp' => $tp]) . "', '#clist');";
                    } else {
                        $ajaxCall = "\$.ajaxCall('" . H::url('product', 'view', ['id' => $id, 'frsh' => 1]) . "');";
                    }
                    L::appendScripts("\$('#mform').modal('hide');$('body').removeClass('modal-open');$('.modal-backdrop').remove();" . $ajaxCall);
                
                    // L::appendScripts("\$('#mform').modal('hide');$.ajaxCall('" . H::url('product', 'list', ['tp' => $tp]) . "', '#clist');");
                } else {
                    $code = $form->getValue('code');
                    $form = new Form\FormProduct();
                    $msg = H::msg(__("Produit %s ajouté. Vous pouvez en saisir un autre ou fermer la fenêtre."));
                    $form->setHtmlBefore(sprintf($msg, $code));
                    L::appendScripts("\$.ajaxCall('" . H::url('product', 'list', ['tp' => $tp]) . "', '#clist');");
                }
            } else {
                $this->alertDanger(
                        __("Une erreur a été détectée lors de l'enregistrement de votre produit."), 
                        __("Ce problème a été envoyé à nos équipes qui font tout leur possible pour le régler. Toutes nos excuses pour la gêne occasionnée."));
            }
        }
        return ['form' => $form];
    }
    
    public function deleteAction()
    {
        $this->disableView();
        $id = $this->getParam('id');
        $tp = $this->getParam('tp') ?: 1;
        if (!DM::deleteProduct($id)) {
            $this->alertDanger(__("Impossible de supprimer ce produit"));
        } else {
            $this->ajaxCall(H::url('product', 'list', ['tp' => $tp]), '#clist');
        }
    }
    
    /**
     * Export des produit avec filtrage
     */
    public function exportAction()
    {
        $this->export(DB::getProductTable(), __("Mes produits"), (new Form\FormFilter())->getValues());
    }
    
    public function importAction()
    {
        $this->todo();
    }
}
