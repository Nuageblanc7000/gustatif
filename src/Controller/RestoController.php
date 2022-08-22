<?php

namespace App\Controller;

use App\Entity\Image;
use App\Form\RestoType;
use App\Entity\Restaurant;
use App\Repository\RestaurantRepository;
use App\Service\FileUploader;
use Symfony\Component\Form\FormError;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class RestoController extends AbstractController
{
   
    
    #[Route('/restaurants', name: 'restos_list')]
    public function restaurants(RestaurantRepository $repo): Response
    {
        return $this->render('restaurant/restaurants.html.twig', [
            'restos' => $repo->findAll(),
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
                        if( $key == 0 )  $resto->setCover($tmtFile);
                        $img = new Image();
                        $img->setPath($tmtFile);
                        $em->persist($img);
                        $resto->addImage($img);   
                    }
                    
                    $em->persist($resto);
                    $em->flush();
                
                    
                    $message = 'Félicitation votre restaurant vient être ajouté!';
                    $this->addFlash('succes',$message);

                }
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
