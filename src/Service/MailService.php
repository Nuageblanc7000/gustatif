<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\TokenResolve;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class MailService
{
    public function __construct(private MailerInterface $mailer  , private TranslatorInterface $translator, private EntityManagerInterface $em)
    {
    }

    /**
     * permet d'envoyer un mail lors de l'inscription d'un user avec token
     *
     * @param User $user
     * @return void
     */
    public function subscribeMail(User $user)
    {
        $subject = $this->translator->trans('Bienvenu sur l\'application!');
        $token = substr(str_replace(['-','/','_','+'],'',base64_encode( random_bytes(35))),0,30);
        $tokenResolve = new TokenResolve();
        $tokenResolve->setToken($token)
                     ->setUserCurrent($user);
        $this->em->persist($tokenResolve);
        $this->em->flush();
        $email = new TemplatedEmail();
        $email->to($user->getEmail())
                        ->subject($subject)
                            ->htmlTemplate('@email_templates/welcome.html.twig')
                            ->context([
                                'pseudo' => $user->getPseudo(),
                                'token' => $token
                                ])
                                ;
        return $this->mailer->send($email);
    }
}