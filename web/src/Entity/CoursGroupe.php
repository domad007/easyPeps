<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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
     * @ORM\Column(type="string", length=255)
     */
    private $points = "6";

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Presences", inversedBy="cours")
     */
    private $presences;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\CustomizedPresences", inversedBy="cours")
     */
    private $customizedPresences;

    public function __construct()
    {
        $this->presences = new ArrayCollection();
    }


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

    public function setPoints(string $points): self
    {
        $this->points = $points;

        return $this;
    }

    public function getPresences(): ?Presences
    {
        return $this->presences;
    }

    public function setPresences(?Presences $presences): self
    {
        $this->presences = $presences;

        return $this;
    }

    public function getCustomizedPresences(): ?CustomizedPresences
    {
        return $this->customizedPresences;
    }

    public function setCustomizedPresences(?CustomizedPresences $customizedPresences): self
    {
        $this->customizedPresences = $customizedPresences;

        return $this;
    }

}
