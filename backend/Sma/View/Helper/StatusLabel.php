<?php 
namespace Sma\View\Helper;

use Osf\View\Helper\Bootstrap\AbstractViewHelper as AVH;
use Osf\View\Helper\Bootstrap\Addon\DropDownMenu;
use Sma\Bean\InvoiceBean as IB;
// use Sma\Session\Identity;
use H;

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
class StatusLabel extends AVH
{
    protected $label = '';
    
    /**
     * Construit un bouton-menu pour changement de statut
     * 
     * @staticvar array $labels
     * @staticvar array $txt
     * @param int $id
     * @param string $currentStatus
     * @param string $type
     * @param string $baseUrl
     * @return \Sma\View\Helper\StatusLabel
     */
    public function __invoke(
            int     $id, 
            ?string $currentStatus, 
            string  $type, 
            string  $baseUrl,
            bool    $reduceForMobile = true,
            ?string $labelUrl = null)
    {
        static $labels = [];
        static $txt = [];
        
        $mobile = (int) (bool) $reduceForMobile;
        $currentStatus = $currentStatus ?: IB::STATUS_CREATED;
        $statuses = IB::getStatusColors(false, $type);
        $txt['l'] = IB::getStatusLetters($type);
        $txt['n'] = IB::getStatusNames($type);
//        if ($type === IB::TYPE_INVOICE && Identity::isLevelBeginner()) {
//            $txt['n'][IB::STATUS_PROCESSED] = __("Payé");
//        }
        
        // Droits
        $toCrea = isset(IB::ALLOWED_OWNER_ACTIONS[$type][$currentStatus][IB::STATUS_CREATED]);
        $toSent = isset(IB::ALLOWED_OWNER_ACTIONS[$type][$currentStatus][IB::STATUS_SENT]);
        $toRead = isset(IB::ALLOWED_OWNER_ACTIONS[$type][$currentStatus][IB::STATUS_READ]);
        $toProc = isset(IB::ALLOWED_OWNER_ACTIONS[$type][$currentStatus][IB::STATUS_PROCESSED]);
        $toCanc = isset(IB::ALLOWED_OWNER_ACTIONS[$type][$currentStatus][IB::STATUS_CANCELED]);
        $fromCrea = (bool) IB::ALLOWED_OWNER_ACTIONS[$type][IB::STATUS_CREATED];
        $fromSent = (bool) IB::ALLOWED_OWNER_ACTIONS[$type][IB::STATUS_SENT];
        $fromRead = (bool) IB::ALLOWED_OWNER_ACTIONS[$type][IB::STATUS_READ];
        $fromProc = (bool) IB::ALLOWED_OWNER_ACTIONS[$type][IB::STATUS_PROCESSED];
        $fromCanc = (bool) IB::ALLOWED_OWNER_ACTIONS[$type][IB::STATUS_CANCELED];
        $hasMenu = $toCrea || $toSent || $toRead || $toProc || $toCanc;
        
        if (!isset($labels[$type][$mobile])) {
            if ($reduceForMobile) {
                $labels[$type][$mobile] = [
                    IB::STATUS_CREATED   => H::html((string) H::html($txt['n'][IB::STATUS_CREATED  ] . ($fromCrea ? ' ▼' : ''))->addCssClass('l100')->mobileExclude() . (string) H::html($txt['l'][IB::STATUS_CREATED  ])->mobileOnly())->escape(false)->addCssClasses(['label', 'label-' . $statuses[IB::STATUS_CREATED  ]]),
                    IB::STATUS_SENT      => H::html((string) H::html($txt['n'][IB::STATUS_SENT     ] . ($fromSent ? ' ▼' : ''))->addCssClass('l100')->mobileExclude() . (string) H::html($txt['l'][IB::STATUS_SENT     ])->mobileOnly())->escape(false)->addCssClasses(['label', 'label-' . $statuses[IB::STATUS_SENT     ]]),
                    IB::STATUS_READ      => H::html((string) H::html($txt['n'][IB::STATUS_READ     ] . ($fromRead ? ' ▼' : ''))->addCssClass('l100')->mobileExclude() . (string) H::html($txt['l'][IB::STATUS_READ     ])->mobileOnly())->escape(false)->addCssClasses(['label', 'label-' . $statuses[IB::STATUS_READ     ]]),
                    IB::STATUS_PROCESSED => H::html((string) H::html($txt['n'][IB::STATUS_PROCESSED] . ($fromProc ? ' ▼' : ''))->addCssClass('l100')->mobileExclude() . (string) H::html($txt['l'][IB::STATUS_PROCESSED])->mobileOnly())->escape(false)->addCssClasses(['label', 'label-' . $statuses[IB::STATUS_PROCESSED]]),
                    IB::STATUS_CANCELED  => H::html((string) H::html($txt['n'][IB::STATUS_CANCELED ] . ($fromCanc ? ' ▼' : ''))->addCssClass('l100')->mobileExclude() . (string) H::html($txt['l'][IB::STATUS_CANCELED ])->mobileOnly())->escape(false)->addCssClasses(['label', 'label-' . $statuses[IB::STATUS_CANCELED ]]),
                ];
            } else {
                $labels[$type][$mobile] = [
                    IB::STATUS_CREATED   => H::html((string) H::html($txt['n'][IB::STATUS_CREATED  ] . ($fromCrea ? ' ▼' : '')))->escape(false)->addCssClasses(['label', 'l100', 'label-' . $statuses[IB::STATUS_CREATED  ]]),
                    IB::STATUS_SENT      => H::html((string) H::html($txt['n'][IB::STATUS_SENT     ] . ($fromSent ? ' ▼' : '')))->escape(false)->addCssClasses(['label', 'l100', 'label-' . $statuses[IB::STATUS_SENT     ]]),
                    IB::STATUS_READ      => H::html((string) H::html($txt['n'][IB::STATUS_READ     ] . ($fromRead ? ' ▼' : '')))->escape(false)->addCssClasses(['label', 'l100', 'label-' . $statuses[IB::STATUS_READ     ]]),
                    IB::STATUS_PROCESSED => H::html((string) H::html($txt['n'][IB::STATUS_PROCESSED] . ($fromProc ? ' ▼' : '')))->escape(false)->addCssClasses(['label', 'l100', 'label-' . $statuses[IB::STATUS_PROCESSED]]),
                    IB::STATUS_CANCELED  => H::html((string) H::html($txt['n'][IB::STATUS_CANCELED ] . ($fromCanc ? ' ▼' : '')))->escape(false)->addCssClasses(['label', 'l100', 'label-' . $statuses[IB::STATUS_CANCELED ]]),
                ];
            }
        }

        if ($hasMenu) {
            $colors = IB::getStatusColors(true, $type);
            $dds = (new DropDownMenu())->alignRight();
            $toCrea && $dds->addLink(H::iconCached('paw', null, $colors[IB::STATUS_CREATED  ]) . $txt['n'][IB::STATUS_CREATED  ], '#', false, ['id' => 'stl' . $txt['l'][IB::STATUS_CREATED  ] . $id, 'onclick' => "\$.ts(" . $id . ", '" . IB::STATUS_CREATED   . "', '" . $statuses[IB::STATUS_CREATED  ] . "', '" . $txt['n'][IB::STATUS_CREATED  ] . "', '" . $txt['l'][IB::STATUS_CREATED  ] . "', '" . $baseUrl . "', " . $mobile . ");return false;"], $currentStatus === IB::STATUS_CREATED   ? ['hidden'] : []);
            $toSent && $dds->addLink(H::iconCached('paw', null, $colors[IB::STATUS_SENT     ]) . $txt['n'][IB::STATUS_SENT     ], '#', false, ['id' => 'stl' . $txt['l'][IB::STATUS_SENT     ] . $id, 'onclick' => "\$.ts(" . $id . ", '" . IB::STATUS_SENT      . "', '" . $statuses[IB::STATUS_SENT     ] . "', '" . $txt['n'][IB::STATUS_SENT     ] . "', '" . $txt['l'][IB::STATUS_SENT     ] . "', '" . $baseUrl . "', " . $mobile . ");return false;"], $currentStatus === IB::STATUS_SENT      ? ['hidden'] : []);
            $toRead && $dds->addLink(H::iconCached('paw', null, $colors[IB::STATUS_READ     ]) . $txt['n'][IB::STATUS_READ     ], '#', false, ['id' => 'stl' . $txt['l'][IB::STATUS_READ     ] . $id, 'onclick' => "\$.ts(" . $id . ", '" . IB::STATUS_READ      . "', '" . $statuses[IB::STATUS_READ     ] . "', '" . $txt['n'][IB::STATUS_READ     ] . "', '" . $txt['l'][IB::STATUS_READ     ] . "', '" . $baseUrl . "', " . $mobile . ");return false;"], $currentStatus === IB::STATUS_READ      ? ['hidden'] : []);
            $toProc && $dds->addLink(H::iconCached('paw', null, $colors[IB::STATUS_PROCESSED]) . $txt['n'][IB::STATUS_PROCESSED], '#', false, ['id' => 'stl' . $txt['l'][IB::STATUS_PROCESSED] . $id, 'onclick' => "\$.ts(" . $id . ", '" . IB::STATUS_PROCESSED . "', '" . $statuses[IB::STATUS_PROCESSED] . "', '" . $txt['n'][IB::STATUS_PROCESSED] . "', '" . $txt['l'][IB::STATUS_PROCESSED] . "', '" . $baseUrl . "', " . $mobile . ");return false;"], $currentStatus === IB::STATUS_PROCESSED ? ['hidden'] : []);
            $toCanc && $dds->addLink(H::iconCached('paw', null, $colors[IB::STATUS_CANCELED ]) . $txt['n'][IB::STATUS_CANCELED ], '#', false, ['id' => 'stl' . $txt['l'][IB::STATUS_CANCELED ] . $id, 'onclick' => "\$.ts(" . $id . ", '" . IB::STATUS_CANCELED  . "', '" . $statuses[IB::STATUS_CANCELED ] . "', '" . $txt['n'][IB::STATUS_CANCELED ] . "', '" . $txt['l'][IB::STATUS_CANCELED ] . "', '" . $baseUrl . "', " . $mobile . ");return false;"], $currentStatus === IB::STATUS_CANCELED  ? ['hidden'] : []);
        }
        
        /* @var $label \Osf\View\Helper\Html */
        $label = clone $labels[$type][$mobile][$currentStatus];
        $hasMenu && $label->setMenu($dds)->setAttribute('id', 'st' . $id);
        $hasMenu || $label->setAttribute('onclick' , "\$.ajaxCall('" . ($labelUrl ?? H::url('event', 'msg', ['k' => 'ds'])) . "')")->addCssClass('clickable');
        $this->label = (string) $label;
        
        return $this;
    }

    /**
     * @return string
     */
    public function render()
    {
        return $this->label;
    }
}
