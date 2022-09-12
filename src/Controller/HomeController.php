<?php

namespace App\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Constraints\Date;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(): Response
    {
        $date = Date('d/Y:m');
        dd($date);
        return $this->render('home/home.html.twig', [
            'controller_name' => 'HomeController',
        ]);

        
    }
    
}
