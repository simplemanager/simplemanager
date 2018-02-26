<?php
namespace App\Sandbox\Form;

use Osf\Form\OsfForm as Form;
use Osf\Form\Element\ElementInput;
// use Osf\Form\Element\ElementCheckboxes;
use Osf\Form\Element\ElementCheckbox;
use Osf\Form\Element\ElementSelect;
use Osf\Form\Element\ElementRadios;
use Osf\Form\Element\ElementTags;
use Osf\Form\Element\ElementTextarea;
use Osf\Form\Element\ElementSubmit;
use Osf\Form\Element\ElementFile;
use Osf\View\Component\Inputmask;
use Osf\Filter\Filter as F;
use Osf\Validator\Validator as V;

/**
 * Login / Pass
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 20 nov. 2013
 * @package common
 * @subpackage forms
 */
class FormTest extends Form
{
    public function init()
    {
        $this->setTitle('Un formulaire de test', 'fa-edit');
        
        $this->add((new ElementInput('login'))
                ->setLabel('Votre login')
                ->setRequired()
                ->add(F::getStringTrim())
                ->add(F::getStringToLower())
                ->add(V::newStringLength()->setMin(3)));
        
        $this->add((new ElementInput('password'))
                ->setType(ElementInput::TYPE_PASSWORD)
                ->setLabel('Votre mot de passe')
                ->setRequired());
        
        $this->add((new ElementCheckbox('like'))
                ->setLabel("J'aime bien les pâtes au beurre avec de la confiture")
                ->setRequired()
                ->setDescription('une description'));

        $this->add((new ElementSelect('selectest'))
                ->setLabel('Je suis')
                ->setDescription('Pour savoir si il faut dire il ou elle...')
                ->setOptions(array('a' => 'Une fille', 'b' => 'Un garçon')));

        $this->add((new ElementSelect('selectest2'))
                ->setLabel('Je veux')
                ->setRequired()
                ->setOptions(array('' => 'choisissez un cadeau', 'a' => 'Une voiture', 'b' => 'Une moto', 'c' => "Rien, j'ai tout ce qu'il faut")));

        $this->add((new ElementInput('selecttest3'))
                ->setLabel('Search')
                ->setTypeSearch());

        $this->add((new ElementSelect('pays_membres'))
                ->setLabel("J'aimerais aller")
                ->setRequired()
                ->setMultiple(true, 3)
                ->setOptions(array('' => 'dans quel pays ? (3 maximum)', 'FR' => 'France', 'IT' => 'Italie', 'BE' => 'Belgique', 'UK' => 'Angleterre', 'US' => 'Etats-Unis')));

        $this->add((new ElementTags('keywords'))
                ->setPlaceholder('ex: vacances, enfants, voilier')
                ->setLabel('Mots clés'));
        
        $this->add((new ElementRadios('radiotest'))
                ->setLabel("Je suis")
                ->setOptions(array('a' => 'en vacances', 'b' => 'au boulot'))
                ->setDescription('Pour savoir si vous êtes courageux...')
                ->setRequired());
        
        $this->add((new ElementInput('email'))
                ->setType(ElementInput::TYPE_EMAIL)
                ->setLabel('Mon e-mail')
                ->setPlaceholder('saisissez un e-mail valide')
                ->setRequired());
        
        $this->add((new ElementInput('gsm'))
                ->setLabel('Mobile')
                ->setType(ElementInput::TYPE_TEL)
                ->setAddonLeft(null, 'fa-phone')
                ->setDataMask(Inputmask::MASK_PHONE_FR));
        
        $this->add((new ElementInput('born'))
                ->setTypeDate()
                ->setLabel('Date de naissance'));
        
        $this->add((new ElementInput('bapt'))
                ->setDataMask('99/99/9999', ['placeholder' => 'dd/mm/yyyy'])
                ->setLabel('Date de baptême'));
        
        $this->add((new ElementInput('borntime'))
                ->setTypeTime()
                ->setValue('11:20')
                ->setLabel('Heure de naissance'));

        $this->add((new ElementInput('color'))
                ->setTypeColor()
                ->setValue('#aa0000')
                ->setLabel('Couleur préférée'));
        
        $this->add((new ElementInput('howmany'))
                ->setLabel('Combien ?')
                ->setAddonLeft('€')
                ->setAddonRight('.00')
                ->setTypeNumber());
        
        $this->add((new ElementTextarea('comment'))
                ->setLabel('Mes commentaires')
                ->setPlaceholder('Ecrivez ce que vous voulez'));
        
        $this->add((new ElementFile('file'))
                ->setAutoUpload()
                ->setAccept(null));
        
        $this->add((new ElementSubmit('submit'))->setValue("S'identifier"));
    }
}