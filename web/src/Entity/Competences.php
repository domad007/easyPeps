<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CompetencesRepository")
 */
class Competences
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
    private $nom;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Types", inversedBy="competences")
     */
    private $typeCompetence;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Evaluation", mappedBy="competences")
     */
    private $evaluations;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Degre", inversedBy="degre")
     */
    private $degre;

    public function __construct()
    {
        $this->evaluation = new ArrayCollection();
        $this->evaluations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getTypeCompetence(): ?Types
    {
        return $this->typeCompetence;
    }

    public function setTypeCompetence(?Types $typeCompetence): self
    {
        $this->typeCompetence = $typeCompetence;

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
            $evaluation->setCompetences($this);
        }

        return $this;
    }

    public function removeEvaluation(Evaluation $evaluation): self
    {
        if ($this->evaluations->contains($evaluation)) {
            $this->evaluations->removeElement($evaluation);
            // set the owning side to null (unless already changed)
            if ($evaluation->getCompetences() === $this) {
                $evaluation->setCompetences(null);
            }
        }

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
}
