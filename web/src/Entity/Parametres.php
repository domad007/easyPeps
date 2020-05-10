<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ParametresRepository")
 */
class Parametres
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Ecole", inversedBy="parametres")
     */
    private $ecole;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="parametres")
     */
    private $professeur;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $type;

    /**
     * @ORM\Column(type="boolean")
     */
    private $appreciation;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $surCombien;

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

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getAppreciation(): ?bool
    {
        return $this->appreciation;
    }

    public function setAppreciation(bool $appreciation): self
    {
        $this->appreciation = $appreciation;

        return $this;
    }

    public function getSurCombien(): ?string
    {
        return $this->surCombien;
    }

    public function setSurCombien(?string $surCombien): self
    {
        $this->surCombien = $surCombien;

        return $this;
    }
}
