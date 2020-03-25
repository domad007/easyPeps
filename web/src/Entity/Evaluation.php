<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\EvaluationRepository")
 */
class Evaluation
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
    private $intitule;

    /**
     * @ORM\Column(type="date")
     */
    private $dateEvaluation;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $heuresCompetence;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Competences", inversedBy="evaluations")
     */
    private $competence;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Groups", inversedBy="evaluations")
     */
    private $groupe;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $periode;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\EvaluationGroup", mappedBy="evaluation")
     */
    private $evaluationGroups;

    public function __construct()
    {
        $this->evaluationGroups = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIntitule(): ?string
    {
        return $this->intitule;
    }

    public function setIntitule(string $intitule): self
    {
        $this->intitule = $intitule;

        return $this;
    }

    public function getDateEvaluation(): ?\DateTimeInterface
    {
        return $this->dateEvaluation;
    }

    public function setDateEvaluation(\DateTimeInterface $dateEvaluation): self
    {
        $this->dateEvaluation = $dateEvaluation;

        return $this;
    }

    public function getHeuresCompetence(): ?string
    {
        return $this->heuresCompetence;
    }

    public function setHeuresCompetence(string $heuresCompetence): self
    {
        $this->heuresCompetence = $heuresCompetence;

        return $this;
    }

    public function getCompetence(): ?Competences
    {
        return $this->competence;
    }

    public function setCompetence(?Competences $competence): self
    {
        $this->competence = $competence;

        return $this;
    }

    public function getGroupe(): ?Groups
    {
        return $this->groupe;
    }

    public function setGroupe(?Groups $groupe): self
    {
        $this->groupe = $groupe;

        return $this;
    }

    public function getPeriode(): ?string
    {
        return $this->periode;
    }

    public function setPeriode(string $periode): self
    {
        $this->periode = $periode;

        return $this;
    }

    /**
     * @return Collection|EvaluationGroup[]
     */
    public function getEvaluationGroups(): Collection
    {
        return $this->evaluationGroups;
    }

    public function addEvaluationGroup(EvaluationGroup $evaluationGroup): self
    {
        if (!$this->evaluationGroups->contains($evaluationGroup)) {
            $this->evaluationGroups[] = $evaluationGroup;
            $evaluationGroup->setEvaluation($this);
        }

        return $this;
    }

    public function removeEvaluationGroup(EvaluationGroup $evaluationGroup): self
    {
        if ($this->evaluationGroups->contains($evaluationGroup)) {
            $this->evaluationGroups->removeElement($evaluationGroup);
            // set the owning side to null (unless already changed)
            if ($evaluationGroup->getEvaluation() === $this) {
                $evaluationGroup->setEvaluation(null);
            }
        }

        return $this;
    }
}
