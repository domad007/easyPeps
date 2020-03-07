<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ClasseRepository")
 */
class Classe
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="classes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $professeur;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nomClasse;


    /**
     * @ORM\Column(type="string", length=255)
     */
    private $titulaire;


    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Eleve", mappedBy="classe", orphanRemoval=true)
     */
    private $eleves;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Ecole", inversedBy="classes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $ecole;

    public function __construct()
    {
        $this->eleves = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getNomClasse(): ?string
    {
        return $this->nomClasse;
    }

    public function setNomClasse(string $nomClasse): self
    {
        $this->nomClasse = $nomClasse;

        return $this;
    }


    public function getTitulaire(): ?string
    {
        return $this->titulaire;
    }

    public function setTitulaire(string $titulaire): self
    {
        $this->titulaire = $titulaire;

        return $this;
    }

    /**
     * @return Collection|Eleve[]
     */
    public function getEleves(): Collection
    {
        return $this->eleves;
    }

    public function addElefe(Eleve $elefe): self
    {
        if (!$this->eleves->contains($elefe)) {
            $this->eleves[] = $elefe;
            $elefe->setClasse($this);
        }

        return $this;
    }

    public function removeElefe(Eleve $elefe): self
    {
        if ($this->eleves->contains($elefe)) {
            $this->eleves->removeElement($elefe);
            // set the owning side to null (unless already changed)
            if ($elefe->getClasse() === $this) {
                $elefe->setClasse(null);
            }
        }

        return $this;
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

}
