<?php
namespace App\Service;

use Exception;
use App\Entity\Plat;
use App\Entity\Image;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\EntityManagerInterface;


class DeleteImageService
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
        dd($e);
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