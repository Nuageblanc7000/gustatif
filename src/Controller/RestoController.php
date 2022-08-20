<?php

namespace App\Controller;

use App\Entity\Restaurant;
use App\Form\RestoType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class RestoController extends AbstractController
{
   
    
    #[Route('/restaurants', name: 'restos_list')]
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

    
    #[Route('/restaurant/create',name:'resto_create')]
    function resto_create(Request $req, EntityManagerInterface $em) : Response
    {
        $resto = new Restaurant();
        $form = $this->createForm(RestoType::class,$resto);
        $form->handleRequest($req);
    
        if($form->isSubmitted() && $form->isValid())
        {
            $em->persist($resto);
            $em->flush();
        }
        return $this->renderForm('/restaurant/create_resto.html.twig',[
            'form' => $form
        ]);
    }
    
    #[Route('/restaurant/{id}', name: 'resto_view')]
    public function restaurant(): Response
    {
        $longi = "3.956659";
        $lati = "50.454241";
        return $this->render('restaurant/restaurant.html.twig', [
            'resto' => '',
            'longi' => $longi,
            'lati' => $lati
        ]);
    }
}
