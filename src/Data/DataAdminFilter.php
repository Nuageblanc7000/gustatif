<?php
namespace App\Data;

use Symfony\Component\Validator\Constraints as Assert;

class DataAdminFilter
{
    private $search;
    // private $cities;


    public function getSearch(): ?string
    {
        return $this->search;
    }

    public function setSearch(string $search): self
    {
        $this->search = $search;

        return $this;
    }  

}