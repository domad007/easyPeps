<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PresencesRepository")
 */
class Presences
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
    private $libelle;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $abreviation;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\CoursGroupe", mappedBy="presences")
     */
    private $cours;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\CustomizedPresences", mappedBy="typePresence")
     */
    private $customizedPresences;

    public function __construct()
    {
        $this->cours = new ArrayCollection();
        $this->customizedPresences = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): self
    {
        $this->libelle = $libelle;

        return $this;
    }

    public function getAbreviation(): ?string
    {
        return $this->abreviation;
    }

    public function setAbreviation(string $abreviation): self
    {
        $this->abreviation = $abreviation;

        return $this;
    }

    /**
     * @return Collection|CoursGroupe[]
     */
    public function getCours(): Collection
    {
        return $this->cours;
    }

    public function addCour(CoursGroupe $cour): self
    {
        if (!$this->cours->contains($cour)) {
            $this->cours[] = $cour;
            $cour->setPresences($this);
        }

        return $this;
    }

    public function removeCour(CoursGroupe $cour): self
    {
        if ($this->cours->contains($cour)) {
            $this->cours->removeElement($cour);
            // set the owning side to null (unless already changed)
            if ($cour->getPresences() === $this) {
                $cour->setPresences(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|CustomizedPresences[]
     */
    public function getCustomizedPresences(): Collection
    {
        return $this->customizedPresences;
    }

    public function addCustomizedPresence(CustomizedPresences $customizedPresence): self
    {
        if (!$this->customizedPresences->contains($customizedPresence)) {
            $this->customizedPresences[] = $customizedPresence;
            $customizedPresence->setTypePresence($this);
        }

        return $this;
    }

    public function removeCustomizedPresence(CustomizedPresences $customizedPresence): self
    {
        if ($this->customizedPresences->contains($customizedPresence)) {
            $this->customizedPresences->removeElement($customizedPresence);
            // set the owning side to null (unless already changed)
            if ($customizedPresence->getTypePresence() === $this) {
                $customizedPresence->setTypePresence(null);
            }
        }

        return $this;
    }
}
