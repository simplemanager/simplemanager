<?php 
namespace Sma\View\Helper;

use Osf\View\Helper\Bootstrap\AbstractViewHelper as AVH;
use Sma\Bean\Addon\WarningInterface;
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
class BeanWarnings extends AVH
{
    /**
     * @var WarningInterface
     */
    protected $bean;
    
    protected $disallowSendWarnings = false;
    protected $withAdvices = true;
    protected $html = true;
    
    /**
     * Affiche les warnings liés à un bean
     * @param bool|null $disallowSendWarnings
     * @param bool|null $withAdvices
     * @param bool|null $html
     * @return \Sma\View\Helper\BeanWarnings
     */
    public function __invoke(WarningInterface $bean, ?bool $disallowSendWarnings = null, ?bool $withAdvices = null, ?bool $html = null)
    {
        $this->bean = $bean;
        $this->disallowSendWarnings = $disallowSendWarnings ?? $this->disallowSendWarnings;
        $this->withAdvices = $withAdvices ?? $this->withAdvices;
        $this->html = $html ?? $this->html;
        return $this;
    }

    /**
     * @return string
     */
    public function render()
    {
        $warns = $this->bean->getWarnings(false, $this->disallowSendWarnings, $this->withAdvices, $this->html);
        if ($warns) {
            foreach ($warns as $warn) {
                $this->html(H::iconCached($warn['icon'], null, AVH::STATUS_COLOR_LIST[$warn['status']], true) . '&nbsp;&nbsp;' . ($this->html ? $warn['title'] : H::html($warn['title'])) . '<br />');
            }
        } else {
            $this->html(H::html(__("Tout est OK"))->addCssClass('text-green'));
        }
        return $this->getHtml();
    }
}
