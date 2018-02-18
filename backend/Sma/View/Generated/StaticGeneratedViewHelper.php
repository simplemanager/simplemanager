<?php
namespace Sma\View\Generated;

use Sma\View\Helper\AbstractStaticViewHelper;

/**
 * Static helpers (quick access)
 *
 * @version 1.0
 * @author Guillaume Ponçon - OpenStates Framework PHP Generator
 * @since OSF 3.0.0
 * @package osf
 * @subpackage generated
 */
abstract class StaticGeneratedViewHelper extends AbstractStaticViewHelper
{

    /**
     * @return string
     */
    public static function actionHistory(\Zend\Db\ResultSet\ResultSetInterface $eventRows, \Sma\Bean\DocumentBeanInterface $bean)
    {
        return self::callHelper('actionHistory', [$eventRows, $bean]);
    }

    /**
     * @return \Sma\View\Helper\BeanWarnings
     */
    public static function beanWarnings(\Sma\Bean\Addon\WarningInterface $bean, bool $disallowSendWarnings = null, bool $withAdvices = null, bool $html = null)
    {
        return self::callHelper('beanWarnings', [$bean, $disallowSendWarnings, $withAdvices, $html]);
    }

    /**
     * @return \Sma\View\Helper\ExchangeButton
     */
    public static function exchangeButton(string $filePrefix, string $controller, string $exportAction, string $importAction = null)
    {
        return self::callHelper('exchangeButton', [$filePrefix, $controller, $exportAction, $importAction]);
    }

    /**
     * @return \Sma\View\Helper\StatusLabel
     */
    public static function statusLabel(int $id, string $currentStatus, string $type, string $baseUrl, bool $reduceForMobile = true, string $labelUrl = null)
    {
        return self::callHelper('statusLabel', [$id, $currentStatus, $type, $baseUrl, $reduceForMobile, $labelUrl]);
    }

}