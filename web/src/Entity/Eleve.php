<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\EleveRepository")
 */
class Eleve
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Veuillez renseigner le nom")
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Veuillez renseigner le prénom")
     */
    private $prenom;

    /**
     * @ORM\Column(type="date")
     */
    private $dateNaissance;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Classe", inversedBy="eleves")
     * @ORM\JoinColumn(nullable=false)
     */
    private $classe;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\CoursGroupe", mappedBy="eleveId")
     */
    private $coursGroupes;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\EvaluationGroup", mappedBy="eleve")
     */
    private $evaluationGroups;

    private $moyennePeriodeCours;
    private $moyennePeriodeEval;
    private $moyenneSemCours;
    private $moyenneSemEval;
    private $moyenneChampSem;

    
    public function __construct()
    {
        $this->coursGroupes = new ArrayCollection();
        $this->evaluationGroups = new ArrayCollection();
        $this->moyennePeriodeCours = new ArrayCollection();
        $this->moyennePeriodeEval = new ArrayCollection();
        $this->moyenneSemCours = new ArrayCollection();
        $this->moyenneSemEval = new ArrayCollection();
        $this->moyenneChampSem = new ArrayCollection();

    }

    
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getDateNaissance(): ?\DateTimeInterface
    {
        return $this->dateNaissance;
    }

    public function setDateNaissance(\DateTimeInterface $dateNaissance): self
    {
        $this->dateNaissance = $dateNaissance;

        return $this;
    }

    public function getClasse(): ?Classe
    {
        return $this->classe;
    }

    public function setClasse(?Classe $classe): self
    {
        $this->classe = $classe;

        return $this;
    }

    /**
     * @return Collection|CoursGroupe[]
     */
    public function getCoursGroupes(): Collection
    {
        return $this->coursGroupes;
    }

    public function addCoursGroupe(CoursGroupe $coursGroupe): self
    {
        if (!$this->coursGroupes->contains($coursGroupe)) {
            $this->coursGroupes[] = $coursGroupe;
            $coursGroupe->setEleveId($this);
        }

        return $this;
    }

    public function removeCoursGroupe(CoursGroupe $coursGroupe): self
    {
        if ($this->coursGroupes->contains($coursGroupe)) {
            $this->coursGroupes->removeElement($coursGroupe);
            // set the owning side to null (unless already changed)
            if ($coursGroupe->getEleveId() === $this) {
                $coursGroupe->setEleveId(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|EvaluationGroup[]
     */
    public function getEvaluationGroups(): Collection
    {
        return $this->evaluationGroups;
    }

    public function addEvaluationGroup(EvaluationGroup $evaluationGroup): self
    {
        if (!$this->evaluationGroups->contains($evaluationGroup)) {
            $this->evaluationGroups[] = $evaluationGroup;
            $evaluationGroup->setEleve($this);
        }

        return $this;
    }

    public function removeEvaluationGroup(EvaluationGroup $evaluationGroup): self
    {
        if ($this->evaluationGroups->contains($evaluationGroup)) {
            $this->evaluationGroups->removeElement($evaluationGroup);
            // set the owning side to null (unless already changed)
            if ($evaluationGroup->getEleve() === $this) {
                $evaluationGroup->setEleve(null);
            }
        }

        return $this;
    }

    public function getMoyennePeriodeCours() : Array {
        return $this->moyennePeriodeCours;
    }
    public function addMoyennePeriodeCours(array $resultats){
        $this->moyennePeriodeCours[] = $resultats;
    }

    public function getMoyennePeriodeEval(): Array {
        return $this->moyennePeriodeEval;
    }
    public function addMoyennePeriodeEval(array $resultats){
        $this->moyennePeriodeEval[] = $resultats;
    }

    public function getMoyenneSemCours() : Array{
        return $this->moyenneSemCours;
    }

    public function addMoyenneSemCours(array $resultats){
        $this->moyenneSemCours[] = $resultats;
    }

    public function getMoyenneSemEval(): Array {
        return $this->moyenneSemEval;
    }

    public function addMoyenneSemEval(array $resultats) {
        $this->moyenneSemEval[] = $resultats;
    }

    public function getMoyenneChampSem(): Array {
        return $this->moyenneChampSem;
    }

    public function addMoyenneChampSem(array $resultats){
        $this->moyenneChampSem[] = $resultats;
    }

   
}
