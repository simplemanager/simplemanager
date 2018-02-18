<?php
namespace Sma\Pdf;

use Sma\Bean\DocumentBeanInterface;

/**
 * Interface pour la compatibilité avec les documents de la base
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 2017
 * @package sma
 * @subpackage pdf
 */
interface DocumentInterface
{
    /**
     * Clé primaire du document dans la table des documents
     * @return null|int
     */
//    public function getId();
    
    /**
     * Type de document : letter, invoice, order, quote, form
     */
    public function getType(): string;
    
    /**
     * Contenu binaire du document
     */
    public function getDump(): string;
    
    /**
     * Bean contenant l'ensemble des données du document au moment de l'enregistrement
     * @return \Sma\Bean\DocumentBeanInterface
     */
    public function getBean(): ?DocumentBeanInterface;
    
    /**
     * Statut du document : created, sent, processed, canceled
     * @return string|null
     */
    public function getStatus(): ?string;
}
