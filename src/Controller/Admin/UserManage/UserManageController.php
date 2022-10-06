<?php

namespace App\Controller\Admin\UserManage;

use App\Entity\User;
use App\Form\UserEditType;
use App\Service\FileUpload;
use App\Service\ImageDelService;
use App\Repository\UserRepository;
use App\Service\DeleteRestoService;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\TokenResolveRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserManageController extends AbstractController
{

    #[Route('/admin/manage/user/{id}', name: 'user_manage')]
    /**
     * permet de modifier un user à partir de l'admin
     *
     * @param EntityManagerInterface $em
     * @param Request $req
     * @param TranslatorInterface $translator
     * @param User $user
     * @param ImageDelService $imageDelService
     * @param FileUpload $fileUpload
     * @return Response
     */
    public function manageUserModif(EntityManagerInterface $em, Request $req, TranslatorInterface $translator, User $user, ImageDelService $imageDelService, FileUpload $fileUpload): Response
    {
        $form = $this->createForm(UserEditType::class, $user);
        //je rajoute le champ qui n'existe pas dans le form de base (même que pour le user normal)
        $form->add('path', FileType::class, [
            'attr' => ['class' => 'avatar-btn js-btn-avatar'],
            'label' => $translator->trans('Choisir une autre photo'),
            'label_attr' => ['class' => 'label-avatar'],
            'mapped' => false,
            'required' => false,

            'constraints' => [
                new File(mimeTypes: ['image/jpg', 'image/jpeg', 'image/png'], mimeTypesMessage: $translator->trans('veuillez mettre un fichier image (png,jpg,jpeg)'), maxSize: '100000k'),
            ],

        ]);
        $form->handleRequest($req);

        if ($form->isSubmitted() and $form->isValid()) {
            $file = $form->get('path')->getData();
            if (!empty($file)) {
                $avatar = $user->getAvatar();
                $imageDelService->setTargetDirectory([
                    $this->getParameter('avatar_user') . '/' . $avatar,
                    $this->getParameter('avatar_user_mini') . '/' . $avatar,
                    $this->getParameter('avatar_user_bg') . '/' . $avatar,
                ]);
                $imageDelService->delete();

                $fileUpload->setTargetDirectory($this->getParameter('avatar_user'));
                $newAvatar = $fileUpload->upload($file);
                $user->setAvatar($newAvatar);
            }

            $em->persist($user);
            $message = $user->getPseudo() . ' ' . $translator->trans('mis à jour');
            $this->addFlash(
                'success',
                $message
            );
            $em->flush();
            return $this->redirectToRoute('app_admin_users');
        }
        return $this->render('/admin/admin-user-manage/user-form-admin.html.twig', [
            'form' => $form->createView(),
            'user' => $user
        ]);
    }

    /**
     * permet de supprimer un utilisateur à partir de l'admin
     *
     * @param UserRepository $userRepository
     * @param ImageDelService $ImageDelService
     * @param DeleteRestoService $deleteRestoService
     * @param TranslatorInterface $translator
     * @param EntityManagerInterface $em
     * @param User $user
     * @param Request $req
     * @return Response
     */
    #[Route('/admin/manage/delete/user/{id}', 'delete_manage')]
    public function manageDeleteUser(UserRepository $userRepository, ImageDelService $ImageDelService, DeleteRestoService $deleteRestoService, TranslatorInterface $translator, EntityManagerInterface $em, User $user, Request $req): Response
    {
        //user anonyme pour les commentaires
        $anonymousUser = $userRepository->findOneBy(['pseudo' => 'user-delete']);
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
        if (!empty($user->getAvatar())) {
            $ImageDelService->setTargetDirectory([
                $this->getParameter('avatar_user') . '/' . $user->getAvatar(),
                $this->getParameter('avatar_user_mini') . '/' . $user->getAvatar(),
                $this->getParameter('avatar_user_bg') . '/' . $user->getAvatar(),
            ]);
            $ImageDelService->delete();
        }
        $message = $translator->trans('Utilisateur') . ' ' . $user->getPseudo() . ' ' . $translator->trans('supprimé');
        $em->remove($user);
        $em->flush();
        $this->addFlash('success', $message);
        return $this->redirectToRoute('app_admin_users');
    }
}
