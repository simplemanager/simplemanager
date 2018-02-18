<?php
namespace App\Info;

use Osf\Container\OsfContainer as Container;
use Osf\Exception\ArchException;
use Osf\Exception\DisplayedException;
use Sma\Controller\Json as JsonAction;
use Sma\Session\Identity;
use Osf\View\Helper\Bootstrap\Help;
use H;

/**
 * Pages statiques d'informations
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 20 déc. 2013
 * @package common
 * @subpackage controllers
 */
class Controller extends JsonAction
{
    protected $pagesDir = null;
    
    public function init()
    {
        H::layout()->setPageTitle('Informations')
                   ->addBreadcrumbLink('informations', H::url('info'));
    }
    
    public function indexAction()
    {
        $this->redirect(H::url('info', 'book'));
    }
    
    public function displayAction()
    {
        // Vérification des paramètres
        $params = Container::getRequest()->getParams();
        if (!array_key_exists('document', $params)) {
            throw new ArchException('Bad request');
        }
        $document = $params['document'];
        $modalId = null;
        
        // Pas de layout si c'est un help (modal)
        $isHelp = substr($document, 0, 5) == 'help/';
        if ($isHelp) {
            $this->disableLayout();
            $modalId = Help::hash(substr($document, 5));
        }
        
        // Il faut être logué pour voir le guide
        if ($document === 'book' && !Identity::isLogged()) {
            throw new DisplayedException(__("Veuillez vous identifier pour accéder à ces informations."));
        }
        
        // Génération du document
        if (!$this->pagesDir) {
            $this->pagesDir = __DIR__ . '/pages/';
        }
        if (!preg_match('/^[a-z0-9\/_-]+$/', $document)) {
            throw new ArchException('Wrong document syntax');
        }
        H::layout()->addBreadcrumbLink($document, H::url('info') . '/' . $document);
        $file = $this->pagesDir . $document . '.md';
        if (!file_exists($file)) {
            $content = [
                'title' => __("Documentation inexistante"),
                'content' => __("Cette documentation n'est pas encore disponible, veuillez nous excuser pour la gêne occasionnée.")
            ];
        } else {
            $content = Container::getMarkdown()->file($file, $isHelp ? '' : '# ');
        }
        
        return ['content' => $content, 'modalId' => $modalId, 'isHelp' => $isHelp];
    }
}
