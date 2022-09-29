<?php

namespace App\Controller\Locales;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class LocaleController extends AbstractController
{
 #[Route('/locale/{locale}', name:"locale")]
 public function locale($locale,Request $req){
    $req->getSession()->set('_locale',$locale);
    return $this->redirect($req->headers->get('referer'));
 }
}
