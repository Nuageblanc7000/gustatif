<?php
namespace App\Service;

use Exception;


/**
 * permet la suppression d'un avatar d'un user
 */
class AvatarDeleteService
{

    public function delete() :void
    {
       
        try {
            foreach ($this->getTargetDirectory() as $target) {
                 if (file_exists($target)) {
                     unlink($target);
                    }
            }
       ;
     } catch (Exception $e) {
        return;
        }
        return;
    }
    public function getTargetDirectory() : array
    {
        return $this->targetDirectory;
    }
    public function setTargetDirectory(?array $targetDirectory) : self
    {
        $this->targetDirectory = $targetDirectory;
        return $this;
    }
}