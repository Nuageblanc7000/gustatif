<?php

namespace App\Controller;

use App\Entity\Plat;
use App\Entity\Image;
use App\Form\PlatType;
use App\Form\RestoType;
use App\Data\DataFilter;
use App\Form\FilterType;
use App\Entity\Restaurant;
use App\Service\FileUploader;
use Symfony\Component\Form\FormError;
use App\Repository\RestaurantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class RestoController extends AbstractController
{
   
    
    #[Route('/restaurants/{s?}', name: 'restos_list')]
    public function restaurants(RestaurantRepository $repo , Request $req ): Response
    {   
        $data = new DataFilter();
        $form= $this->createForm(FilterType::class,$data);
        $form->handleRequest($req);
        if($form->isSubmitted() && $form->isValid())
        {
        }
        return $this->render('restaurant/restaurants.html.twig', [
            'restos' => $repo->restoPaginator($data),
            'form' => $form->createView()
        ]);
    }

    
    #[Route('/restaurant/create',name:'resto_create')]
    /**
     * permet la création d'un restaurant avec ajout d'image et gestion par service
     *
     * @param Request $req
     * @param EntityManagerInterface $em
     * @param FileUploader $upload
     * @return Response
     */
    function resto_create(Request $req, EntityManagerInterface $em , FileUploader $upload) : Response
    {
        $resto = new Restaurant();
        $form = $this->createForm(RestoType::class,$resto);
        $form->handleRequest($req);
        if($form->isSubmitted() && $form->isValid())
        {
                $files = $form->get('images')->getData();

                if(!empty($files))
                {
                    $upload->setTargetDirectory($this->getParameter('resto_images'));
                 
                    foreach ($files  as $key => $file) 
                    {
                        
                        $tmtFile =  $upload->upload($file->getPath());
                        if( $key == array_key_first($files) )  $resto->setCover($tmtFile);
                        $img = new Image();
                        $img->setPath($tmtFile);
                        $em->persist($img);
                        $resto->addImage($img);   
                    }
                    
                    $em->persist($resto);
                    $em->flush();
                    $message = 'Félicitation votre restaurant vient être ajouté!';
                    $this->addFlash('succes',$message);
                        // ajouter le return ! ici
                }
        }
        return $this->renderForm('/restaurant/create_resto.html.twig',[
            'form' => $form
        ]);
    }
    #[Route('/restaurant/plat',name:'create_plat')]
    public function create_plat(Request $req, EntityManagerInterface $em , FileUploader $uploader , RestaurantRepository $repo) : Response
    
    {
        $plat = new Plat();
        // $user = $this->getUser();
        $form = $this->createForm(PlatType::class,$plat);
        $form->handleRequest($req);
        if($form->isSubmitted() && $form->isValid())
        {
            $datas = $form->get('images')->getData();
            foreach ($datas as $data) {
                $uploader->setTargetDirectory($this->getParameter('resto_plats'));
                $path = $uploader->upload($data->getPath());
                $plat->setName($data->getAlt());
                $plat->setImage($path);
                $plat->setRestaurant($repo->find(19));
                $em->flush();
                $em->persist($plat);
                $em->flush();
            }
            dd('');

            
            // il faudra gérer le user !! pour lui donner l'id du resto et vérifier que le resto est bien à lui et qu'il n'ai 

        }
        return $this->renderForm('/restaurant/create_plat.html.twig',['form' =>$form]);
    }

    #[Route('/restaurant/{id}', name: 'resto_view')]
    public function restaurant(Restaurant $resto): Response
    {

        $longi = $resto->getCity()->getLongitude();
        $lati = $resto->getCity()->getLatitude();
        return $this->render('restaurant/restaurant.html.twig', [
            'resto' => $resto,
            'longi' => $longi,
            'lati' => $lati
        ]);
    }
}
