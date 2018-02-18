<?php
namespace Sma\Pdf\Addon;

use Osf\Pdf\Tcpdf\Document;
use Sma\Session\Identity;
use Osf\Stream\Text;

/**
 * Sma parameters registration for a Tcpdf document
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 2017
 * @package osf
 * @subpackage pdf
 */
trait SmaParameters
{
    protected function registerSmaParameters(Document $document)
    {
        // Couleurs
        $color = Identity::getColor();
        if ($color) {
            [$r, $g, $b] = Text::explodeColor($color, 0, 0, 0);
            $document->setDefaultColor($r, $g, $b);
            $document->setLinkColor($r, $g, $b);
        }
        $document->setParams(Identity::getParams('document'));
        
        // Police
        // @todo à déplacer dans Tcpdf ?
        $authorizedFonds = [
            'times', // Performant
            'helvetica', // Lisible
            'helvetica_light', // Bien, clean
            'helvetica_condensed', // Bien
//            'latothin', // Difficile à lire
//            'latohairline', // Illisible
            'latolight', // Bien, clean
            'lato', // Lisible
            'latomedium', // Gros, lisible
            'latoblack', // Très gros... 
//            'dejavusanscondensed', // Un peu moche
//            'dejavusansmono', // Un peu moche
            'dejavuserif', // Lisible, pas très clean
            'freemono', // Proportionel, un peu moche
            'freeserif', // Lisible, très international
//            'futura_light', // S'affiche mal
//            'futura_condensed_light', // S'affiche mal
//            'futura_condensed', // S'affiche mal
            'courier', // Courier... un peu moche
        ];
        $font = Identity::getParam('document', 'font');
        $font = in_array($font, $authorizedFonds) ? $font : 'times';
        $document->setDefaultFont($font, $font);
    }
}
