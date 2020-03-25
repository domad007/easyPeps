<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DegreRepository")
 */
class Degre
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
     * @ORM\OneToMany(targetEntity="App\Entity\Competences", mappedBy="degre")
     */
    private $degre;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Groups", mappedBy="degre")
     */
    private $groups;

    public function __construct()
    {
        $this->degre = new ArrayCollection();
        $this->groups = new ArrayCollection();
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
    public function getDegre(): Collection
    {
        return $this->degre;
    }

    public function addDegre(Competences $degre): self
    {
        if (!$this->degre->contains($degre)) {
            $this->degre[] = $degre;
            $degre->setDegre($this);
        }

        return $this;
    }

    public function removeDegre(Competences $degre): self
    {
        if ($this->degre->contains($degre)) {
            $this->degre->removeElement($degre);
            // set the owning side to null (unless already changed)
            if ($degre->getDegre() === $this) {
                $degre->setDegre(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Groups[]
     */
    public function getGroups(): Collection
    {
        return $this->groups;
    }

    public function addGroup(Groups $group): self
    {
        if (!$this->groups->contains($group)) {
            $this->groups[] = $group;
            $group->setDegre($this);
        }

        return $this;
    }

    public function removeGroup(Groups $group): self
    {
        if ($this->groups->contains($group)) {
            $this->groups->removeElement($group);
            // set the owning side to null (unless already changed)
            if ($group->getDegre() === $this) {
                $group->setDegre(null);
            }
        }

        return $this;
    }
}
