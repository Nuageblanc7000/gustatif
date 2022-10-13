<?php

namespace App\Controller\Locales;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class LocaleController extends AbstractController
{
 #[Route('/locale/{locale}', name:"locale")]
 public function locale($locale,Request $req){
    $languages = $this->getParameter('app.locales');

    if(!in_array($locale,$languages)){
      $locale = 'fr';
    }
    $req->getSession()->set('_locale',$locale);
    $referer = $req->headers->get('referer');
    if($referer === null){
      return $this->redirectToRoute('home');
    }else{  
       return $this->redirect($req->headers->get('referer'));
      }
 }
}
