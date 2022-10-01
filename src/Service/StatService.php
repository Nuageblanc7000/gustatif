<?php
namespace App\Service;

use App\Repository\UserRepository;
use App\Repository\RestaurantRepository;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\UX\Chartjs\Model\Chart;

class StatService{
  public function  __construct(public TranslatorInterface $translator,public UserRepository $userRepository, public RestaurantRepository $restaurantRepository, public ChartBuilderInterface $chart){}
  
/**
 * retoune le nombre de restaurants sur le site
 *
 * @return integer|null
 */
  public function getCountRestos() : ?int  
  {
    $countResto = $this->restaurantRepository->findCountRetos() ?? 0;
    return $countResto;
  }
  /**
   * retoune le nombre de users
   *
   * @return integer
   */
  public function getCountUsers() : ?int  
  {
    $counUser = $this->getCountUsersNotVerified() + $this->getCountUsersVerified();
    return $counUser;
  }

  /**
   * retourne le nombre de compte vérifier
   *
   * @return integer|null
   */
  public function getCountUsersVerified() : ?int  
  {
    $verifAcount = $this->userRepository->findCountVerified() ?? 0;
    return $verifAcount;
  }
   /**
   * retourne le nombre de compte non vérifier
   *
   * @return integer|null
   */
  public function getCountUsersNotVerified() : ?int  
  {
    $verifAcount = $this->userRepository->findCountNotVerified() ?? 0;
    return $verifAcount;
  }
}