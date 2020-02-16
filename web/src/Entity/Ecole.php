<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\EcoleRepository")
 */
class Ecole
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Classe", mappedBy="ecole")
     */
    private $ecole;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nomEcole;

    public function __construct()
    {
        $this->ecole = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection|Classe[]
     */
    public function getEcole(): Collection
    {
        return $this->ecole;
    }

    public function addEcole(Classe $ecole): self
    {
        if (!$this->ecole->contains($ecole)) {
            $this->ecole[] = $ecole;
            $ecole->setEcole($this);
        }

        return $this;
    }

    public function removeEcole(Classe $ecole): self
    {
        if ($this->ecole->contains($ecole)) {
            $this->ecole->removeElement($ecole);
            // set the owning side to null (unless already changed)
            if ($ecole->getEcole() === $this) {
                $ecole->setEcole(null);
            }
        }

        return $this;
    }

    public function getNomEcole(): ?string
    {
        return $this->nomEcole;
    }

    public function setNomEcole(string $nomEcole): self
    {
        $this->nomEcole = $nomEcole;

        return $this;
    }
}
