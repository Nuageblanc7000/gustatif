<?php

namespace App\Controller;

use DateTime;
use DateTimeZone;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Constraints\Date;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(): Response
    {
        //date de la base de donnée!
        $dateUpdate = '26-09-2022';

        $firstday = new \DateTimeImmutable();
        $firstday->setTimezone(new DateTimeZone('Europe/Paris'));
        $nowTimeZone= $firstday->setTimezone(new DateTimeZone('Europe/Paris'))->format('j-m-Y');
    
        $dateTime = $firstday;
        $dateFormat = $dateTime->format('j-m-y');

        $monday = date('j-m-Y',strtotime($dateUpdate.'+7 days'));
        dd($monday);
        if($monday === $nowTimeZone){
            dd('ok');
        }else{
            dd('non il reste encore '.''. date('j',strtotime($monday) - strtotime($nowTimeZone)));
        }


        return $this->render('home/home.html.twig', [
            'controller_name' => 'HomeController',
        ]);

        
    
    
}
}
