<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\EvaluationGroupRepository")
 */
class EvaluationGroup
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Evaluation", inversedBy="evaluationGroups")
     */
    private $evaluation;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Eleve", inversedBy="evaluationGroups")
     */
    private $eleve;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $points;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEvaluation(): ?Evaluation
    {
        return $this->evaluation;
    }

    public function setEvaluation(?Evaluation $evaluation): self
    {
        $this->evaluation = $evaluation;

        return $this;
    }

    public function getEleve(): ?Eleve
    {
        return $this->eleve;
    }

    public function setEleve(?Eleve $eleve): self
    {
        $this->eleve = $eleve;

        return $this;
    }

    public function getPoints(): ?string
    {
        return $this->points;
    }

    public function setPoints(string $points): self
    {
        $this->points = $points;

        return $this;
    }
}
