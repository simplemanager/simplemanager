<?php
namespace Sma;

use App\Common\Container;
use Osf\Image\ImageHelper as OsfImage;
use DB;

/**
 * Picture management
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 2017
 * @package sma
 * @subpackage picture
 */
class Image 
{
    /**
     * Generated image folder in current application
     * @return string
     */
    protected static function getImgFolder(): string
    {
        return APP_PATH . '/htdocs/www/img/';
    }
    
    /**
     * Generated image base url in current application
     * @return string
     */
    protected static function getImgBaseUrl(): string
    {
        return Container::getRequest()->getBaseUrl() . 'img/';
    }
    
    /**
     * Extract image on file system and get the file name
     * @param int $imageId
     * @param int $perimeter
     * @return string
     */
    protected static function getImageFileName(int $imageId, int $perimeter = OsfImage::DEFAULT_PERIMETER)
    {
        $file = self::getImageHash($imageId) . '-' . $perimeter . '.png';
        $absFile = self::getImgFolder() . $file;
        if (!file_exists($absFile)) {
            $image = DB::getImageTable()->find($imageId);
            if (!$image || !$image->getContent()) {
                return null;
            }
            $blob = $image->getContent();
            $imagick = OsfImage::getImageContentFromBlob($blob, $perimeter);
            file_put_contents($absFile, (string) $imagick);
        }
        return $file;
    }
    
    /**
     * Get url of an image from Image DB table
     * @param int $imageId
     * @param int $perimeter
     * @return string|null
     */
    public static function getImageUrl(int $imageId, int $perimeter = OsfImage::DEFAULT_PERIMETER)
    {
        $fileName = self::getImageFileName($imageId, $perimeter);
        return $fileName ? self::getImgBaseUrl() . $fileName : null;
    }
    
    /**
     * Get full path of an image extracted from DB
     * @param int $imageId
     * @param int $perimeter
     * @return string|null
     */
    public static function getImageFile(int $imageId, int $perimeter = OsfImage::DEFAULT_PERIMETER)
    {
        $fileName = self::getImageFileName($imageId, $perimeter);
        return $fileName ? self::getImgFolder() . $fileName : null;
    }
    
    /**
     * Build a hash corresponding to image id
     * @param int $imageId
     * @return string
     */
    public static function getImageHash(int $imageId)
    {
        return hash('sha1', 'OSI' . $imageId);
    }
    
    /**
     * Remove images corresponding to imageId from filesystem
     * @param int $imageId
     */
    public static function cleanImage(int $imageId)
    {
        $pattern = self::getImgFolder() . self::getImageHash($imageId) . '*';
        foreach (glob($pattern) as $file) {
            unlink($file);
        }
    }
}
