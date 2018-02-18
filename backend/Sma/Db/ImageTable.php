<?php
namespace Sma\Db;

use Osf\Image\ImageHelper as Image;
use Osf\Stream\Text;
use Osf\Exception\ArchException;
use Osf\Exception\DisplayedException;
use Sma\Log;
use Sma\Session\Identity;
use Sma\Db\Generated\AbstractImageTable;
use DB, Imagick;

/**
 * Table model for table image
 *
 * Use this class to complete AbstractImageTable
 *
 * @version 1.0
 * @author Guillaume Ponçon - OpenStates Framework PHP Generator
 * @since OSF 2.0
 * @package osf
 * @subpackage generated
 */
class ImageTable extends AbstractImageTable
{
    /**
     * Ajout d'une image dans la base après avoir calculé et modifié le contenu
     * @param array $postFile
     * @param string $type
     * @param string $description
     * @param int $idAccount
     * @return array inserted row
     * @throws ArchException
     * @throws \Osf\Exception\DisplayedException
     */
    public function insertImage(array $postFile, $type = null, $description = null, int $idAccount = null, int $perimeter = 1200, Imagick $image = null)
    {
        if (!isset($postFile['name']) ||
            !isset($postFile['type']) || substr($postFile['type'], 0, 5) !== 'image' ||
            !isset($postFile['tmp_name']) || !is_uploaded_file($postFile['tmp_name']) ||
            !isset($postFile['error']) || $postFile['error'] !== 0) {
            Log::error('Invalid image file metadata in ImageTable::insertImage()', 'DB', $postFile);
            throw new ArchException('Invalid files metadata');
        }
        if ($this->fields['content']['characterMaximumLength'] < filesize($postFile['tmp_name'])) {
            throw new DisplayedException(__("La taille du fichier est trop importante."));
        }
        $type = $type == 'logo' ? 'logo' : 'unknown';
        $description = Text::crop((string) $description, $this->fields['description']['characterMaximumLength']);
        $idAccount = $idAccount ?: Identity::getIdAccount();
        $image = $image === null ? $postFile['tmp_name'] : $image;
        $imageContent = Image::getImageContent($image, $perimeter);
        $colors = Image::getColors(null, $imageContent, 1, true, 100, true);
        $row = [
            'type' => $type,
            'content' => $imageContent,
            'color' => (isset($colors[0]) ? $colors[0] : null),
            'description' => $description ?? null,
            'id_account' => $idAccount,
            'bean' => serialize(['post' => $postFile, 'colors' => $colors]),
        ];
        
//        if ($updateImageId) {
//            return $this->update($row, ['id' => (int) $updateImageId]);
//        }
        
        // Insertion manuelle à cause du bug de Zend Db 2 qui insert les blobs
        // en double. Le blob est ici dans un des paramètres de la requête préparée.
        $result = $this->prepare('INSERT INTO `image` (`id`, `type`, `content`, `color`, `description`, `id_account`, `bean`) '
                     . 'VALUES (null, ?, ?, ?, ?, ?, ?)')->execute($row);
        $row['id'] = $result->getGeneratedValue();
        return $row;
    }
    
    public function deleteImage(int $imageId)
    {
        $idAccount = $idAccount ?? Identity::getIdAccount();
        DB::getImageTable()->delete(['id_account' => $idAccount, 'id' => $imageId]);
        return $this;
    }
    
    /**
     * Supprime le logo lié à un compte dans la base
     * @param int $idAccount (défaut = compte courant)
     * @return $this
     */
    public function cleanAccountLogo(int $idAccount = null)
    {
        $idAccount = $idAccount ?? Identity::getIdAccount();
        $this->delete([
            'id_account' => $idAccount, 
            'type' => 'logo', 
            'id NOT IN (SELECT id_logo FROM company WHERE id_account=' . $idAccount . ' AND id_logo IS NOT NULL)']);
        return $this;
    }
}