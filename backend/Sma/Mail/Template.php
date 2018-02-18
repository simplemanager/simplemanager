<?php
namespace Sma\Mail;

use Sma\Mail;
use Osf\Exception\ArchException;
use H;

/**
 * Mail template renderer
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 2017
 * @package sma
 * @subpackage mail
 */
class Template
{
    const HTML_REPLACES = [
        ' !' => '&nbsp;!',
        ' ?' => '&nbsp;?',
        ' :' => '&nbsp;:',
    ];
    
    /**
     * @var Mail
     */
    protected $mail;
    protected $html = '';
    protected $txt  = '';
    
    public function __construct(Mail $mail)
    {
        $this->mail = $mail;
    }
    
    public function render(bool $convertToTxt = false, bool $refresh = false)
    {
        if ($refresh || $this->html === '') {
            $this->html = $this->buildHtml();
        }
        if ($convertToTxt && ($refresh || $this->txt === '')) {
            $this->txt = Tools::buildParagraph($this->html, true, '');
        }
        return $convertToTxt ? $this->txt : $this->html;
    }
    
    protected function buildHtml()
    {
        $html = '';
        foreach ($this->mail->getItems() as $item) {
            switch ($item['type']) {
                case Mail::I_TITLE : 
                    $html .= $this->bullet();
                    $html .= H::html(H::html($item['text'], 'strong')->escape($item['escape']), 'p')->escape(false);
                    break;
                case Mail::I_PARAGRAPH : 
                    $html .= $this->bullet();
                    $html .= H::html($item['text'], 'p')->escape($item['escape']);
                    break;
                case Mail::I_LINK : 
                    $this->bullet(H::html($item['text'], 'a')->setAttribute('href', $item['url']), false);
                    break;
                case Mail::I_BULLET : 
                    $this->bullet($item['escape'] ? htmlspecialchars($item['text']) : $item['text'], false);
                    break;
                case Mail::I_TEXT : 
                    $html .= $this->bullet();
                    $html .= H::html($item['text'])->escape($item['escape']);
                    break;
                default : 
                    throw new ArchException('Unknown type [' . $item['type'] . ']');
            }
        }
        $html .= $this->bullet() . '<hr/>' . $this->mail->getFooter();
        return str_replace(
                array_keys(self::HTML_REPLACES), 
                array_values(self::HTML_REPLACES), 
                $html);
    }
    
    /**
     * Gestion des listes
     * @staticvar array $bullets
     * @param string $text
     * @param bool $output
     * @return string
     */
    protected function bullet($text = null, bool $output = true)
    {
        static $bullets = [];
        
        if ($text !== null) {
            $bullets[] = $text;
        }
        
        if ($output && isset($bullets[0])) {
            $html = '<ul><li>' . implode('</li><li>', $bullets) . '</li></ul>';
            $bullets = [];
            return $html;
        }
        
        return null;
    }
}