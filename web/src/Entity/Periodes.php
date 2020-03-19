<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PeriodesRepository")
 */
class Periodes
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
    private $nomPeriode;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $pourcentage;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Groups", inversedBy="periodes")
     */
    private $groupe;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Cours", mappedBy="periode")
     */
    private $cours;

    public function __construct()
    {
        $this->cours = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomPeriode(): ?string
    {
        return $this->nomPeriode;
    }

    public function setNomPeriode(string $nomPeriode): self
    {
        $this->nomPeriode = $nomPeriode;

        return $this;
    }

    public function getPourcentage(): ?string
    {
        return $this->pourcentage;
    }

    public function setPourcentage(string $pourcentage): self
    {
        $this->pourcentage = $pourcentage;

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
            $cour->setPeriode($this);
        }

        return $this;
    }

    public function removeCour(Cours $cour): self
    {
        if ($this->cours->contains($cour)) {
            $this->cours->removeElement($cour);
            // set the owning side to null (unless already changed)
            if ($cour->getPeriode() === $this) {
                $cour->setPeriode(null);
            }
        }

        return $this;
    }
}
