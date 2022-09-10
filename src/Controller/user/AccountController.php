<?php

namespace App\Controller\user;

use App\Entity\FalseImg;
use App\Form\UserEditType;
use App\Service\FileUploader;
use App\Form\UserModifyAvatarType;
use App\Repository\UserRepository;
use App\Service\DeleteImageService;
use Symfony\UX\Chartjs\Model\Chart;
use App\Form\UserPasswordChangeType;
use App\Service\AvatarDeleteService;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\TokenResolveRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Contracts\Translation\TranslatorInterface;

class AccountController extends AbstractController
{
    #[IsGranted('ROLE_USER')]
    #[Route('/profil/{div?}', name: 'app_profil')]
    /**
     * Permet d'afficher le profil !RESTAURATEUR!
     * CHART JS POUR LE GRAPHIQUE
     * @param ChartBuilderInterface $chartBuilder
     * @return Response
     */
    public function ProfilResto(ChartBuilderInterface $chartBuilder): Response
    {
        $user = $this->getUser();
        if(in_array('ROLE_RESTAURATEUR',$user->getRoles())){
            $restos = $user->getRestaurant();
            $tabCharts =[];
            $dates = new \DateTimeImmutable();
            $date = date("m");
            foreach ($restos as  $resto) {
                $comments = count($resto->getComments());
                $data = [0,0,0,0,0,0,0,0,$comments];
            
              $chart = $chartBuilder->createChart(Chart::TYPE_LINE);
              //ici pour connaitre le nombre de commentaire du restaurant
              $chart->setData([
                  'labels' => ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet','Aout','Septembre','Octobre','Novembre'],
                  'datasets' => [
                      [
                        
                          'backgroundColor' => ['red','blue','green','pink','yellow','orange','grey','black','cyan','#feaa3a'],
                          'borderColor' => '#feaa3a',
                          'data' => $data,
                      ],
                  ],
              ]);
              $chart->setOptions([
                  'scales' => [
                      'y' => [
                          'suggestedMin' => 0,
                          'suggestedMax' => 40,
                      ],
                  ],
              ]);

              $tabCharts[] = $chart;
            }
    
            return $this->render('profil/index.html.twig', [
                'charts' => $tabCharts,
            ]);
        }else{
            return $this->render('profil/index.html.twig', [
            ]);
        }
    }

    /**
     * Undocumented function
     *
     * @param Request $req
     * @param EntityManagerInterface $em
     * @return Response
     */
    #[Route('/profil/user/edit', name: 'profil_edit',priority:1)]
    public function userEdit(Request $req, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        $this->denyAccessUnlessGranted("USER_EDIT", $user);
        $form = $this->createForm(UserEditType::class, $user);
        if (in_array('ROLE_RESTAURATEUR', $user->getRoles())) {
            //au cas ou je veux ajouter d'autre info dans le futur
            //différente du user normal! comme par exemple la tva
        }
        $form->handleRequest($req);

        if ($form->isSubmitted() && $form->isValid()) {
            //action dans la bd de mise  à jour
            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('app_profil', [], Response::HTTP_FOUND);
        }

        return $this->renderForm('/user/edit.html.twig', [
            'form' => $form
        ]);
    }


    /**
     * permet la modification du mot de passer par formulaire non mapped
     *
     * @param Request $req
     * @param EntityManagerInterface $em
     * @return Response
     */
    #[Route('/profil/password', name: 'profil_modify_password',priority:4)]
    function modifyPassword(Request $req, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(UserPasswordChangeType::class, $user);
        $form->handleRequest($req);
        if ($form->isSubmitted() && $form->isValid()) {
            //action dans la bd de mise  à jour
            $em->persist($user);
            $em->flush();
            return $this->redirectToRoute('app_profil', [], Response::HTTP_FOUND);
        }


        return $this->renderForm('/user/password_change.html.twig', ['form' => $form]);
    }

    /********************************* */



    /**
     * Permet de gérer la photo de profil d'un utilisateur
     *
     * @param Request $req
     * @param EntityManagerInterface $em
     * @param FileUploader $fileUploader
     * @param AvatarDeleteService $avatarDeleteService
     * @return Response
     */
    #[Route('/profil/avatar', name: 'profil_modify_avatar',priority:4)]
    public function ModifyAvatar(Request $req, EntityManagerInterface $em, FileUploader $fileUploader, AvatarDeleteService $avatarDeleteService): Response
    {
        $user = $this->getUser();
        $falseImage = new FalseImg();
        $form = $this->createForm(UserModifyAvatarType::class, $falseImage);
        $form->handleRequest($req);
        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('path')->getData();
            if (!empty($file)) {

                /**service de traitement des images + service de delete des images */
                $fileUploader->setTargetDirectory($this->getParameter('avatar_user'));
                $tmtf = $fileUploader->upload($file);
                $avatarDeleteService->setTargetDirectory(
                    [
                        $this->getParameter('avatar_user') . '/' . $user->getAvatar(),
                        $this->getParameter('avatar_user_mini') . '/' . $user->getAvatar(),
                        $this->getParameter('avatar_user_bg') . '/' . $user->getAvatar(),
                    ]
                );
                $avatarDeleteService->delete();
                $user->setAvatar($tmtf);
                $em->persist($user);
                $em->flush();

                return $this->redirectToRoute('app_profil', [], Response::HTTP_FOUND);
            }
        }
        return $this->renderForm('/user/avatar_modify.html.twig', ['form' => $form]);
    }



    /**
     * Route pour la confirmation de suppression 
     *
     * @return Response
     */
    #[Route('/profil/user/deleting',name:'profil_deleting',priority:4)]
    
    public function beforeDeleting(): Response
    {

        return $this->render('/profil/_profil-delete.html.twig', []);
    }


    /**
     * permet de supprimer un user
     *
     * @param UserRepository $userRepository
     * @param TokenResolveRepository $tokenRepo
     * @param DeleteImageService $deleteImageService
     * @param AvatarDeleteService $avatarDeleteService
     * @param TranslatorInterface $translator
     * @return Response
     */
    #[Route('/profil/user/delete', name: 'delete_profil')]
    public function delete(UserRepository $userRepository,Request $req, TokenResolveRepository $tokenRepo, DeleteImageService $deleteImageService, AvatarDeleteService $avatarDeleteService ,TranslatorInterface $translator): Response
    {
 
        $user = $this->getUser();
        $session = $req->getSession();
        $session->set('bye','bye'); 

        $token = $tokenRepo->findOneBy(['userCurrent' => $user->getId()]);
        if (in_array('ROLE_RESTAURATEUR', $user->getRoles())) {
            //on vérifie d'abord qu'il y a bien un resto sinon ça sert à rien de boucler dans le vide
            $restos = $user->getRestaurant();
            if ($restos) {
                foreach ($restos as  $resto) {
                    $images = $resto->getImages();
                    //on vérifie si le resto à des photos si oui il faut penser à supprimer leurs images
                    if ($images) {
                        foreach ($images as $image) {
                            $deleteImageService->setTargetDirectory([

                                $this->getParameter('resto_images') . '/' . $image->getPath(),
                                $this->getParameter('resto_mini_images') . '/' . $image->getPath(),
                                $this->getParameter('resto_bg_images') . '/' . $image->getPath()
                            ]);
                            $deleteImageService->delete($image);
                        }
                    }

                    $plats = $resto->getPlats();

                    //on vérifie si il y a des spécialités si oui il faut penser à supprimer leurs images
                    if ($plats) {
                        foreach ($plats as $plat) {
                            $deleteImageService->setTargetDirectory(
                                [
                                    $this->getParameter('resto_plats') . '/' . $plat->getImage(),
                                    $this->getParameter('resto_mini_plats') . '/' . $plat->getImage(),
                                    $this->getParameter('resto_bg_plats') . '/' . $plat->getImage()
                                ]
                            );
                            $deleteImageService->delete($plat);
                        }
                    }
                }
            }
        }

        if ($token) {
            $tokenRepo->remove($token, true);
        }
        $this->container->get('security.token_storage')->setToken(null);
        if(!empty($user->getAvatar())){

            $avatarDeleteService->setTargetDirectory([
                $this->getParameter('avatar_user') . '/' . $user->getAvatar(),
                $this->getParameter('avatar_user_mini') . '/' . $user->getAvatar(),
                $this->getParameter('avatar_user_bg') . '/' . $user->getAvatar(),
            ]);
            $avatarDeleteService->delete();
        }
        $message = $translator->trans('Nous espérons vous revoir vite') .' '. $user->getPseudo();
            
        $userRepository->remove($user, true);
        $this->addFlash('sucess',$message);
        
        $ref = $req->headers->set('referer','');
        return $this->redirectToRoute('home');
    }
}
