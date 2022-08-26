<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Origine;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $tabOrgineFr = 
        ['Italienne',
        'Belge',
        'Africaine',
        'Anglaise',
        'Française',
        'Irlandaise',
        'Islandaise',
        'Bulgare',
        'Suisse',
        'Ukrainienne',
        'Grecque'
    ];
        for ($i=0; $i < count($tabOrgineFr) ; $i++) { 
            $Origine = new Origine();
            $Origine->setName($tabOrgineFr[$i]);
            $Origine->setImage(strtolower($tabOrgineFr[$i]).'.svg');
            $manager->persist($Origine);
        }
        $tabCategory = ['fast-food','restaurant'];
        for ($c=0; $c < count($tabCategory) ; $c++) { 
            $category = new Category();
            $category->setName($tabCategory[$c]);
            $manager->persist($category);
        }

        $manager->flush();
    }
}
