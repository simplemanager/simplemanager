<?php
namespace Sma\Mail;

use Osf\Stream\Html;

/**
 * Mail formatting tools
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 2017
 * @package sma
 * @subpackage mail
 */
class Tools
{
    public static function buildSpacesAlignCenter($txt, $lineLen = 75)
    {
        $margin = max(0, round(($lineLen - mb_strlen($txt)) / 2));
        return str_repeat(' ', $margin);
    }
    
    /**
     * Construit un paragraphe en mode texte
     * @param string $content
     * @param bool $htmlToText
     * @param string $endLine
     * @return string
     */
    public static function buildParagraph($content, bool $htmlToText = true, string $endLine = "\n\n")
    {
        $content = trim($content);
        if ($content !== '' && $htmlToText) {
            $content =  Html::toText($content);
        }
        return $content !== '' ? $content . $endLine : '';
    }
}