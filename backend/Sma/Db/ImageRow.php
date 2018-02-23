<?php
namespace Sma\Db;

use Osf\Image\ImageHelper as Image;
use Osf\Exception\ArchException;
use Osf\Image\ImageInfo;
use Sma\Db\Generated\AbstractImageRow;

/**
 * Row model for table image
 *
 * Use this class to complete AbstractImageRow
 *
 * @version 1.0
 * @author Guillaume Ponçon - OpenStates Db Generator
 * @since OSF 2.0
 * @package application
 * @subpackage models
 */
class ImageRow extends AbstractImageRow
{
    /**
     * Ajoute une image en la réduisant / recadrant et en construisant le bean avec les couleurs dominantes
     * 
     * Il n'y a plus qu'à faire save() après ça
     * 
     * @param string $imageFile
     * @param string $type
     * @param string $description
     * @param number $perimeter
     * @throws ArchException
     * @deprecated since version 2.0
     * @return \Sma\Db\ImageRow
     */
    public function setAndProcessImage($imageFile, $type = 'logo', $description = null, $perimeter = Image::DEFAULT_PERIMETER)
    {
        // Récupération et traitement de l'image
        if (!file_exists($imageFile)) {
            throw new ArchException('Image file do not exists');
        }
        $image = Image::getImageContent($imageFile, $perimeter);
        $bean = new ImageInfo();
        $iw = $image->getimagewidth();
        $ih = $image->getimageheight();
        $bean
            ->setColors(Image::getColors(null, $image->__tostring()))
            ->setFormat($image->getformat())
            ->setHeight($ih)
            ->setWidth($iw)
            ->setQuality(($ih + $iw) * 2 < $perimeter ? ImageInfo::QUALITY_POOR : ImageInfo::QUALITY_GOOD);
        $this
            ->setContent($image->__tostring())
            ->setType($type)
            ->setDescription($description)
            ->setBean($bean);
        
        return $this;
    }
    
    /**
     * Récupère un chemin vers une image
     * @param int $id
     * @param string $tempDir
     * @throws ArchException
     * @return string
     */
    public function getImageFile($id = null, $tempDir = null)
    {
        $tempDir = $tempDir !== null ?: '/tmp/osf_' . $this->getSchema() . '_' . $this->table . '_images';
        if (!is_dir($tempDir) && !@mkdir($tempDir)) {
            throw new ArchException('Unable to create temporary directory ' . $tempDir);
        }
        $id = $id !== null ?: $this->getId();
        if (!is_int($id) || !$id) {
            throw new ArchException('Bad id type');
        }
        $file = $tempDir . '/' . (int) $id . '.png';
        if (!file_exists($file)) {
            $image = $this->getTableGateway()->find($id);
            if (!is_object($image)) {
                throw new ArchException('Image ' . (int) $id . ' not found');
            }
            if (!@file_put_contents($file, $image->getContent())) {
                throw new ArchException('Unable to write ' . $file);
            }
        }
        return $file;
    }
}
