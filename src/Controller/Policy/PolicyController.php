<?php

namespace App\Controller\Policy;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PolicyController extends AbstractController
{
    /**
     * retourne la page de la politique de confidentialité
     *
     * @return Response
     */
    #[Route('/politique-de-confidentialite', name: 'app_policy')]
    public function privacy(): Response
    {
        return $this->render('privacy-policy/policy.html.twig', []);
    }

        /**
     * retourne la page de la politique de confidentialité
     *
     * @return Response
     */
    #[Route('/terms-conditions', name: 'app_terms')]
    public function terms(): Response
    {
        return $this->render('privacy-policy/terms-conditions.html.twig', []);
    }
}
