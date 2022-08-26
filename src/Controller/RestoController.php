<?php

namespace App\Controller;

use App\Entity\Plat;
use App\Entity\Image;
use App\Form\PlatType;
use App\Form\RestoType;
use App\Data\DataFilter;
use App\Entity\FalseImg;
use App\Form\FilterType;
use App\Entity\Restaurant;
use App\Entity\Speciality;
use App\Service\FileUploader;
use App\Service\DeleteImageService;
use App\Repository\RestaurantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Test\Constraint\ResponseStatusCodeSame;
use Symfony\Contracts\Translation\TranslatorInterface;

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
                    $em->persist($resto);
                    $special = new Speciality();
                    $special->setRestaurant($resto);
                    $em->persist($special);
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
    #[Route('/restaurant/plat', name: 'create_plat')]
    public function create_plat(Request $req, EntityManagerInterface $em, FileUploader $uploader, RestaurantRepository $repo): Response

    {
        $plat = new Plat();
        // $user = $this->getUser();
        $form = $this->createForm(PlatType::class, $plat);
        $form->handleRequest($req);
        if ($form->isSubmitted() && $form->isValid()) {
            $datas = $form->get('images')->getData();
            foreach ($datas as $data) {
                $plat = new Plat();
                $uploader->setTargetDirectory($this->getParameter('resto_plats'));
                $path = $uploader->upload($data->getPath());
                $plat->setName($data->getAlt());
                $plat->setImage($path);
                $em->persist($plat);
                $repo->find(1)->getSpeciality()->addPlat($plat);
            }
            $em->flush();
            // il faudra gérer le user !! pour lui donner l'id du resto et vérifier que le resto est bien à lui et qu'il n'ai 

        }
        return $this->renderForm('/restaurant/create_plat.html.twig', ['form' => $form]);
    }


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
                    return $this->redirectToRoute('home');
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
     * permet la suppression d'une image avec vérification par token
     *
     * @param Request $req
     * @param [type] $token
     * @param Image $image
     * @param DeleteImageService $deleteImageService
     * @return void
     */
    public function updateImageResto(Request $req, $token, Image $image, DeleteImageService $deleteImageService)
    {


        if ($this->isCsrfTokenValid('delete' . $image->getId(), $token)) {
            //le service ici de suppression
            $deleteImageService->setTargetDirectory(
                [
                    $this->getParameter('resto_images') . '/' . $image->getPath(),
                    $this->getParameter('resto_mini_images') . '/' . $image->getPath(),
                    $this->getParameter('resto_bg_images') . '/' . $image->getPath()
                ]
            );
            $deleteImageService->delete($image);
            $this->addFlash('sucess', 'félicitation');
            return $this->redirectToRoute('resto_modify', ['id' => '1'], Response::HTTP_FOUND);
        } else {
            throw $this->createNotFoundException('aucun résultat');
        }
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
