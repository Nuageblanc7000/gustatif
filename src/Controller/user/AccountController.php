<?php

namespace App\Controller\user;

use App\Entity\FalseImg;
use App\Form\UserEditType;
use App\Service\FileUploader;
use App\Form\UserModifyAvatarType;
use App\Repository\UserRepository;
use App\Service\DeleteImageService;
use App\Form\UserPasswordChangeType;
use App\Service\AvatarDeleteService;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\TokenResolveRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
#[Route('/profil')]
class AccountController extends AbstractController
{
    
    #[Route('/edit',name: 'app_profil_edit')]
    public function userEdit(Request $req, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        $this->denyAccessUnlessGranted("USER_EDIT", $user);
        $form = $this->createForm(UserEditType::class,$user);
        if(in_array('ROLE_RESTAURATEUR',$user->getRoles())){
            //au cas ou je veux ajouter d'autre info dans le futur
            //différente du user normal! comme par exemple la tva
        }
        $form->handleRequest($req);

        if($form->isSubmitted() && $form->isValid())
        {
            //action dans la bd de mise  à jour
            $em->persist($user);
            $em->flush();
            
            if(in_array('ROLE_RESTAURATEUR',$user->getRoles())){
                return $this->redirectToRoute('app_profil',[],Response::HTTP_FOUND);
            }
            else{
                return $this->redirectToRoute('app_profil_user',[],Response::HTTP_FOUND);
                // return $this->redirect($req->headers->get('referer'),Response::HTTP_FOUND);
            }
        }

        return $this->renderForm('/user/edit.html.twig',[
            'form'=> $form
        ]);
    }
    #[Route('/password',name: 'app_profil_modify_password')]
    /**
     * permet la modification du mot de passer par formulaire non mapped
     *
     * @param Request $req
     * @param EntityManagerInterface $em
     * @return Response
     */
    function modifyPassword(Request $req, EntityManagerInterface $em) : Response
    {
        $user = $this->getUser();
        $form = $this->createForm(UserPasswordChangeType::class,$user);
        $form->handleRequest($req);
        if($form->isSubmitted() && $form->isValid())
        {
            //action dans la bd de mise  à jour
            $em->persist($user);
            $em->flush();

            if(in_array('ROLE_RESTAURATEUR',$user->getRoles())){
                return $this->redirectToRoute('app_profil',[],Response::HTTP_FOUND);
            }
            else{
                dd('je suis un user normal!');
            }
        }
        

        return $this->renderForm('/user/password_change.html.twig',['form' => $form]);
    }

    /********************************* */



    #[Route('/avatar',name: 'app_profil_modify_avatar')]
    /**
     * Permet de gérer la photo de profil d'un utilisateur
     *
     * @param Request $req
     * @param EntityManagerInterface $em
     * @param FileUploader $fileUploader
     * @param AvatarDeleteService $avatarDeleteService
     * @return Response
     */
    public function ModifyAvatar(Request $req, EntityManagerInterface $em, FileUploader $fileUploader,AvatarDeleteService $avatarDeleteService) :Response
    {
        $user = $this->getUser();
        $falseImage = new FalseImg();
        $form = $this->createForm(UserModifyAvatarType::class,$falseImage);
        $form->handleRequest($req);
        if($form->isSubmitted() && $form->isValid())
        {
            $file = $form->get('path')->getData();
            if(!empty($file)){
                $fileUploader->setTargetDirectory($this->getParameter('avatar_user'));
                $tmtf = $fileUploader->upload($file);
                $avatarDeleteService->setTargetDirectory(
                    [
                        $this->getParameter('avatar_user') . '/' . $user->getAvatar(),
                        $this->getParameter('avatar_user_mini') . '/' . $user->getAvatar(),
                    ]
                    );
                $avatarDeleteService->delete();
                $user->setAvatar($tmtf);
                $em->persist($user);
                $em->flush();

                return $this->redirectToRoute('app_profil',[],Response::HTTP_FOUND);
            }
        }       
        return $this->renderForm('/user/avatar_modify.html.twig',['form' => $form]);
    }


    #[Route('/delete',name: 'app_profil_delete')]
    public function FunctionName(UserRepository $userRepository,TokenResolveRepository $tokenRepo): Response
    {
        $user = $this->getUser();
        $this->denyAccessUnlessGranted("USER_DELETE", $user);
        $token = $tokenRepo->findOneBy(['userCurrent' => $user->getId()]);

        if($token)
        {
            $tokenRepo->remove($token,true);
        }
        $this->container->get('security.token_storage')->setToken(null);
        $userRepository->remove($user, true);
        

        return $this->redirectToRoute('home', [], Response::HTTP_SEE_OTHER);
    }

}