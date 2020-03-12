<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CoursGroupeRepository")
 */
class CoursGroupe
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Cours", inversedBy="coursGroupes")
     */
    private $coursId;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Eleve", inversedBy="coursGroupes")
     */
    private $eleveId;

    /**
     * @ORM\Column(type="integer")
     */
    private $points;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCoursId(): ?Cours
    {
        return $this->coursId;
    }

    public function setCoursId(?Cours $coursId): self
    {
        $this->coursId = $coursId;

        return $this;
    }

    public function getEleveId(): ?Eleve
    {
        return $this->eleveId;
    }

    public function setEleveId(?Eleve $eleveId): self
    {
        $this->eleveId = $eleveId;

        return $this;
    }

    public function getPoints(): ?int
    {
        return $this->points;
    }

    public function setPoints(int $points): self
    {
        $this->points = $points;

        return $this;
    }
}
