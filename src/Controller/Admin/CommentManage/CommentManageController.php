<?php

namespace App\Controller\Admin\CommentManage;

use App\Entity\Comment;
use App\Form\CommentType;
use App\Service\MailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CommentManageController extends AbstractController
{

    #[Route('/admin/manage/edit/comment/{id}', name: 'edit_manage_comment')]
    public function manageEditComment(TranslatorInterface $translator, EntityManagerInterface $em, Comment $comment, Request $req, MailService $mailService): Response
    {
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($req);
        if ($form->isSubmitted() and $form->isValid()) {
            $mailService->setSubject('Modification commentaire');
            $mailService->setContext($comment->getId());
            $mailService->setTemplate('@email_templates/modif-comment.html.twig');
            $mailService->mailToolService($comment->getAuthor());
            $message = $translator->trans('commentaire modifié'.' id: '.$comment->getId());
            $this->addFlash(
                'success',
                $message
            );
            $em->persist($comment);
            $em->flush();
            return $this->redirectToRoute('app_admin_comments');
        }
        return $this->render(
            '/admin/admin-comments-manage/comment_form_admin.html.twig',
            [
                'form' => $form->createView()
            ]
        );
    }

    #[Route('/admin/manage/delete/comment/{id}', name: 'delete_manage_comment')]
    public function manageDeleteComment(TranslatorInterface $translator, EntityManagerInterface $em, Comment $comment) : Response
    {
        $message = $translator->trans('commentaire supprimé'.' id: '.$comment->getId());
        $this->addFlash(
            'success',
            $message
        );
        $em->remove($comment);
        $em->flush();
        return  $this->redirectToRoute('app_admin_comments');
    }
}
