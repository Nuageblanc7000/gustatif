<?php

namespace App\Service;

use App\Entity\Restaurant;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\DeleteImagesEntityService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\ParameterBag;

/**permet de supprimer un restaurant complet avec ces liaisons et toutes les images liée */
class DeleteRestoService
{
    /**
     * 
     *
     * @param EntityManagerInterface $em
     * @param DeleteImagesEntityService $deleteImagesEntityService
     * @param ParameterBagInterface $parameterBag
     */
    public function __construct(public EntityManagerInterface $em, public DeleteImagesEntityService $deleteImagesEntityService , public ParameterBagInterface $parameterBag)
    {
    }

    /**
     * Permet de supprimer une entité restaurant en supprimant aussi les images liée à ces relations
     *
     * @param Restaurant $resto
     * @return void
     */
    public function destroy(Restaurant $resto): void
    {
        $images = $resto->getImages();
        $plats = $resto->getPlats();
        foreach ($images as $image) {
            $path = $image->getPath();
            $this->deleteImagesEntityService->setTargetDirectory([
                 $this->parameterBag->get('resto_images').'/'.$path,
                 $this->parameterBag->get('resto_mini_images').'/'.$path,
                 $this->parameterBag->get('resto_bg_images').'/'.$path,
            ]);
            $this->deleteImagesEntityService->delete($image);
        }

        foreach ($plats as  $plat) {
            $path = $plat->getImage();
            $this->deleteImagesEntityService->setTargetDirectory([
                $this->parameterBag->get('resto_plats'). '/'.$path,
                $this->parameterBag->get('resto_mini_plats') .'/'.$path,
                $this->parameterBag->get('resto_bg_plats').'/'.$path,
            ]);
            $this->deleteImagesEntityService->delete($plat);
        }
        $this->em->remove($resto);
        $this->em->flush();
    }
}
