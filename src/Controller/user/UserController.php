<?php

namespace App\Controller\user;

use App\Entity\User;
use App\Form\UserType;
use App\Service\MailService;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\TokenResolveRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class UserController extends AbstractController
{


    #[Route('/register', name: 'app_user_register', methods: ['GET', 'POST'])]
    public function new(Request $request,EntityManagerInterface $em,MailService $mail, UserPasswordHasherInterface $hasher): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $hasher->hashPassword($user,$form->get('password')->getData());
            $user->setPassword($password);
            $em->persist($user);
            $em->flush();
            $mail->subscribeMail($user);
            return $this->redirectToRoute('app_user_profil', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/active-account/{token}', name: 'active-account')]
    /**
     * Undocumented function
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
            // dd($user);
            $em->persist($user);
            $em->remove($tokens);
            $em->flush();
            
         $messageFlash = $translator->trans("Votre compte est maintenant activé bienvenu !").' '. $user->getPseudo();
         $this->addFlash('sucess', $messageFlash);
        }
   
        return  $this->redirectToRoute('login');
    }
}
