<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CoursRepository")
 */
class Cours
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
    private $dateCours;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nombreHeures;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Groups", inversedBy="cours")
     */
    private $groupe;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\CoursGroupe", mappedBy="coursId")
     */
    private $coursGroupes;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Periodes", inversedBy="cours")
     */
    private $periode;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $surCombien;


    public function __construct()
    {
        $this->coursGroupes = new ArrayCollection();
        $this->periodes = new ArrayCollection();
        $this->evaluations = new ArrayCollection();
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

    public function getDateCours(): ?\DateTimeInterface
    {
        return $this->dateCours;
    }

    public function setDateCours(\DateTimeInterface $dateCours): self
    {
        $this->dateCours = $dateCours;

        return $this;
    }

    public function getNombreHeures(): ?int
    {
        return $this->nombreHeures;
    }

    public function setNombreHeures(string $nombreHeures): self
    {
        $this->nombreHeures = $nombreHeures;

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

    public function getPeriode(): ?Periodes
    {
        return $this->periode;
    }

    public function setPeriode(?Periodes $periode): self
    {
        $this->periode = $periode;

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
            $evaluation->setCours($this);
        }

        return $this;
    }

    public function removeEvaluation(Evaluation $evaluation): self
    {
        if ($this->evaluations->contains($evaluation)) {
            $this->evaluations->removeElement($evaluation);
            // set the owning side to null (unless already changed)
            if ($evaluation->getCours() === $this) {
                $evaluation->setCours(null);
            }
        }

        return $this;
    }

    public function getSurCombien(): ?string
    {
        return $this->surCombien;
    }

    public function setSurCombien(string $surCombien): self
    {
        $this->surCombien = $surCombien;

        return $this;
    }
}
