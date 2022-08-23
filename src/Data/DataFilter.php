<?php
namespace App\Data;

use App\Entity\Category;
use App\Entity\Origine;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
class DataFilter
{

    /**
     * page en cours
     *
     * @var integer
     */
    public int $page = 0;

    /**
     * Undocumented variable
     *
     * @var string
     */
    #[Assert\Length(max:40,maxMessage:'Recherche trop longue')]
    public ?string $s = '';

     /**
     * @var Category[]
     */
    public $categories = [];

    /**
     * origine cuisine
     *
     * @var Origine
     */
    public ?Origine $t;
    
    public $min;
    public $max;

}
