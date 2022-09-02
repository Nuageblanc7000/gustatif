<?php

namespace App\Controller\user\profilResto;

use App\Form\UserEditType;
use App\Repository\UserRepository;
use Symfony\UX\Chartjs\Model\Chart;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\TokenResolveRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
#[Route('/profil')]
class ProfilRestoController extends AbstractController
{
    #[Route('/', name: 'app_profil')]
    /**
     * Permet d'afficher le profil !RESTAURATEUR!
     * CHART JS POUR LE GRAPHIQUE
     * @param ChartBuilderInterface $chartBuilder
     * @return Response
     */
    public function ProfilResto(ChartBuilderInterface $chartBuilder): Response
    {   
        $user = $this->getUser();

        $chart = $chartBuilder->createChart(Chart::TYPE_LINE);
        //ici pour connaitre le nombre de commentaire du restaurant
        $chart->setData([
            'labels' => ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet'],
            'datasets' => [
                [
                    'label' => 'Nombre de visite sur la page',
                    'backgroundColor' => '#feaa3a',
                    'borderColor' => '#feaa3a',
                    'data' => [0, 10, 5, 2, 20, 30, 45],
                ],
            ],
        ]);
        $chart->setOptions([
            'scales' => [
                'y' => [
                    'suggestedMin' => 0,
                    'suggestedMax' => 100,
                ],
            ],
        ]);
        
        return $this->render('profil/index.html.twig', [
            'chart' => $chart,
        ]);
    }

   
}
