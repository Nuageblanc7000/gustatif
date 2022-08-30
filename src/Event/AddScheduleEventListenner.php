<?php

namespace App\Event;

use App\Entity\Conference;
use App\Entity\Restaurant;
use App\Entity\Schedule;
use App\Entity\Timetable;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\String\Slugger\SluggerInterface;
/**
 * permet l'ajout automatique d'un horaire prè construit à la création d'une entity restaurant
 * 
 */
class AddScheduleEventListenner
{
    private $slugger;
    /**
     * Undocumented function
     *
     * @param SluggerInterface $slugger
     */
    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }
    /**
     * on utilise l'event prePersit pour enregistrer les donnée lors de la création de l'entity
     *
     * @param Restaurant $resto
     * @param LifecycleEventArgs $event
     * @return void
     */
    public function prePersist(Restaurant $resto, LifecycleEventArgs $event)
    {
        $schedule = new Schedule();
        $event->getObjectManager()->persist($schedule);
        for ($i=0; $i < 7 ; $i++) { 
            $timetable = new Timetable();
            $timetable->setDay($i);
            $event->getObjectManager()->persist($timetable);
            $schedule->addTimetable($timetable);
        }
        $resto->setSchedule($schedule);
    }
}