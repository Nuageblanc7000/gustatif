<?php

namespace App\Controller\user;

use App\Entity\Like;
use App\Entity\FalseImg;
use App\Form\UserEditType;
use App\Service\FileUpload;
use App\Service\ImageDelService;
use App\Form\UserModifyAvatarType;
use App\Repository\CityRepository;
use App\Repository\UserRepository;
use App\Service\DeleteImageService;
use App\Service\DeleteRestoService;
use Symfony\UX\Chartjs\Model\Chart;
use App\Form\UserPasswordChangeType;
use App\Service\DeleteImagesService;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\TokenResolveRepository;
use App\Service\DeleteImagesEntityService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\Serializer\SerializerInterface;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

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
    public function ProfilResto(ChartBuilderInterface $chartBuilder, TranslatorInterface $translator, SerializerInterface $serializerInterface): Response
    {
        $user = $this->getUser();
        if(in_array('ROLE_RESTAURATEUR',$user->getRoles()) || in_array('ROLE_ADMIN',$user->getRoles())){
            $restos = $user->getRestaurant();
            $tabCharts =[];
            $dates = new \DateTimeImmutable();
            $data = [];
            $label = [$translator->trans('Commentaires'),$translator->trans('Followers')];
            foreach ($restos as  $resto) {
                $data=[$comments = count($resto->getComments()),count($resto->getLikes())];
                
            
              $chart = $chartBuilder->createChart(Chart::TYPE_BAR);
              //ici pour connaitre le nombre de commentaire du restaurant
              $chart->setData([
                'labels' => $label,
                'datasets' => [
                    [
                        'label' => $resto->getName(),
                        'backgroundColor' => ["#FFB714", "#1c2826" ],
                        'borderColor' => '',
                        'data' => $data,
                    ],
                ],
            ]);
            $chart->setOptions([
                'y' => [
                    'suggestedMin' => 0,
                    'suggestedMax' => '',
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
    public function userEdit(Request $req, EntityManagerInterface $em, TranslatorInterface $translator): Response
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
            $message = $translator->trans('Mise à jour du profil réussie');
            $this->addFlash('success',$message);
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
    function modifyPassword(Request $req, EntityManagerInterface $em, TranslatorInterface $translator): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(UserPasswordChangeType::class, $user);
        $form->handleRequest($req);
        if ($form->isSubmitted() && $form->isValid()) {
            //action dans la bd de mise  à jour
            $em->persist($user);
            $em->flush();
            $message = $translator->trans('Mot de passe mis à jour');
            $this->addFlash('success',$message);
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
     * @param FileUpload $fileUploader
     * @param DeleteImageService $DeleteImageService
     * @return Response
     */
    #[Route('/profil/avatar', name: 'profil_modify_avatar',priority:4)]
    public function ModifyAvatar(Request $req, EntityManagerInterface $em, FileUpload $fileUploader, ImageDelService $ImageDelService, TranslatorInterface $translator): Response
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
                $ImageDelService->setTargetDirectory(
                    [
                        $this->getParameter('avatar_user') . '/' . $user->getAvatar(),
                        $this->getParameter('avatar_user_mini') . '/' . $user->getAvatar(),
                        $this->getParameter('avatar_user_bg') . '/' . $user->getAvatar(),
                    ]
                );
                $ImageDelService->delete();
                $user->setAvatar($tmtf);
                $em->persist($user);
                $em->flush();
                $message = $translator->trans('Photo profil modifiée');
                $this->addFlash('success',$message);
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
    * permet de supprimer son profil et toutes les liaisons si on est restaurateur
    *
    * @param UserRepository $userRepository
    * @param Request $req
    * @param TokenResolveRepository $tokenRepo
    * @param ImageDelService $ImageDelService
    * @param DeleteRestoService $deleteRestoService
    * @param TranslatorInterface $translator
    * @return Response
    */
    #[Route('/profil/user/delete', name: 'delete_profil')]
    public function delete(UserRepository $userRepository,Request $req, TokenResolveRepository $tokenRepo, ImageDelService $ImageDelService,DeleteRestoService $deleteRestoService ,TranslatorInterface $translator,EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        $session = $req->getSession();
        $session->set('bye','bye'); 
        $token = $tokenRepo->findOneBy(['userCurrent' => $user->getId()]);
          //user anonyme pour les commentaires
          $anonymousUser = $userRepository->findOneBy(['pseudo'=>'user-delete']);
          $comments = $user->getComments();
          foreach ($comments as $com) {
              $com->setAuthor($anonymousUser);
              $em->persist($com);
          }
        if (in_array('ROLE_RESTAURATEUR', $user->getRoles())) {
            //on vérifie d'abord qu'il y a bien un resto sinon ça sert à rien de boucler dans le vide
            $restos = $user->getRestaurant();
            if (count($restos) > 0) {
                foreach ($restos as  $resto) {
                 $deleteRestoService->destroy($resto);
                }
            }
        }
        if ($token) {
            $tokenRepo->remove($token, true);
        }
        $this->container->get('security.token_storage')->setToken(null);
        if(!empty($user->getAvatar())){
            $ImageDelService->setTargetDirectory([
                $this->getParameter('avatar_user') . '/' . $user->getAvatar(),
                $this->getParameter('avatar_user_mini') . '/' . $user->getAvatar(),
                $this->getParameter('avatar_user_bg') . '/' . $user->getAvatar(),
            ]);
            $ImageDelService->delete();
        }
        $message = $translator->trans('Nous espérons vous revoir vite') .' '. $user->getPseudo();
        $em->remove($user);
        $em->flush();
        $this->addFlash('sucess',$message);
        
        $ref = $req->headers->set('referer','');
        return $this->redirectToRoute('home');
    }
    
    /**
     * permet d'enlever un like du compte courant
     *
     * @param Like $like
     * @param EntityManagerInterface $em
     * @param TranslatorInterface $translator
     * @return Response
     * @return TranslatorInterface $translator
     */
    #[IsGranted('ROLE_USER')]
    #[Route('/profil/user/dislike/{id}', name: 'dislike_resto')]
    public function dislikeResto(Like $like,EntityManagerInterface $em,TranslatorInterface $translator): Response
    {
        $user = $this->getUser();
        if($like->getUser() !== $user){
            return  throw new AccessDeniedHttpException(message: 'Accès refusé', code: 403);
        }
        $message = $translator->trans('Restaurant enlevé des favoris') .' '. $like->getRestaurant()->getName();
        $this->addFlash('success',$message);
        $em->remove($like);
        $em->flush();
        return $this->redirectToRoute('app_profil', ['div'=>'favoris-profil']);
    }
}
