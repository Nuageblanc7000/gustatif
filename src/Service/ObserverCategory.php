<?php 
namespace App\Service;

use App\Repository\CategoryRepository;
final class ObserverCategory{
public function  __construct(public CategoryRepository $categoryRepository){}

public function getObserver(){
    $fast = $this->categoryRepository->findOneBy(['name' => 'fast-food']);
       $restoId = $this->categoryRepository->findOneBy(['name' => 'restaurant']);
    return ['fastId'=>$fast,'restoId'=>$restoId];
}

}