<?php

namespace App\Controller\Admin;

use App\Service\StatService;
use App\Data\DataAdminFilter;
use App\Form\AdminFilterUserType;
use App\Repository\CommentRepository;
use App\Repository\RestaurantRepository;
use App\Repository\UserRepository;
use Symfony\UX\Chartjs\Model\Chart;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DashBoardController extends AbstractController
{
    /**
     * permet d'afficher les stats du sites
     *
     * @param ChartBuilderInterface $chart
     * @param TranslatorInterface $translator
     * @param StatService $statService
     * @return Response
     */
    #[Route('/admin', name: 'app_admin')]
    public function dashBoard(ChartBuilderInterface $chart, TranslatorInterface $translator, StatService $statService): Response
    {
        $chart = $chart->createChart(Chart::TYPE_PIE);
        $chart->setData([
            'labels' => [$translator->trans('Compte nom vérifié'),$translator->trans('compte vérifié')],
            'datasets' => [
                [
                    
                    'backgroundColor' => ["#ef2917","#FFB714" ],
                    'borderColor' => '',
                    'data' => [$statService->getCountUsersNotVerified(),$statService->getCountUsersVerified()],
                ],
            ],
        ]);
        $chart->setOptions([
            'scales' => [
                'y' => [
                    'suggestedMin' => 0,
                    'suggestedMax' => $statService->getCountUsers(),
                ],
            ],
        ]);

        return $this->render('/admin/dash_board/index.html.twig', [
            'chart' => $chart,
            'usersCount' => $statService->getCountUsers(),
            'restosCount' => $statService->getCountRestos()
        ]);
    }

    /**
     * page utilisateurs avec paginations (gestion utilisateur)
     *
     * @param UserRepository $userRepository
     * @param Request $req
     * @param PaginatorInterface $paginatorInterface
     * @return Response
     */
    #[Route('/admin/users/manage', name: 'app_admin_users')]
    public function userManage(UserRepository $userRepository,Request $req, PaginatorInterface $paginatorInterface): Response
    {
        $data = new DataAdminFilter();
        $form = $this->createForm(AdminFilterUserType::class, $data);
        $form->handleRequest($req);
        $paginator = $paginatorInterface->paginate($userRepository->findFilterUserAdmin($data), $req->query->getInt('page', 1),10);
        if ($form->isSubmitted() && $form->isValid()) {
        }
        return $this->renderForm('/admin/dash_board/users-manage.html.twig', [
            'users' => $paginator,
            'form' => $form
        ]);
    }



    /**
     * permet de gérer les restaurants
     *
     * @param RestaurantRepository $restaurantRepository
     * @param Request $req
     * @param PaginatorInterface $paginatorInterface
     * @return Response
     */
    #[Route('/admin/restos/manage', name: 'app_admin_restos')]
    public function restosManage(RestaurantRepository $restaurantRepository,Request $req, PaginatorInterface $paginatorInterface): Response
    {
        $data = new DataAdminFilter();
        $form = $this->createForm(AdminFilterUserType::class, $data);
        $form->handleRequest($req);
        $paginator = $paginatorInterface->paginate($restaurantRepository->findFilterRestoAdmin($data), $req->query->getInt('page', 1),10);
        if ($form->isSubmitted() && $form->isValid()) {
        }
        return $this->renderForm('/admin/dash_board/restos-manage.html.twig', [
            'restos' => $paginator,
            'form' => $form
        ]);
    }

    #[Route('/admin/comments/manage', name: 'app_admin_comments')]
    public function commentsManage(CommentRepository $commentRepository,Request $req, PaginatorInterface $paginatorInterface): Response
    {
        $data = new DataAdminFilter();
        $form = $this->createForm(AdminFilterUserType::class, $data);
        $form->handleRequest($req);
        return $this->render('/admin/dash_board/comments-manage.html.twig', [
            'comments' => $commentRepository->findAll(),
            'form' => $form->createView()
        ]);
    }
}
