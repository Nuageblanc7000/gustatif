<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RestoController extends AbstractController
{
    #[Route('/restaurants', name: 'restaurants')]
    public function restaurants(): Response
    {
        $tab=[['id' => 1, 'name' => 'La perle noir','city' => 'Saint-Ghislain','type' => 'fast-food', 'images' => ['fast0.jpg','fast1.jpg'],'star' => 4],
        ['id' => 2,'name' => 'La table de trois','city' => 'Mons','type' => 'restaurant', 'images' => ['fast0.jpg','fast1.jpg'],'star' => 5],
        ['id' => 3,'name' => 'La boule bleu','city' => 'Enghien','type' => 'fast', 'images' => ['fast0.jpg','fast1.jpg'], 'star' => 1],
        ['id' => 3,'name' => 'La boule bleu','city' => 'Enghien','type' => 'fast', 'images' => ['fast0.jpg','fast1.jpg'], 'star' => 1],
        ['id' => 4,'name' => 'La boule bleu','city' => 'Enghien','type' => 'fast', 'images' => ['fast0.jpg','fast1.jpg'], 'star' => 1],
        ['id' => 5,'name' => 'La boule bleu','city' => 'Enghien','type' => 'fast', 'images' => ['fast0.jpg','fast1.jpg'], 'star' => 1],
        ['id' => 6,'name' => 'La boule bleu','city' => 'Enghien','type' => 'fast', 'images' => ['fast0.jpg','fast1.jpg'], 'star' => 1],
        ['id' => 7,'name' => 'La boule bleu','city' => 'Enghien','type' => 'fast', 'images' => ['fast0.jpg','fast1.jpg'], 'star' => 1],
        ['id' => 8,'name' => 'La boule bleu','city' => 'Enghien','type' => 'fast', 'images' => ['fast0.jpg','fast1.jpg'], 'star' => 1],
        
        ];
        return $this->render('restaurant/restaurants.html.twig', [
            'tab' => $tab,
        ]);
    }

    #[Route('/restaurant/{id}', name: 'restaurant')]
    public function restaurant(): Response
    {
        return $this->render('restaurant/restaurant.html.twig', [
            'resto' => '',
        ]);
    }

}
