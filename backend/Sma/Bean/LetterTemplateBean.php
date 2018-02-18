<?php
namespace Sma\Bean;

use Sma\Bean\LetterBean;

/**
 * Template de courrier
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 2017
 * @package sma
 * @subpackage bean
 */
class LetterTemplateBean extends LetterBean
{
    const TARGET_TYPE_EMAIL  = 'email';
    const TARGET_TYPE_LETTER = 'letter';
    const TARGET_TYPE_BOTH   = 'both';
    
    const TARGET_TYPES = [
        self::TARGET_TYPE_EMAIL,
        self::TARGET_TYPE_LETTER,
        self::TARGET_TYPE_BOTH,
    ];
    
    const DT_INVOICES = 'invoices';
    const DT_INVOICE  = 'invoice';
    const DT_ORDER    = 'order';
    const DT_QUOTE    = 'quote';
    const DT_RECIPENT = 'recipient';
    
    const DATA_TYPES = [
        self::DT_INVOICES,
        self::DT_INVOICE,
        self::DT_ORDER,
        self::DT_QUOTE,
        self::DT_RECIPENT ,
    ];
}