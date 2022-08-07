<?php

namespace App\Controller\user;

use App\Repository\TokenResolveRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
#[Route('profil')]
class ProfilController extends AbstractController
{
    #[Route('/', name: 'app_profil')]
    public function index(): Response
    {
        return $this->render('profil/index.html.twig', [
            'controller_name' => 'ProfilController',
        ]);
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
        

        return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
    }

}
