<?php

namespace App\Controller;

use App\Entity\Like;

use App\Entity\Comment;

use App\Data\DataFilter;
use App\Form\FilterType;
use App\Form\CommentType;
use App\Entity\Restaurant;
use App\Repository\LikeRepository;
use App\Repository\CommentRepository;
use App\Repository\RestaurantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class RestoController extends AbstractController
{

    /**
     * Affichage de tous les restaurants
     *
     * @param RestaurantRepository $repo
     * @param Request $req
     * @return Response
     */
    #[Route('/restaurants', name: 'restos_list')]
    public function restaurants(RestaurantRepository $repo, Request $req ): Response
    {
        $data = new DataFilter();
        $form = $this->createForm(FilterType::class, $data);
        $form->handleRequest($req);
        if ($form->isSubmitted() && $form->isValid()) {
        }
        return $this->render('restaurant/restaurants.html.twig', [
            'restos' => $repo->restoPaginator($data),
            'form' => $form->createView()
        ]);
    }

    

    /**
     * Affichage d'un restaurant en particulier + donnée pour la carte
     *
     * @param Restaurant $resto
     * @param Request $req
     * @param EntityManagerInterface $em
     * @param CommentRepository $commentRepository
     * @return Response
     */
    #[Route('/restaurant/{id}', name: 'resto_view')]
    public function restaurant(Restaurant $resto, Request $req, EntityManagerInterface $em, CommentRepository $commentRepository,TranslatorInterface $translator): Response
    {   
        $comment = new Comment();
        $user= $this->getUser();
        $form = $this->createForm(CommentType::class,$comment);
        $form->handleRequest($req);
        if($form->isSubmitted() && $form->isValid())
        {
            $comment->setAuthor($user)
                    ->setResto($resto);
            $em->persist($comment);
            $em->flush();
            $message = $translator->trans('L\'établissement ' .$resto->getName(). ' vous remercie pour votre avis' );
            $this->addFlash('success',$message);
            return $this->redirect($req->headers->get('referer'),Response::HTTP_FOUND);
        }
        $longi = $resto->getCity()->getLongitude();
        $lati = $resto->getCity()->getLatitude();
        return $this->render('restaurant/restaurant.html.twig', [
            'resto' => $resto,
            'longi' => $longi,
            'lati' => $lati,
            'form' => $form->createView(),
            'repo' => $commentRepository->findBy(['resto' => $resto],['id' =>  'DESC']),
        ]);
    }


    #[Route('/like/{id}',name:'like_resto',methods:['POST'])]
    public function likeResto(Request $req, Restaurant $resto, EntityManagerInterface $em ,LikeRepository $likeRepository): Response
    { 
        $user = $this->getUser();
        
        if(!$user){
            $pageLogin = $this->generateUrl('login');
                return $this->json(['route' => $pageLogin,200]);
            }
        if($user !== $resto->getUser()){
            if(!$resto->isLikeByUser($user))
            {
                $like = new Like();
                $like->setUser($user)
                ->setRestaurant($resto);
                $em->persist($like);
                $em->flush();
                return $this->json(['response'=>'like'],200);

            }else{
                $like = $likeRepository->findOneBy(['restaurant'=> $resto ,'user'=> $user]);
                $em->remove($like);
                $em->flush();
                
                return $this->json(['response'=>'dislike'],200);
            }
        }else{
            return  throw new AccessDeniedHttpException(message:'Accès refusé',code:403);
        }
        return  throw new NotFoundHttpException(code:404);
    
}
    
}
