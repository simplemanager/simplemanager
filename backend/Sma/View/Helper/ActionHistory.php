<?php 
namespace Sma\View\Helper;

use Osf\View\Helper\Bootstrap\AbstractViewHelper as AVH;
use Zend\Db\ResultSet\ResultSetInterface as ResultSet;
use Osf\Helper\Mysql;
use Sma\Bean\DocumentBeanInterface as DocumentBean;
use Sma\Db\DocumentEventTable as DET;
use H, DateTime;

/**
 * Status label with menu
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 2017
 * @package osf
 * @subpackage view
 */
class ActionHistory extends AVH
{
    protected $actions = '';

    /**
     * Construit une liste HTML d'historique d'événements
     * @param ResultSet $eventRows
     * @param DocumentBean $bean
     * @return string
     */
    public function __invoke(ResultSet $eventRows, DocumentBean $bean)
    {
        /* @var $eventRow \Sma\Db\DocumentEventRow */
        $actions = '';
        $vcount = 0;
        $oldDocHistory = null;
        foreach ($eventRows as $eventRow) {
            $docHistory = $eventRow->getIdDocumentHistory();
            if ($docHistory === null) {
                $icon = H::icon('warning', null, 'red')->setTooltip(__("Pas d'historique"), 'left');
            } else {
                if ($docHistory !== $oldDocHistory) {
                    $vcount++;
                    $oldDocHistory = $docHistory;
                    $date = new DateTime($eventRow->getDate());
                    $icon = H::icon('file-pdf-o', null, 'red')->setTooltip('V' . $vcount, 'left');
                } else {
                    $icon = ''; // H::iconCached('ellipsis-v');
                }
                $filename = $bean->buildFileName($vcount, $date);
                $icon = $icon ? H::link($icon, 'document', 'export', ['dh' => $docHistory, 'file' => $filename])->setAttribute('target', '_blank')->addCssClass('extlink') : '&nbsp;';
            }
            $icon = H::html($icon, 'div')->escape(false)->setAttribute('style', 'width: 20px;display: inline-block; text-align: center');
            $comment = $eventRow->getComment() ? ' ' . H::html('(' . $eventRow->getComment() . ')', 'span')->addCssClass('text-gray')->mobileExclude() : '';
            $actions .= trim($icon . ' ' . Mysql::formatDateTime($eventRow->getDate(), true)) . ' : ' . DET::getEventMessage($eventRow->getEvent(), $bean->getType()) . $comment . '<br />';
        }
        return $actions;
    }
    
    /**
     * @return string
     */
    public function render()
    {
        return $this->actions;
    }
}