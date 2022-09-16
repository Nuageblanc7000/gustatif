<?php

namespace App\Controller;

use App\Repository\RestaurantRepository;

use Psr\Cache\CacheItemInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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
    #[Route('/resto-week',name:'resto-week')]
    function test(RestaurantRepository $restaurantRepository,CacheInterface $cacheInterface): Response
        {
            $randomRestoIds = [];
            $limit =   $restaurantRepository->findAll();
            if(count($limit) > 3){
            $cache = $cacheInterface->get('restoRand',function(CacheItemInterface $item) use($randomRestoIds ,$limit){ 
                $item->expiresAfter(30);
                    while (count($randomRestoIds) < 3) {
                        $rand = array_rand($limit);
                        if(!array_key_exists($rand,$randomRestoIds)){
                            $randomRestoIds[ $rand ] = $limit[$rand]->getId();
                        }
                    }
                    return $randomRestoIds;
                });
                $randomResto = $cache;
                $hydratation =  $restaurantRepository->findBy(['id'=> $randomResto]);
            }else{     
                $cacheInterface->delete('restoRand'); 

                if(count($limit) > 0){
                    $hydratation = $limit;
                }else{
                    $hydratation = $randomRestoIds;
                }
            }
            //permet de supprimer le cache en cours 
            $response = $this->render('/_partials/_flip-card.html.twig',['restos'=>$hydratation]);
        
         
        return $response;
    }
    
   
}
