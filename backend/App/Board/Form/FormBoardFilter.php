<?php
namespace App\Board\Form;

use Osf\Form\OsfForm as Form;
use Osf\Form\Element\ElementInput;
use Osf\Form\Element\ElementSubmit;
use App\Common\Container;
use H;

/**
 * Filtre du tableau de bord
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 2017
 * @package app
 * @subpackage board
 */
class FormBoardFilter extends Form
{
    protected $from;
    protected $to;
    protected $period = null;
    
    public function __construct($from = null, $to = null, bool $periodYear = false)
    {
        if (!$from || !$to) {
            $month = $periodYear ? null : date('m');
            $year  = date('Y');
        }
        if (!$from && !$to) {
            $this->period = $periodYear ? 'year' : 'month';
        }
        if ($periodYear) {
            $this->from = $from ? $from : date("d/m/Y", mktime(0, 0, 0, 1, 1, $year));
            $this->to   = $to   ? $to   : date("d/m/Y", mktime(0, 0, 0, 1, 1 , $year + 1));
        } else {
            $this->from = $from ? $from : date("d/m/Y", mktime(0, 0, 0, $month, 1 , $year));
            $this->to   = $to   ? $to   : date("d/m/Y", mktime(0, 0, 0, $month + 1 > 12 ? 1 : $month + 1, 1 , $year));
        }
        parent::__construct();
    }
    
    public function init()
    {
        $title  = __("Période") . ' : ';
        $title .= ($this->period === 'month' ? H::html(__("ce mois-ci"))->addCssClass('text-red')  : H::link(__("ce mois-ci"), 'board', 'index')) . ', ';
        $title .=  $this->period === 'year'  ? H::html(__("cette année"))->addCssClass('text-red') : H::link(__("cette année"), 'board', 'index', ['range' => 'year']);
        $this->setTitle($title, 'calendar');
        Container::getDevice()->isMobile() ? $this->setExpandable() : $this->setCollapsable();
        
        $this->add((new ElementInput('from'))
                ->setPlaceholder(__("Du"))
                ->setTypeDate()
                ->setValue($this->from)
                ->getHelper()->setSize(4)->getElement());
        
        $this->add((new ElementInput('to'))
                ->setPlaceholder(__("Au"))
                ->setTypeDate()
                ->setValue($this->to)
                ->getHelper()->setSize(4)->getElement());
        
        $this->add((new ElementSubmit('submit'))->setValue('Afficher')
                ->getHelper()->addCssClass('btn-block')->setSize(4)->getElement());
    }
}
