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
    private $subject;
    private $template;
    private $context;

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
        $subject = $this->translator->trans('Bienvenue sur l\'application!');
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

    public function mailToolService(User $user)
    {
        $subject = $this->translator->trans($this->getSubject());
        $email = new TemplatedEmail();
        $email->to($user->getEmail())
                        ->subject($subject)
                            ->htmlTemplate($this->getTemplate())
                            ->context([
                                'user' => $user,
                                'context' => $this->getContext()
                                ])
                                ;
        return $this->mailer->send($email);
    }
    /**
     * permet de lire le sujet
     *
     * @return string
     */
    public function getSubject() : string
    {
        return $this->subject;
    }
    /**
     * permet de récupérer un sujet en string
     *
     * @param string $subject
     * @return self
     */
    public function setSubject(string $subject): self
    {
        if(!empty($subject)){
            $this->subject = $subject;    
        }else{
            $this->$subject = '';
        }
        return $this;
    }

    public function getContext() : mixed
    {
        return $this->context;
    }

    public function setContext(mixed $context) : self
    {
        $this->context = $context;
        return $this;
    }

    public function getTemplate() : string
    {
        return $this->template;
    }

    public function setTemplate(string $template) : self
    {
        $this->template = $template;
        return $this;
    }
}
