<?php

namespace App\Controller;

use App\Repository\RestaurantRepository;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use FFI;
use Psr\Cache\CacheItemInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\VarDumper\Cloner\Data;
use Symfony\Contracts\Cache\CacheInterface;

class HomeController extends AbstractController
{
    public $CACHE_RESTO = [];
    #[Route('/', name: 'home')]
    public function index(RestaurantRepository $restaurantRepository): Response
    {
        return $this->render('home/home.html.twig', [
            'controller_name' => 'HomeController',
        ]);

    }    
    #[Route('/test',name:'test')]
    function test(RestaurantRepository $restaurantRepository,CacheInterface $cacheInterface): Response
        {
            
            $randomRestoIds = [];
            $cache = $cacheInterface->get('restoRand',function(CacheItemInterface $item) use($restaurantRepository ,$randomRestoIds, $cacheInterface){ 
                $item->expiresAfter(30);
                $limit =   $restaurantRepository->findAll();
                while (count($randomRestoIds) < 3) {
                    $rand = array_rand($limit);
                    if(!array_key_exists($rand,$randomRestoIds)){
                        $randomRestoIds[ $rand ] = $limit[$rand]->getId();
                    }
                }
                return $randomRestoIds;
                
            });
            // $cacheInterface->delete('restoRand'); 

             

            
            $randomResto = $cache;

            
    
            $response = $this->render('/_partials/_flip-card.html.twig',['restos' => $restaurantRepository->findBy(['id'=> $randomResto])]);
         
        return $response;
    }
    
   
}
