<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\EcoleRepository")
 */
class Ecole
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nomEcole;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Classe", mappedBy="ecole")
     */
    private $classes;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Ponderation", mappedBy="ecole")
     */
    private $Evaluation;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Ponderation", mappedBy="ecole")
     */
    private $ponderations;

    public function __construct()
    {
        $this->classes = new ArrayCollection();
        $this->Evaluation = new ArrayCollection();
        $this->ponderations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomEcole(): ?string
    {
        return $this->nomEcole;
    }

    public function setNomEcole(string $nomEcole): self
    {
        $this->nomEcole = $nomEcole;

        return $this;
    }

    /**
     * @return Collection|Classe[]
     */
    public function getClasses(): Collection
    {
        return $this->classes;
    }

    public function addClass(Classe $class): self
    {
        if (!$this->classes->contains($class)) {
            $this->classes[] = $class;
            $class->setEcole($this);
        }

        return $this;
    }

    public function removeClass(Classe $class): self
    {
        if ($this->classes->contains($class)) {
            $this->classes->removeElement($class);
            // set the owning side to null (unless already changed)
            if ($class->getEcole() === $this) {
                $class->setEcole(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Ponderation[]
     */
    public function getEvaluation(): Collection
    {
        return $this->Evaluation;
    }

    public function addEvaluation(Ponderation $evaluation): self
    {
        if (!$this->Evaluation->contains($evaluation)) {
            $this->Evaluation[] = $evaluation;
            $evaluation->setEcole($this);
        }

        return $this;
    }

    public function removeEvaluation(Ponderation $evaluation): self
    {
        if ($this->Evaluation->contains($evaluation)) {
            $this->Evaluation->removeElement($evaluation);
            // set the owning side to null (unless already changed)
            if ($evaluation->getEcole() === $this) {
                $evaluation->setEcole(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Ponderation[]
     */
    public function getPonderations(): Collection
    {
        return $this->ponderations;
    }

    public function addPonderation(Ponderation $ponderation): self
    {
        if (!$this->ponderations->contains($ponderation)) {
            $this->ponderations[] = $ponderation;
            $ponderation->setEcole($this);
        }

        return $this;
    }

    public function removePonderation(Ponderation $ponderation): self
    {
        if ($this->ponderations->contains($ponderation)) {
            $this->ponderations->removeElement($ponderation);
            // set the owning side to null (unless already changed)
            if ($ponderation->getEcole() === $this) {
                $ponderation->setEcole(null);
            }
        }

        return $this;
    }
}
