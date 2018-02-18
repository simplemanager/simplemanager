<?php 
namespace Sma\View\Helper;

use Osf\View\Helper\Bootstrap\AbstractViewHelper as AVH;
use Osf\View\Helper\Bootstrap\Addon\DropDownMenu;
use H;

/**
 * Affichage des warnings liés à un bean
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 2017
 * @package osf
 * @subpackage view
 */
class ExchangeButton extends AVH
{
    const FORMATS = [
        'csv'  => ['icon' => 'file-text-o',  'color' => 'blue'], 
        'xls'  => ['icon' => 'file-excel-o', 'color' => 'red'], 
        'xlsx' => ['icon' => 'file-excel-o', 'color' => 'orange'], 
        'ods'  => ['icon' => 'file-excel-o', 'color' => 'green']
    ];
    
    protected $filePrefix;
    protected $controller;
    protected $exportAction;
    protected $importAction;
    
    /**
     * Affiche le bouton d'import/export avec le menu correspondant
     * @param string $filePrefix
     * @param string $controller
     * @param string $action
     * @return \Sma\View\Helper\ExchangeButton
     */
    public function __invoke(string $filePrefix, string $controller, string $exportAction, ?string $importAction = null)
    {
        $this->filePrefix = $filePrefix;
        $this->controller = $controller;
        $this->exportAction = $exportAction;
        $this->importAction = $importAction;
        return $this;
    }

    /**
     * @return string
     */
    public function render()
    {
        $fileName = date('Ymd-hi-') . $this->filePrefix;
        $ddmenu = (new DropDownMenu())->alignRight();
        foreach (self::FORMATS as $format => $params) {
            $ddmenu->addLink(H::iconCached($params['icon'], null, $params['color']) . sprintf(__("Export %s"), strtoupper($format)),  H::url($this->controller, $this->exportAction) . '/' . $fileName . '.' . $format,  false, [], ['extlink']);
        }
        if ($this->importAction) {
            $ddmenu->addSeparator()->addLink(H::iconCached('download', null, 'fuchsia') . __("Importer..."), H::url($this->controller, $this->importAction));
        }
        return (string) H::button()
            ->icon('exchange')
            ->marginLeft()
            ->statusPrimary()
            ->setMenu($ddmenu);
    }
}
