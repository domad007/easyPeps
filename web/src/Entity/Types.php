<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TypesRepository")
 */
class Types
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
     * @ORM\OneToMany(targetEntity="App\Entity\Competences", mappedBy="typeCompetence")
     */
    private $competences;

    public function __construct()
    {
        $this->possede = new ArrayCollection();
        $this->competences = new ArrayCollection();
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
     * @return Collection|Competences[]
     */
    public function getPossede(): Collection
    {
        return $this->possede;
    }

    public function addPossede(Competences $possede): self
    {
        if (!$this->possede->contains($possede)) {
            $this->possede[] = $possede;
            $possede->setTypes($this);
        }

        return $this;
    }

    public function removePossede(Competences $possede): self
    {
        if ($this->possede->contains($possede)) {
            $this->possede->removeElement($possede);
            // set the owning side to null (unless already changed)
            if ($possede->getTypes() === $this) {
                $possede->setTypes(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Competences[]
     */
    public function getCompetences(): Collection
    {
        return $this->competences;
    }

    public function addCompetence(Competences $competence): self
    {
        if (!$this->competences->contains($competence)) {
            $this->competences[] = $competence;
            $competence->setTypeCompetence($this);
        }

        return $this;
    }

    public function removeCompetence(Competences $competence): self
    {
        if ($this->competences->contains($competence)) {
            $this->competences->removeElement($competence);
            // set the owning side to null (unless already changed)
            if ($competence->getTypeCompetence() === $this) {
                $competence->setTypeCompetence(null);
            }
        }

        return $this;
    }
}
