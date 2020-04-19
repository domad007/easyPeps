<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SemestresRepository")
 */
class Semestres
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
     * @ORM\OneToMany(targetEntity="App\Entity\Periodes", mappedBy="semestres")
     */
    private $periode;

    public function __construct()
    {
        $this->periode = new ArrayCollection();
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

    /**
     * @return Collection|Periodes[]
     */
    public function getPeriode(): Collection
    {
        return $this->periode;
    }

    public function addPeriode(Periodes $periode): self
    {
        if (!$this->periode->contains($periode)) {
            $this->periode[] = $periode;
            $periode->setSemestres($this);
        }

        return $this;
    }

    public function removePeriode(Periodes $periode): self
    {
        if ($this->periode->contains($periode)) {
            $this->periode->removeElement($periode);
            // set the owning side to null (unless already changed)
            if ($periode->getSemestres() === $this) {
                $periode->setSemestres(null);
            }
        }

        return $this;
    }
}
