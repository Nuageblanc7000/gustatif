<?php

namespace App\Controller\user\profilUser;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/profil/utilisateur')]
class ProfilUserController extends AbstractController
{
    #[Route('/', name: 'app_profil_user')]
    public function index(): Response
    {
        $user = $this->getUser();
        $this->denyAccessUnlessGranted('USER_VIEW',$user);
        
        return $this->render('profil_user/index.html.twig', [
            'controller_name' => 'ProfilUserController',
        ]);
    }
}
