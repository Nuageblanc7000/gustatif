<?php
namespace App\Controller\Admin\CommentManage;

use App\Entity\Comment;
use App\Form\CommentType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CommentManageController extends AbstractController{

    #[Route('/admin/manage/edit/comment/{id}',name:'edit_manage_comment')]
    public function manageEditComment( TranslatorInterface $translator, EntityManagerInterface $em, Comment $comment, Request $req): Response
    {
    $form = $this->createForm(CommentType::class,$comment);
    $form->handleRequest($req);
    $message = $translator->trans('commentaire modifié');
        $this->addFlash(
           'success',
           $message 
        );
        if ($form->isSubmitted() and $form->isValid()) {
            $em->persist($comment);
            $em->flush();
            return $this->redirectToRoute('app_admin_comments');
        }
        return $this->render('/admin/admin-comments-manage/comment_form_admin.html.twig',
    [
        'form' => $form->createView()
    ]
    );
    }
}