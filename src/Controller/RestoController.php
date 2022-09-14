<?php

namespace App\Controller;

use App\Entity\Like;
use App\Entity\Plat;
use App\Entity\Image;
use App\Form\PlatType;
use App\Entity\Comment;
use App\Form\RestoType;
use App\Data\DataFilter;
use App\Entity\Schedule;
use App\Form\FilterType;
use App\Form\CommentType;
use App\Entity\Restaurant;
use App\Form\ScheduleType;
use App\Service\FileUpload;
use App\Repository\LikeRepository;
use App\Service\DeleteImageService;
use App\Repository\CommentRepository;
use Symfony\Component\Form\FormError;
use App\Repository\RestaurantRepository;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class RestoController extends AbstractController
{

    /**
     * Affichage de tous les restaurants
     *
     * @param RestaurantRepository $repo
     * @param Request $req
     * @return Response
     */
    #[Route('/restaurants', name: 'restos_list')]
    public function restaurants(RestaurantRepository $repo, Request $req ): Response
    {
        $data = new DataFilter();
        $form = $this->createForm(FilterType::class, $data);
        $form->handleRequest($req);
        if ($form->isSubmitted() && $form->isValid()) {
        }
        return $this->render('restaurant/restaurants.html.twig', [
            'restos' => $repo->restoPaginator($data),
            'form' => $form->createView()
        ]);
    }

    /**
     * permet la création d'un restaurant avec ajout d'image et gestion par service
     *
     * @param Request $req
     * @param EntityManagerInterface $em
     * @param FileUpload $upload
     * @param TranslatorInterface $translator
     * @return Response
     */
    #[IsGranted('ROLE_RESTAURATEUR')]
    #[Route('/restaurant/create', name: 'resto_create')]
     function resto_create(Request $req, EntityManagerInterface $em, FileUpload $upload, TranslatorInterface $translator): Response
     { 
        $user = $this->getUser();
        // $this->denyAccessUnlessGranted('RESTO_VIEW', $user);
        $resto = new Restaurant();
        $form = $this->createForm(RestoType::class, $resto);
        $form->handleRequest($req);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $countInput = count($form->get('images')->getData());
            if ($countInput <= 0) {
                $form->get('images')->addError(new FormError('Veuillez Ajouter au minimum une image'));
            } else {
                $files = $form->get('images')->getData();
                if (!empty($files)) {
                    $upload->setTargetDirectory($this->getParameter('resto_images'));

                    foreach ($files  as $key => $file) {

                        $tmtFile =  $upload->upload($file->getPath());
                        if ($key == array_key_first($files))  $resto->setCover($tmtFile);
                        $img = new Image();
                        $img->setPath($tmtFile);
                        $em->persist($img);
                        $resto->addImage($img);
                    }
                    $resto->setUser($user);
                    $em->persist($resto);
                    $em->flush();
                    $message = 'Félicitation votre restaurant vient être ajouté!';
                    $this->addFlash('succes', $message);
                }
            }
        }
        return $this->renderForm('/restaurant/create_resto.html.twig', [
            'form' => $form
        ]);
    }
    /**
     * Undocumented function
     *
     * @param Request $req
     * @param Restaurant $resto
     * @param EntityManagerInterface $em
     * @param FileUpload $uploader
     * @param RestaurantRepository $repo
     * @return Response
     */
    #[IsGranted('ROLE_RESTAURATEUR')]
    #[Route('/restaurant/plat/{id}', name: 'create_plat')]
    public function create_plat(Request $req,Restaurant $resto, EntityManagerInterface $em, FileUpload $uploader, RestaurantRepository $repo): Response
    {
        $this->denyAccessUnlessGranted('VIEW_PAGE_RESTO',$resto); 
        $user = $this->getUser();
        if($resto->getUser() !== $user)
        {
          // gestion de la securité avec retour vers page 403
            return  throw new AccessDeniedHttpException(message:'Accès refusé',code:403);
        }
        $limit = 4;
        $plats = $resto->getPlats();
        $countPlat = count($plats);
        $plat = new Plat();
        $form = $this->createForm(PlatType::class, $plat);
        $form->handleRequest($req);
        if ($form->isSubmitted() && $form->isValid()) {
            $countInput = count($form->get('images')->getData());

           if(($countInput + $countPlat) > $limit ){
            $form->get('images')->addError(new FormError('En mode non abonné vous ne pouvez avoir que'.' '.$limit.' '.'spécialités'));
           }else{

               $datas = $form->get('images')->getData();
               foreach ($datas as $data) {
                   $plat = new Plat();
                   $uploader->setTargetDirectory($this->getParameter('resto_plats'));
                   $path = $uploader->upload($data->getPath());
                   $plat->setName($data->getAlt());
                   $plat->setImage($path);
                   $em->persist($plat);
                   $resto->addPlat($plat);
                }
                $em->flush();
                return $this->redirect($req->headers->get('referer'));
            }

        }
        return $this->renderForm('/restaurant/create_plat.html.twig', ['form' => $form ,'plats' => $plats ]);
    }


    /**
     * Modification d'une entity Restaurant
     *
     * @param Restaurant $resto
     * @param Request $req
     * @param EntityManagerInterface $em
     * @param FileUpload $upload
     * @return Response
     */
    #[IsGranted('ROLE_RESTAURATEUR')]
    #[Route('/restaurant/modify/{id}', name: 'resto_modify')]
    public  function modify_resto(Restaurant $resto, Request $req, EntityManagerInterface $em, FileUpload $upload): Response
    {
        $user = $this->getUser();
        $this->denyAccessUnlessGranted('VIEW_PAGE_RESTO',$resto); 
        if($resto->getUser() !== $user)
        {
          // gestion de la securité avec retour vers page 403
            return  throw new AccessDeniedHttpException(message:'Accès refusé',code:403);
        }
        $form = $this->createForm(RestoType::class, $resto);
        $limit = 4;
        $form->handleRequest($req);
        $images = $resto->getImages();
        $CountImages = count($resto->getImages());
        if ($form->isSubmitted() && $form->isValid()) {
            if ($CountImages + count($form->get('images')->getData())  !== 0) {
                if (($CountImages + count($form->get('images')->getData())) <= $limit) {
                    $files = $form->get('images')->getData();
                    if (!empty($files)) {
                        $upload->setTargetDirectory($this->getParameter('resto_images'));

                        foreach ($files  as $key => $file) {
                            $tmtFile =  $upload->upload($file->getPath());
                            if ($key == array_key_first($files))  $resto->setCover($tmtFile);
                            $img = new Image();
                            $img->setPath($tmtFile);
                            $em->persist($img);
                            $resto->addImage($img);
                        }
                    }
                    $em->persist($resto);
                    $em->flush();
                    $message = 'Félicitation votre restaurant vient être ajouté!';
                    $this->addFlash('succes', $message);
                    return $this->redirect($req->headers->get('referer'));
                } else {
                    $form->get('images')->addError(new FormError('Les comptes non-abonnés ne peuvent avoir que ' . $limit . 'images'));
                }
            } else {
                $form->get('images')->addError(new FormError('Minimum une image'));
            }
        }
        return $this->renderForm('/restaurant/create_resto.html.twig', [
            'form' => $form,
            'images' => $images
        ]);
    }


    /**
     * permet la suppression d'une image
     *
     * @param Request $req
     * @param [type] $token
     * @param null|Image $image
     * @param DeleteImageService $deleteImageService
     * @return void
     */
    #[IsGranted('ROLE_RESTAURATEUR')]
    #[Route('restaurant/images/update/{id}/{token}', name: 'resto_image_delete')]
    public function updateImageResto(Request $req, $token, Image $image, DeleteImageService $deleteImageService , EntityManagerInterface $em)
    {
   //vérifier si les images appartiennent à l'utilisateur avant tout
   $resto = $image->getRestaurant();   
   $this->denyAccessUnlessGranted('VIEW_PAGE_RESTO',$resto); 
   $idResto =  $image->getRestaurant()->getId();
        if($resto->getCover() === $image->getPath()){
            $resto->setCover('');
            $em->persist($resto);
            $em->flush();
        }
        if ($this->isCsrfTokenValid('delete'.$image->getId(), $token)) {
                $deleteImageService->setTargetDirectory(
                    [
                        $this->getParameter('resto_images') . '/' .$image->getPath(),
                        $this->getParameter('resto_mini_images') . '/' .$image->getPath(),
                        $this->getParameter('resto_bg_images') . '/' . $image->getPath()
                    ]
                    );
                    $deleteImageService->delete($image);
                    
            $this->addFlash('sucess', 'félicitation');
            return $this->redirectToRoute('resto_modify',['id' => $idResto], Response::HTTP_FOUND);
        } else {
            throw $this->createNotFoundException('aucun résultat');
        }
    }

    /**
     * permet la supression d'un plat
     *
     * @param Request $req
     * @param [type] $token
     * @param Plat $plat
     * @param DeleteImageService $deleteImageService
     * @return void
     */
    #[IsGranted('ROLE_RESTAURATEUR')]
    #[Route('restaurant/plat/update/{id}/{token}', name: 'resto_plat_delete')]
    public function updatePlatImage(Request $req, $token, Plat $plat, DeleteImageService $deleteImageService)
    {
   //vérifier si les images appartiennent à l'utilisateur avant tout
        $resto = $plat->getRestaurant();
        $this->denyAccessUnlessGranted('VIEW_PAGE_RESTO',$resto); 
        $idResto =  $plat->getRestaurant()->getId();
    
        if ($this->isCsrfTokenValid('delete'.$plat->getId(), $token)) {
                $deleteImageService->setTargetDirectory(
                    [
                        $this->getParameter('resto_plats') . '/' .$plat->getImage(),
                        $this->getParameter('resto_mini_plats') . '/' .$plat->getImage(),
                        $this->getParameter('resto_bg_plats') . '/' . $plat->getImage()
                    ]
                    );
                    $deleteImageService->delete($plat);
                
            $this->addFlash('sucess', 'félicitation');
            return $this->redirectToRoute('create_plat',['id' => $idResto], Response::HTTP_FOUND);
        } else {
            throw $this->createNotFoundException('aucun résultat');
        }
    }


    /**
     * Permet de gérer l'horaire
     *
     * @param Request $req
     * @param DayRepository $repo
     * @return Response
     */
    #[IsGranted('ROLE_RESTAURATEUR')]
    #[Route('/restaurant/horaire/{id}','schedule_gestion')]
    public function hourly(Request $req , Schedule $schedule , EntityManagerInterface $em, RestaurantRepository $repo ) : Response
    {
        $resto = $repo->findOneBy(['schedule' => $schedule]);
        $this->denyAccessUnlessGranted('VIEW_PAGE_RESTO',$resto);
        $form = $this->createForm(ScheduleType::class,$schedule);

        $form->handleRequest($req);
        if($form->isSubmitted() && $form->isValid()){
                $em->persist($schedule);
                $em->flush();
        }
        return $this->render('/restaurant/schedule_gestion.html.twig',['form'=> $form->createView()]);
    }



    /**
     * permet la suppression d'un restaurant
     *
     * @param Request $req
     * @param EntityManagerInterface $em
     * @return Response
     */
    #[IsGranted('ROLE_RESTAURATEUR')]    
    #[Route('/restaurant/delete/{id}')]
    public function deleteResto(Request $req, EntityManagerInterface $em ): Response
    {
        //doit seulement être fait!
        return $this->render('$0.html.twig', []);
    }
    /**
     * Affichage d'un restaurant en particulier + donnée pour la carte
     *
     * @param Restaurant $resto
     * @param Request $req
     * @param EntityManagerInterface $em
     * @param CommentRepository $commentRepository
     * @return Response
     */
    #[Route('/restaurant/{id}', name: 'resto_view')]
    public function restaurant(Restaurant $resto, Request $req, EntityManagerInterface $em, CommentRepository $commentRepository): Response
    {   
        $comment = new Comment();
        $user= $this->getUser();
        $form = $this->createForm(CommentType::class,$comment);
        $form->handleRequest($req);
        if($form->isSubmitted() && $form->isValid())
        {
            $comment->setAuthor($user)
                    ->setResto($resto);
            $em->persist($comment);
            $em->flush();
            return $this->redirect($req->headers->get('referer'),Response::HTTP_FOUND);
        }
        $longi = $resto->getCity()->getLongitude();
        $lati = $resto->getCity()->getLatitude();
        return $this->render('restaurant/restaurant.html.twig', [
            'resto' => $resto,
            'longi' => $longi,
            'lati' => $lati,
            'form' => $form->createView(),
            'repo' => $commentRepository->findBy(['resto' => $resto],['id' =>  'DESC']),
        ]);
    }


    #[Route('/like/{id}',name:'like_resto',methods:['POST'])]
    public function likeResto(Request $req, Restaurant $resto, EntityManagerInterface $em ,LikeRepository $likeRepository): Response
    {
        
        $user = $this->getUser();
        
        if(!$user){
            $pageLogin = $this->generateUrl('login');
                return $this->json(['route' => $pageLogin,200]);
            }
        if($user !== $resto->getUser()){
            if(!$resto->isLikeByUser($user))
            {
                $like = new Like();
                $like->setUser($user)
                ->setRestaurant($resto);
                $em->persist($like);
                $em->flush();
                return $this->json(['reponse'=>'like'],200);

            }else{
                $like = $likeRepository->findOneBy(['restaurant'=> $resto ,'user'=> $user]);
                $em->remove($like);
                $em->flush();
                return $this->json(['reponse'=>'dislike'],200);
            }
        }else{
            return  throw new AccessDeniedHttpException(message:'Accès refusé',code:403);
        }
        return  throw new NotFoundHttpException(code:404);
    
}
    
}
