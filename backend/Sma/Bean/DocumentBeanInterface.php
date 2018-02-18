<?php
namespace Sma\Bean;

use Osf\Pdf\Document\Bean\ContactBean;
use DateTime;

/**
 * Interface commune pour les lettres, factures, etc. à indexer
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 2017
 * @package sma
 * @subpackage bean
 */
interface DocumentBeanInterface
{
    /**
     * Identifiant (clé primaire) du document dans la table "document"
     * @param null|int $id
     */
    public function setId($id);
    
    /**
     * Identifiant (clé primaire) du document dans la table "document"
     * @return null|int
     */
    public function getId();
    
    /**
     * Titre du document à afficher
     * @return null|string
     */
    public function getTitle();
    
    /**
     * Description du document
     * @return null|string
     */
    public function getDescription();
    
    /**
     * Template utilisé pour la génération (optionel)
     * @return null|string
     */
    public function getTemplate();
    
    /**
     * Hash spécifique au document
     * @return string
     */
    public function buildHash(): string;
    
    /**
     * Dernier hash calculé
     * @param bool $buildIfNotFound
     * @return string|null
     */
    public function getHashLastBuilded(bool $buildIfNotFound = false): ?string;
    
    /**
     * Url vers le document pour indexation
     * @return string
     */
    public function buildUrl(): string;
    
    /**
     * Données (non translitérées) à indexer pour la recherche
     * @return string
     */
    public function getSearchData(): string;
    
    /**
     * Type de document (letter, template, invoice, order, quote...)
     * @return string
     */
    public function getType(): string;
    
    /**
     * Expéditeur
     * @param bool $computeTitle
     * @return ContactBean
     */
    public function getProvider(bool $computeTitle = true): ContactBean;
    
    /**
     * Destinataire
     * @param bool $computeTitle
     * @return ContactBean
     */
    public function getRecipient(bool $computeTitle = true): ContactBean;
    
    /**
     * Construit un nom de fichier pour ce document
     * @param int $version version à ajouter
     * @param DateTime $date date de la révision à substituer à la date du bean
     * @return string
     */
    public function buildFileName(int $version = null, DateTime $date = null): string;
}