<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\GroupsRepository")
 */
class Groups
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Classe", mappedBy="groups")
     */
    private $classes;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Cours", mappedBy="groupe")
     */
    private $cours;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Periodes", mappedBy="groupe")
     */
    private $periodes;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $calculAutomatique;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Degre", inversedBy="groups")
     */
    private $degre;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Evaluation", mappedBy="groupe")
     */
    private $evaluations;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="groups")
     */
    private $professeur;

    public function __construct()
    {
        $this->classes = new ArrayCollection();
        $this->cours = new ArrayCollection();
        $this->evaluations = new ArrayCollection();
        $this->periodes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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
            $class->setGroups($this);
        }

        return $this;
    }

    public function removeClass(Classe $class): self
    {
        if ($this->classes->contains($class)) {
            $this->classes->removeElement($class);
            // set the owning side to null (unless already changed)
            if ($class->getGroups() === $this) {
                $class->setGroups(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Cours[]
     */
    public function getCours(): Collection
    {
        return $this->cours;
    }

    public function addCour(Cours $cour): self
    {
        if (!$this->cours->contains($cour)) {
            $this->cours[] = $cour;
            $cour->setGroupe($this);
        }

        return $this;
    }

    public function removeCour(Cours $cour): self
    {
        if ($this->cours->contains($cour)) {
            $this->cours->removeElement($cour);
            // set the owning side to null (unless already changed)
            if ($cour->getGroupe() === $this) {
                $cour->setGroupe(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Periodes[]
     */
    public function getPeriodes(): Collection
    {
        return $this->periodes;
    }

    public function addPeriode(Periodes $periode): self
    {
        if (!$this->periodes->contains($periode)) {
            $this->periodes[] = $periode;
            $periode->setGroupe($this);
        }

        return $this;
    }

    public function removePeriode(Periodes $periode): self
    {
        if ($this->periodes->contains($periode)) {
            $this->periodes->removeElement($periode);
            // set the owning side to null (unless already changed)
            if ($periode->getGroupe() === $this) {
                $periode->setGroupe(null);
            }
        }

        return $this;
    }

    public function getCalculAutomatique(): ?int
    {
        return $this->calculAutomatique;
    }

    public function setCalculAutomatique(?int $calculAutomatique): self
    {
        $this->calculAutomatique = $calculAutomatique;

        return $this;
    }

    public function getDegre(): ?Degre
    {
        return $this->degre;
    }

    public function setDegre(?Degre $degre): self
    {
        $this->degre = $degre;

        return $this;
    }

    /**
     * @return Collection|Evaluation[]
     */
    public function getEvaluations(): Collection
    {
        return $this->evaluations;
    }

    public function addEvaluation(Evaluation $evaluation): self
    {
        if (!$this->evaluations->contains($evaluation)) {
            $this->evaluations[] = $evaluation;
            $evaluation->setGroupe($this);
        }

        return $this;
    }

    public function removeEvaluation(Evaluation $evaluation): self
    {
        if ($this->evaluations->contains($evaluation)) {
            $this->evaluations->removeElement($evaluation);
            // set the owning side to null (unless already changed)
            if ($evaluation->getGroupe() === $this) {
                $evaluation->setGroupe(null);
            }
        }

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
}
