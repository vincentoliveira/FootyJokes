<?php

namespace FootyJokes\APIBundle\Helper;

class CommonHelper
{
    
    /**
     * Permet de créer les vignettes à chaque upload d'image
     * 
     * @param string $source_image_path             Chemin d'accès à l'image originale (sur le serveur)
     * @param string $destination_image_path        Chemin de destination de l'image redimensionné (sur le serveur)
     * @param string $maxHeight                     Hauteur de l'image voulue
     * @param string $maxWidth                      Largeur de l'image voulue (facultatif)
     * @param bool   $getSizes                      If true, just return sizes, else do the job
     * 
     * @return boolean
     */
    static public function resizeImage($source_image_path, $destination_image_path, $maxHeight = null, $maxWidth = null, $getSizes = false)
    {
        if (empty($source_image_path) || !file_exists($source_image_path) || is_dir($source_image_path) || empty($maxHeight)) {
            return false;
        }

        list($source_image_width, $source_image_height, $source_image_type) = getimagesize($source_image_path);
        switch ($source_image_type) {
            case IMAGETYPE_GIF:
                $source_gd_image = imagecreatefromgif($source_image_path);
                break;
            case IMAGETYPE_JPEG:
                $source_gd_image = imagecreatefromjpeg($source_image_path);
                break;
            case IMAGETYPE_PNG:
                $source_gd_image = imagecreatefrompng($source_image_path);
                break;
        }
        if (!isset($source_gd_image) || $source_gd_image === false) {
            return false;
        }
        
        $thumbnail_image_height = $maxHeight;
        if ($maxWidth != null) {
            $thumbnail_image_width = $maxWidth;
        }
        else {
            $thumbnail_image_width = (int) ($source_image_width * $maxHeight / $source_image_height );
        }
        
        // if getSizes option, just return sizes
        if ($getSizes) {
            return array(
                'oldWidth' => $source_image_width,
                'oldHeight' => $source_image_height,
                'newWidth' => $thumbnail_image_width,
                'newHeight' => $thumbnail_image_height,
            );
        }
        
        $thumbnail_gd_image = imagecreatetruecolor($thumbnail_image_width, $thumbnail_image_height);
        imagealphablending($thumbnail_gd_image, false);
        imagesavealpha($thumbnail_gd_image, true);
        imagecopyresampled($thumbnail_gd_image, $source_gd_image, 0, 0, 0, 0, $thumbnail_image_width, $thumbnail_image_height, $source_image_width, $source_image_height);

        switch ($source_image_type) {
            case IMAGETYPE_GIF:
                imagegif($thumbnail_gd_image, $destination_image_path);
                break;
            case IMAGETYPE_JPEG:
                imagejpeg($thumbnail_gd_image, $destination_image_path, 90);
                break;
            case IMAGETYPE_PNG:
                imagepng($thumbnail_gd_image, $destination_image_path, 0);
                break;
        }
        imagedestroy($source_gd_image);
        imagedestroy($thumbnail_gd_image);

        return true;
    }
}
