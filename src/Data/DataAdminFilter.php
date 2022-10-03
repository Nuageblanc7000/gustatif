<?php
namespace App\Data;

use App\Entity\Restaurant;
use Symfony\Component\Validator\Constraints as Assert;

//fausse entité pour créer mes filtres de recherche
class DataAdminFilter
{
    private $search;
    private $resto;


    public function getSearch(): ?string
    {
        return $this->search;
    }

    public function setSearch(string $search): self
    {
        $this->search = $search;

        return $this;
    }  

    public function getResto(): ?Restaurant
    {
        return $this->resto;
    }

    public function setResto(Restaurant $resto): self
    {
        $this->resto = $resto;

        return $this;
    }  

}