<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileUploadService
{

    /**
     * $targetDirectory, permet de récupérer l'emplacement du dossier, $sluger, permet d'obtenir des méthodes de traitement pour nettoyer le nom du fichier des caractères innaproprié, et le flash pour récupérer le flashbagInterface pour envoyer un message flash
     *
     * @param string $targetDirectory
     * @param SluggerInterface $slugger
     * @param FlashBagInterface $flash
     */
    public function __construct(private $targetDirectory,private  SluggerInterface $slugger , private FlashBagInterface $flash)
    {
    }

    public function upload(UploadedFile $file)
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $fileName = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

        try 
        {
          $file->move($this->getTargetDirectory(), $fileName);
        } catch (FileException $e) 
        
        {
            $message =   $e->getMessage();
            $this->flash->add('sucess',$message);
        }

        return $fileName;
    }

    public function getTargetDirectory()
    {
        return $this->targetDirectory;
    }
}