<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\RestaurantRepository;
use Psr\Cache\CacheItemInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Cache\CacheInterface;

class HomeController extends AbstractController
{
    public $CACHE_RESTO = [];
    /**
     * permet de créer chaque semaine 3 nouveau restaurant pour la page d'accueil avec le cache
     *
     * @param RestaurantRepository $restaurantRepository
     * @param CacheInterface $cacheInterface
     * @return Response
     */
    #[Route('/', name: 'home')]
    public function index(RestaurantRepository $restaurantRepository,CacheInterface $cacheInterface): Response
    {
        $randomRestoIds = [];
        $limit =   $restaurantRepository->findAllRestoOpti();
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
        $response = $this->render('home/home.html.twig',['restos'=>$hydratation]);
    return $response;
    }
    
    
   #[Route('/header',name:'header_form')]
   public function header_form(CategoryRepository $categoryRepository): Response
   {
       $fast = $categoryRepository->findOneBy(['name' => 'fast-food']);
       $restoId = $categoryRepository->findOneBy(['name' => 'restaurant']);
       return $this->render('/_partials/header/_header.html.twig', ['fast' => $fast,'restoId'=>$restoId]);
   }
}
