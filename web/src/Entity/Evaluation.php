<?php

namespace App\Entity;

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
     * @ORM\ManyToOne(targetEntity="App\Entity\Cours", inversedBy="evaluations")
     */
    private $cours;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Competences", inversedBy="evaluations")
     */
    private $competence;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $heuresCompetence;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCours(): ?Cours
    {
        return $this->cours;
    }

    public function setCours(?Cours $cours): self
    {
        $this->cours = $cours;

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

    public function getHeuresCompetence(): ?string
    {
        return $this->heuresCompetence;
    }

    public function setHeuresCompetence(string $heuresCompetence): self
    {
        $this->heuresCompetence = $heuresCompetence;

        return $this;
    }
}
