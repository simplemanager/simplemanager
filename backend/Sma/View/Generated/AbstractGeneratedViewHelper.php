<?php
namespace Sma\View\Generated;

use Osf\View\Generated\AbstractGeneratedViewHelper as OsfAbstractGeneratedViewHelper;

/**
 * Osf Helpers builders
 *
 * @version 1.0
 * @author Guillaume Ponçon - OpenStates Framework PHP Generator
 * @since OSF 3.0.0
 * @package osf
 * @subpackage generated
 * @property \Sma\View\Helper\ActionHistory $actionHistory
 * @property \Sma\View\Helper\BeanWarnings $beanWarnings
 * @property \Sma\View\Helper\ExchangeButton $exchangeButton
 * @property \Sma\View\Helper\StatusLabel $statusLabel
 * @method string actionHistory(\Zend\Db\ResultSet\ResultSetInterface $eventRows, \Sma\Bean\DocumentBeanInterface $bean)
 * @method \Sma\View\Helper\BeanWarnings beanWarnings(\Sma\Bean\Addon\WarningInterface $bean, bool $disallowSendWarnings = null, bool $withAdvices = null, bool $html = null)
 * @method \Sma\View\Helper\ExchangeButton exchangeButton(string $filePrefix, string $controller, string $exportAction, string $importAction = null)
 * @method \Sma\View\Helper\StatusLabel statusLabel(int $id, string $currentStatus, string $type, string $baseUrl, bool $reduceForMobile = true, string $labelUrl = null)
 */
abstract class AbstractGeneratedViewHelper extends OsfAbstractGeneratedViewHelper
{

    public static function getAvailableHelpers()
    {
        return array_merge(parent::getAvailableHelpers(), array (
          'actionHistory' => '\\Sma\\View\\Helper\\ActionHistory',
          'beanWarnings' => '\\Sma\\View\\Helper\\BeanWarnings',
          'exchangeButton' => '\\Sma\\View\\Helper\\ExchangeButton',
          'statusLabel' => '\\Sma\\View\\Helper\\StatusLabel',
        ));
    }

}