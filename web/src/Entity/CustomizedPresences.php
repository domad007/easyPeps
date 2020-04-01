<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CustomizedPresencesRepository")
 */
class CustomizedPresences
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Presences", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $typePresence;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="customizedPresences")
     */
    private $user;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $abreviationCustomized;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\CoursGroupe", mappedBy="customizedPresences")
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

    public function getTypePresence(): ?Presences
    {
        return $this->typePresence;
    }

    public function setTypePresence(Presences $typePresence): self
    {
        $this->typePresence = $typePresence;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getAbreviationCustomized(): ?string
    {
        return $this->abreviationCustomized;
    }

    public function setAbreviationCustomized(string $abreviationCustomized): self
    {
        $this->abreviationCustomized = $abreviationCustomized;

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
            $cour->setCustomizedPresences($this);
        }

        return $this;
    }

    public function removeCour(CoursGroupe $cour): self
    {
        if ($this->cours->contains($cour)) {
            $this->cours->removeElement($cour);
            // set the owning side to null (unless already changed)
            if ($cour->getCustomizedPresences() === $this) {
                $cour->setCustomizedPresences(null);
            }
        }

        return $this;
    }
}
