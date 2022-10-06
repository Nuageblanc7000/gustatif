<?php

namespace App\Controller\user;

use App\Entity\User;
use App\Form\UserType;
use App\Entity\TokenResolve;
use App\Service\MailService;
use App\Entity\ResetPassword;
use App\Form\ResetPasswordType;
use App\Form\UserPasswordChangeType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\TokenResolveRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class UserController extends AbstractController
{
    /**
     * permet de s'inscrire avec envoi d'email
     *
     * @param Request $request
     * @param EntityManagerInterface $em
     * @param MailService $mail
     * @param UserPasswordHasherInterface $hasher
     * @param TranslatorInterface $translator
     * @return Response
     */
    #[Route('/register', name: 'user_register', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em, MailService $mail, UserPasswordHasherInterface $hasher, TranslatorInterface $translator): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_profil', [], Response::HTTP_FOUND);
        }
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $password = $hasher->hashPassword($user, $form->get('password')->getData());
            $user->setPassword($password);
            $em->persist($user);
            $em->flush();
            $mail->subscribeMail($user);
            $message = $translator->trans('Valider votre email pour vous connecter');
            $this->addFlash('success', $message);
            return $this->redirectToRoute('login', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/active-account/{token}', name: 'active-account')]
    /**
     * route permettant d'activer le user qui a vérifier son email
     *
     * @param TokenResolveRepository $tokenRepo
     * @param [type] $token
     * @param EntityManagerInterface $em
     * @param Request $req
     * @param TranslatorInterface $translator
     * @return void
     */
    public function activeAccount(TokenResolveRepository $tokenRepo, $token, EntityManagerInterface $em, Request $req, TranslatorInterface $translator)
    {
        $tokens = $tokenRepo->findOneBy(['token' => $token]);
        if ($tokens) {
            $user = $tokens->getUserCurrent()->setIsAcountVerified(true);
            $em->persist($user);
            $em->remove($tokens);
            $em->flush();

            $messageFlash = $translator->trans("Votre compte est maintenant activé bienvenu !") . ' ' . $user->getPseudo();
            $this->addFlash('success', $messageFlash);
        }
        return  $this->redirectToRoute('login');
    }



    #[Route('/reset-password', name: 'reset_password')]
    /**
     * permet d'envoyer une demande de reset password
     *
     * @param Request $req
     * @param MailService $mailService
     * @param UserRepository $user
     * @param TranslatorInterface $translator
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function resetPassword(Request $req, MailService $mailService, UserRepository $user, TranslatorInterface $translator, EntityManagerInterface $em, TokenResolveRepository $tokenResolveRepository): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('profil_modify_password');
        }
        $newMail = new ResetPassword();
        $form = $this->createForm(ResetPasswordType::class, $newMail);
        $form->handleRequest($req);
        if ($form->isSubmitted() && $form->isValid()) {
            $mailData = $form->get('mail')->getData();
            $userCurrent = $user->findOneBy(['email' => $mailData]);
            if (!$userCurrent->isIsAcountVerified()) {
                $message = $translator->trans('Un email vous a été envoyé pour réinitialiser votre mot de passe');
                $this->addFlash(
                    'success',
                    $message
                );
                return $this->redirectToRoute('login');
            }

            if ($userCurrent) {
                $oldResetPassword = $tokenResolveRepository->findOneBy(['userCurrent' => $userCurrent]);
                if ($oldResetPassword) {
                    $em->remove($oldResetPassword);
                    $em->flush();
                }

                $token = substr(str_replace(['-', '/', '_', '+'], '', base64_encode(random_bytes(35))), 0, 30);
                $tokenResolve = new TokenResolve();
                $tokenResolve->setToken($token);
                $tokenResolve->setUserCurrent($userCurrent);
                $tokenResolve->setCreatedAt(new \DateTimeImmutable('+2 hours'));
                $userCurrent->setTokenResolve($tokenResolve);
                $em->persist($tokenResolve);
                $em->persist($userCurrent);
                $em->flush();
                $mailService->setSubject('Demande de réinitialisation de mot de passe');
                $mailService->setTemplate('@email_templates/recover-password.html.twig');
                $mailService->setContext($token);
                $mailService->mailToolService($userCurrent);
            }
            $message = $translator->trans('Un email vous a été envoyé pour réinitialiser votre mot de passe');
            $this->addFlash(
                'success',
                $message
            );
            return $this->redirectToRoute('login');
        }
        return $this->render('/security/lost_account.html.twig', [
            'form' => $form->createView()
        ]);
    }
    #[Route('/new-password-verify/{token}', name: 'new_password_verify_password')]
    /**
     * permet de vérifier par token l'accès au changement de mot de passe
     *
     * @param string $token
     * @param TokenResolveRepository $tokenResolveRepository
     * @param EntityManagerInterface $em
     * @param TranslatorInterface $translator
     * @param Request $req
     * @param UserPasswordHasherInterface $hasher
     * @return Response
     */
    public function newPasswordVerify(string $token, TokenResolveRepository $tokenResolveRepository, EntityManagerInterface $em, TranslatorInterface $translator, Request $req, UserPasswordHasherInterface $hasher): Response
    {
        $resetPass = $tokenResolveRepository->findOneBy(['token' => $token]);
        if (!$resetPass || $resetPass->getCreatedAt() < new \DateTime('now')) {
            if ($resetPass) {
                $em->remove($resetPass);
                $em->flush();
            }
            $message = $translator->trans('Votre demande est expirée veuillez refaire une demande');
            $this->addFlash(
                'error',
                $message
            );
            return $this->redirectToRoute('login');
        }
        $user = $resetPass->getUserCurrent();
        $form = $this->createForm(UserPasswordChangeType::class, $user);
        $form->handleRequest($req);

        if ($form->isSubmitted() && $form->isValid()) {
            $newPassword = $form->get('password')->getData();

            $user->setPassword($hasher->hashPassword($user, $newPassword));
            $em->persist($user);
            $em->remove($resetPass);
            $em->flush();
            $message = $translator->trans('Mot de passe modifié');
            $this->addFlash(
                'success',
                $message
            );
            return $this->redirectToRoute('login');
        }

        return $this->render('/security/reset_password.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
