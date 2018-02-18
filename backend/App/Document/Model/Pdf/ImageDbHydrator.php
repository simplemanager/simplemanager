<?php
namespace App\Document\Model\Pdf;

use Osf\Image\ImageInfo;
use Osf\Pdf\Document\Bean\ImageBean;
use Sma\Db\ImageRow;

/**
 * Hydrateur de lettre pdf à partir des données de la base
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 12 nov. 2013
 * @package company
 * @subpackage pdf
 */
class ImageDbHydrator
{
    /**
     * @var ImageRow
     */
    protected $imageRow;
    
    /**
     * @param ImageBean $bean
     * @return ImageBean
     */
    public function hydrate(ImageBean $bean)
    {
        $bean->setImageFile($this->imageRow->getImageFile());
        $imageInfo = $this->imageRow->getBean();
        if ($imageInfo instanceof ImageInfo) {
            $colors = $imageInfo->getColors();
            if (is_array($colors)) {
                $bean->setColors($colors);
            }
        }
        
        return $bean;
    }

    /**
     * @return \Sma\Db\ImageRow
     */
    public function getImageRow() {
        return $this->imageRow;
    }
    
    /**
     * @param ImageRow $image
     * @return \App\Document\Model\Pdf\ImageDbHydrator
     */
    public function setImageRow(ImageRow $image) {
        $this->imageRow = $image;
        return $this;
    }
    
}