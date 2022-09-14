<?php
namespace App\Service;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileUpload
{
    private $targetDirectory;
    private $slugger;


    public function __construct(SluggerInterface $slugger)
    {
     
        $this->slugger = $slugger;
    }

    public function upload(UploadedFile $file)
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        // $safeFilename = $this->slugger->slug($originalFilename);
        $fileName = uniqid().'.'.$file->guessExtension();

        try {
            $file->move($this->getTargetDirectory(), $fileName);
        } catch (FileException $e) {
        }

        return $fileName;
    }
    public function getTargetDirectory() : string
    {
        return $this->targetDirectory;
    }
    public function setTargetDirectory(?string $targetDirectory) : self
    {
        $this->targetDirectory = $targetDirectory;
        return $this;
    }
}