<?php

namespace App\Entity;

use App\Repository\TimeTableRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TimeTableRepository::class)]
class Timetable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $open = null;

    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $close = null;

    #[ORM\Column]
    private ?int $day = null;

    #[ORM\ManyToOne(inversedBy: 'timetables')]
    private ?Schedule $schedule = null;

    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $openpm = null;

    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $closepm = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOpen(): ?\DateTimeInterface
    {
        return $this->open;
    }

    public function setOpen(?\DateTimeInterface $open): self
    {
        $this->open = $open;

        return $this;
    }

    public function getClose(): ?\DateTimeInterface
    {
        return $this->close;
    }

    public function setClose(?\DateTimeInterface $close): self
    {
        $this->close = $close;

        return $this;
    }

    public function getDay(): ?int
    {
        return $this->day;
    }

    public function setDay(int $day): self
    {
        $this->day = $day;

        return $this;
    }

    public function getSchedule(): ?Schedule
    {
        return $this->schedule;
    }

    public function setSchedule(?Schedule $schedule): self
    {
        $this->schedule = $schedule;

        return $this;
    }

    public function getOpenpm(): ?\DateTimeInterface
    {
        return $this->openpm;
    }

    public function setOpenpm(?\DateTimeInterface $openpm): self
    {
        $this->openpm = $openpm;

        return $this;
    }

    public function getClosepm(): ?\DateTimeInterface
    {
        return $this->closepm;
    }

    public function setClosepm(?\DateTimeInterface $closepm): self
    {
        $this->closepm = $closepm;

        return $this;
    }
}
