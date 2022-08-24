<?php
namespace App\Entity;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FalseImg
{
    private $path;
    private $alt;

    public function getPath(): ?UploadedFile
    {
        return $this->path;
    }

    public function setPath(File $path): self
    {
        $this->path = $path;

        return $this;
    }

    public function getAlt(): ?string
    {
        return $this->alt;
    }

    public function setAlt(string $alt): self
    {
        $this->alt = $alt;

        return $this;
    }
    
}
