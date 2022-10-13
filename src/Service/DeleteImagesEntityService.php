<?php
namespace App\Service;

use Exception;
use Doctrine\ORM\EntityManagerInterface;


/**
 * service pour la suppression d'image
 */
class DeleteImagesEntityService
{

    private $em;
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function delete(object $image)
    { 
        try {

            foreach ($this->getTargetDirectory() as $target) {
                 if (file_exists($target)) {
                     unlink($target);
                    }
            }
            $this->em->remove($image);
            $this->em->flush();
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