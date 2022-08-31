<?php

namespace App\Controller;

use App\Entity\Plat;
use App\Entity\Image;
use App\Form\PlatType;
use App\Form\RestoType;
use App\Data\DataFilter;
use App\Entity\FalseImg;
use App\Entity\Schedule;
use App\Form\FilterType;
use App\Entity\Restaurant;
use App\Entity\Speciality;
use App\Form\ScheduleType;
use App\Service\FileUploader;
use App\Repository\PlatRepository;
use App\Repository\ImageRepository;
use App\Service\DeleteImageService;
use Symfony\Component\Form\FormError;
use App\Repository\RestaurantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Test\Constraint\ResponseStatusCodeSame;
use Symfony\Component\Config\Definition\Exception\ForbiddenOverwriteException;

class RestoController extends AbstractController
{


    #[Route('/restaurants', name: 'restos_list')]
    public function restaurants(RestaurantRepository $repo, Request $req): Response
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

    #[IsGranted('ROLE_USER')]
    #[Route('/restaurant/create', name: 'resto_create')]
    /**
     * permet la création d'un restaurant avec ajout d'image et gestion par service
     *
     * @param Request $req
     * @param EntityManagerInterface $em
     * @param FileUploader $upload
     * @return Response
     */
    
     function resto_create(Request $req, EntityManagerInterface $em, FileUploader $upload, TranslatorInterface $translator): Response
    { 
        $resto = new Restaurant();
        $form = $this->createForm(RestoType::class, $resto);
        $form->handleRequest($req);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
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
    #[IsGranted('ROLE_USER')]
    #[Route('/restaurant/plat/{id}', name: 'create_plat')]
    /**
     * Undocumented function
     *
     * @param Request $req
     * @param Restaurant $resto
     * @param EntityManagerInterface $em
     * @param FileUploader $uploader
     * @param RestaurantRepository $repo
     * @return Response
     */
    public function create_plat(Request $req,Restaurant $resto, EntityManagerInterface $em, FileUploader $uploader, RestaurantRepository $repo): Response
    {
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

    #[IsGranted('ROLE_USER')]
    #[Route('/restaurant/modify/{id}', name: 'resto_modify')]
    /**
     * Modification d'une entity Restaurant
     *
     * @param Restaurant $resto
     * @param Request $req
     * @param EntityManagerInterface $em
     * @param FileUploader $upload
     * @return Response
     */
    public  function modify_resto(Restaurant $resto, Request $req, EntityManagerInterface $em, FileUploader $upload): Response
    {
        $user = $this->getUser();

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
    #[Route('restaurant/images/update/{id}/{token}', name: 'resto_image_delete')]
    /**
     * permet la suppression d'une image
     *
     * @param Request $req
     * @param [type] $token
     * @param null|Image $image
     * @param DeleteImageService $deleteImageService
     * @return void
     */
    public function updateImageResto(Request $req, $token, Image $image, DeleteImageService $deleteImageService)
    {
   //vérifier si les images appartiennent à l'utilisateur avant tout
        $idResto =  $image->getRestaurant()->getId();
    
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


    #[Route('restaurant/plat/update/{id}/{token}', name: 'resto_plat_delete')]
    /**
     * permet la supression d'un plat
     *
     * @param Request $req
     * @param [type] $token
     * @param Plat $plat
     * @param DeleteImageService $deleteImageService
     * @return void
     */
    public function updatePlatImage(Request $req, $token, Plat $plat, DeleteImageService $deleteImageService)
    {
   //vérifier si les images appartiennent à l'utilisateur avant tout
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

    #[IsGranted('ROLE_RESTAURATEUR')]
    #[Route('/restaurant/horaire/{id}','schedule_gestion')]
    /**
     * Permet de gérer l'horaire
     *
     * @param Request $req
     * @param DayRepository $repo
     * @return void
     */
    public function hourly(Request $req , Schedule $schedule , EntityManagerInterface $em ){
        $form = $this->createForm(ScheduleType::class,$schedule);

        $form->handleRequest($req);
        if($form->isSubmitted() && $form->isValid()){
                $em->persist($schedule);
                $em->flush();
        }
        return $this->render('/restaurant/schedule_gestion.html.twig',['form'=> $form->createView()]);
    }

    #[Route('/restaurant/{id}', name: 'resto_view')]
    /**
     * Affichage d'un restaurant en particulier + donnée pour la carte
     *
     * @param Restaurant $resto
     * @return Response
     */
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
