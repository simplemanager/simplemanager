<?php
namespace Sma\Pdf;

use Osf\Pdf\Tcpdf\Letter as TcpdfLetter;
use Sma\Bean\LetterBean;
use Sma\Bean\DocumentBeanInterface;

/**
 * Osf Tcpdf Letter avec des personnalisations spécifique à l'application
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 2017
 * @package sma
 * @subpackage pdf
 */
class Letter extends TcpdfLetter implements DocumentInterface
{
    use Addon\SmaParameters;
    use Addon\Type;
    use Addon\Dump;
    
    public function __construct(LetterBean $letter) {
        parent::__construct($letter);
        $this->registerSmaParameters($this);
        $this->setType('letter');
    }
    
    /**
     * @return string Contenu du PDF
     */
    public function generate(bool $return = true)
    {
        try {
            return $this->output(null, $return ? 'S' : 'I');
        } catch (DisplayedException $e) {
            echo $e->getMessage();
        } catch (Exception $e) {
            echo __("La génération du document comporte des erreurs. Nous analysons la situation. Veuillez nous excuser pour la gêne occasionnée.");
            Log::error($e->getMessage(), 'PDF', $e);
        }
    }
    
    /**
     * @return \Sma\Bean\LetterBean
     */
    public function getBean(): ?DocumentBeanInterface
    {
        return parent::getBean();
    }
    
    /**
     * Statut du document
     * @return string|null
     */
    public function getStatus(): ?string
    {
        return $this->getBean()->getStatus();
    }
}
