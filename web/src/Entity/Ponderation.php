<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PonderationRepository")
 */
class Ponderation
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Ecole", inversedBy="ponderations")
     */
    private $ecole;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\User", cascade={"persist", "remove"})
     */
    private $professeur;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $evaluation;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $cours;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEcole(): ?Ecole
    {
        return $this->ecole;
    }

    public function setEcole(?Ecole $ecole): self
    {
        $this->ecole = $ecole;

        return $this;
    }

    public function getProfesseur(): ?User
    {
        return $this->professeur;
    }

    public function setProfesseur(?User $professeur): self
    {
        $this->professeur = $professeur;

        return $this;
    }

    public function getEvaluation(): ?string
    {
        return $this->evaluation;
    }

    public function setEvaluation(string $evaluation): self
    {
        $this->evaluation = $evaluation;

        return $this;
    }

    public function getCours(): ?string
    {
        return $this->cours;
    }

    public function setCours(string $cours): self
    {
        $this->cours = $cours;

        return $this;
    }
}
