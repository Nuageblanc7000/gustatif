<?php

namespace App\Controller\user;

use App\Entity\User;
use App\Form\UserType;
use App\Service\MailService;
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
    public function new(Request $request,EntityManagerInterface $em,MailService $mail, UserPasswordHasherInterface $hasher,TranslatorInterface $translator): Response
    {
        if($this->getUser())
        {
            return $this->redirectToRoute('app_profil',[],Response::HTTP_FOUND);
        }
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $password = $hasher->hashPassword($user,$form->get('password')->getData());
            $user->setPassword($password);
            $em->persist($user);
            $em->flush();
            $mail->subscribeMail($user);
            $message = $translator->trans('Valider votre email pour vous connecter');
            $this->addFlash('success',$message);
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
    public function activeAccount(TokenResolveRepository $tokenRepo,$token, EntityManagerInterface $em , Request $req, TranslatorInterface $translator ){
        $tokens = $tokenRepo->findOneBy(['token' => $token]);
        if($tokens)
        {
            $user= $tokens->getUserCurrent()->setIsAcountVerified(true);
            $em->persist($user);
            $em->remove($tokens);
            $em->flush();
            
         $messageFlash = $translator->trans("Votre compte est maintenant activé bienvenu !").' '. $user->getPseudo();
         $this->addFlash('sucess', $messageFlash);
        }
   
        return  $this->redirectToRoute('login');
    }
}
