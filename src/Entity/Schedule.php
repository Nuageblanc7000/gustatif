<?php

namespace App\Entity;

use App\Repository\ScheduleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ScheduleRepository::class)]
class Schedule
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToMany(mappedBy: 'schedule', targetEntity: Timetable::class,orphanRemoval:true)]
    private Collection $timetables;

    #[ORM\OneToOne(mappedBy: 'schedule', cascade: ['persist', 'remove'])]
    private ?Restaurant $restaurant = null;

    public function __construct()
    {
        $this->timetables = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, Timetable>
     */
    public function getTimetables(): Collection
    {
        return $this->timetables;
    }

    public function addTimetable(Timetable $timetable): self
    {
        if (!$this->timetables->contains($timetable)) {
            $this->timetables->add($timetable);
            $timetable->setSchedule($this);
        }

        return $this;
    }

    public function removeTimetable(Timetable $timetable): self
    {
        if ($this->timetables->removeElement($timetable)) {
            // set the owning side to null (unless already changed)
            if ($timetable->getSchedule() === $this) {
                $timetable->setSchedule(null);
            }
        }

        return $this;
    }

    public function getRestaurant(): ?Restaurant
    {
        return $this->restaurant;
    }

    public function setRestaurant(?Restaurant $restaurant): self
    {
        // unset the owning side of the relation if necessary
        if ($restaurant === null && $this->restaurant !== null) {
            $this->restaurant->setSchedule(null);
        }

        // set the owning side of the relation if necessary
        if ($restaurant !== null && $restaurant->getSchedule() !== $this) {
            $restaurant->setSchedule($this);
        }

        $this->restaurant = $restaurant;

        return $this;
    }
}
