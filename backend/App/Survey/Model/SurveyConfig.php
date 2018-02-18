<?php
namespace App\Survey\Model;

use Osf\Config\OsfConfig;
use Osf\Stream\Text as T;
use Osf\Stream\Yaml;

/**
 * Sondage pour testeurs
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 2017
 * @package sma
 * @subpackage config
 */
class SurveyConfig extends OsfConfig
{
    const CONFIG_FILE = '/App/Survey/Config/survey.yml';
    
    public function __construct() {
        $file = APPLICATION_PATH . self::CONFIG_FILE;
        $stream = T::substituteConstants(file_get_contents($file));
        $this->appendConfig(Yaml::parse($stream));
    }
}
