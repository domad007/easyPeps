<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use EWZ\Bundle\RecaptchaBundle\Validator\Constraints as Recaptcha;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(
 *  fields={"mail"},
 *  message="L'adresse mail existe déjà !"
 * )
 * 
 * @UniqueEntity(
 * fields={"nomUser"},
 * message="Le nom d'utilisateur existe déjà !"
 * )
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Veuillez renseigner votre nom")
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Veuillez renseigner votre prénom")
     */
    private $prenom;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Veuillez renseigner votre nom d'utilisateur")
     */
    private $nomUser;


    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Email(message="Veuillez renseigner votre email")
     */
    private $mail;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Length(min=8, minMessage="Votre mot de passe doit comporter minimum 8 caratères")
     */
    private $mdp;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $sexe;

    /**
     * @ORM\Column(type="date")
     */
    private $dateNaiss;

    /**
     * @Assert\EqualTo(propertyPath="mdp", message="Votre mot de passe ne corresponds pas")
     */
    public $confMdp;

    /**
     * @Assert\EqualTo("true", message="Vous devez accepter les droits de condition générales d'utilisation")
     */
    public $acceptCGU;

    /**
     * @Recaptcha\IsTrue(message="Veuillez valider que vous n'êtes pas un robot")
     */
    public $recaptcha;
    
    /**
     * @var string le token qui servira lors de l'oubli de mot de passe
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $resetToken;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Classe", mappedBy="professeur")
     */
    private $classes;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\CustomizedPresences", mappedBy="user")
     */
    private $customizedPresences;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Role", mappedBy="users")
     */
    private $userRoles;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Groups", mappedBy="professeur")
     */
    private $groups;

    /**
     * @ORM\Column(type="boolean")
     */
    private $userActif;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Appreciation", mappedBy="professeur")
     */
    private $appreciations;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Parametres", mappedBy="professeur")
     */
    private $parametres;

    public function __construct()
    {
        $this->classes = new ArrayCollection();
        $this->customizedPresences = new ArrayCollection();
        $this->userRoles = new ArrayCollection();
        $this->groups = new ArrayCollection();
        $this->appreciations = new ArrayCollection();
        $this->parametres = new ArrayCollection();
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

    public function getNomUser(): ?string
    {
        return $this->nomUser;
    }

    public function setNomUser(string $nomUser): self
    {
        $this->nomUser = $nomUser;

        return $this;
    }


    public function getMail(): ?string
    {
        return $this->mail;
    }

    public function setMail(string $mail): self
    {
        $this->mail = $mail;

        return $this;
    }

    public function getMdp(): ?string
    {
        return $this->mdp;
    }

    public function setMdp(string $mdp): self
    {
        $this->mdp = $mdp;

        return $this;
    }

    public function getSexe(): ?string
    {
        return $this->sexe;
    }

    public function setSexe(string $sexe): self
    {
        $this->sexe = $sexe;

        return $this;
    } 
      
    public function getDateNaiss(): ?\DateTimeInterface
    {
        return $this->dateNaiss;
    }

    public function setDateNaiss(\DateTimeInterface $dateNaiss): self
    {
        $this->dateNaiss = $dateNaiss;

        return $this;
    }

      /**
     * @return string
     */
    public function getResetToken(): string
    {
        return $this->resetToken;
    }

    /**
     * @param string $resetToken
     */
    public function setResetToken(?string $resetToken): void
    {
        $this->resetToken = $resetToken;
    }

    public function getRoles(){

        $roles = $this->userRoles->map(function($role){
            return $role->getTitle();
        })->toArray();

        $roles[] = 'ROLE_USER';
        return $roles;
    }
    
    public function getPassword(){
        return $this->mdp;
    }

    public function getSalt(){

    }

    public function getUsername(){
        return $this->nomUser;
    }

    public function eraseCredentials(){
        
    }

    /**
     * @return Collection|Classe[]
     */
    public function getClasses(): Collection
    {
        return $this->classes;
    }

    public function addClass(Classe $class): self
    {
        if (!$this->classes->contains($class)) {
            $this->classes[] = $class;
            $class->setProfesseur($this);
        }

        return $this;
    }

    public function removeClass(Classe $class): self
    {
        if ($this->classes->contains($class)) {
            $this->classes->removeElement($class);
            // set the owning side to null (unless already changed)
            if ($class->getProfesseur() === $this) {
                $class->setProfesseur(null);
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
            $customizedPresence->setUser($this);
        }

        return $this;
    }

    public function removeCustomizedPresence(CustomizedPresences $customizedPresence): self
    {
        if ($this->customizedPresences->contains($customizedPresence)) {
            $this->customizedPresences->removeElement($customizedPresence);
            // set the owning side to null (unless already changed)
            if ($customizedPresence->getUser() === $this) {
                $customizedPresence->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Role[]
     */
    public function getUserRoles(): Collection
    {
        return $this->userRoles;
    }

    public function addUserRole(Role $userRole): self
    {
        if (!$this->userRoles->contains($userRole)) {
            $this->userRoles[] = $userRole;
            $userRole->addUser($this);
        }

        return $this;
    }

    public function removeUserRole(Role $userRole): self
    {
        if ($this->userRoles->contains($userRole)) {
            $this->userRoles->removeElement($userRole);
            $userRole->removeUser($this);
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
            $group->setProfesseur($this);
        }

        return $this;
    }

    public function removeGroup(Groups $group): self
    {
        if ($this->groups->contains($group)) {
            $this->groups->removeElement($group);
            // set the owning side to null (unless already changed)
            if ($group->getProfesseur() === $this) {
                $group->setProfesseur(null);
            }
        }

        return $this;
    }

    public function getUserActif(): ?bool
    {
        return $this->userActif;
    }

    public function setUserActif(bool $userActif): self
    {
        $this->userActif = $userActif;

        return $this;
    }

    /**
     * @return Collection|Appreciation[]
     */
    public function getAppreciations(): Collection
    {
        return $this->appreciations;
    }

    public function addAppreciation(Appreciation $appreciation): self
    {
        if (!$this->appreciations->contains($appreciation)) {
            $this->appreciations[] = $appreciation;
            $appreciation->setProfesseur($this);
        }

        return $this;
    }

    public function removeAppreciation(Appreciation $appreciation): self
    {
        if ($this->appreciations->contains($appreciation)) {
            $this->appreciations->removeElement($appreciation);
            // set the owning side to null (unless already changed)
            if ($appreciation->getProfesseur() === $this) {
                $appreciation->setProfesseur(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Parametres[]
     */
    public function getParametres(): Collection
    {
        return $this->parametres;
    }

    public function addParametre(Parametres $parametre): self
    {
        if (!$this->parametres->contains($parametre)) {
            $this->parametres[] = $parametre;
            $parametre->setProfesseur($this);
        }

        return $this;
    }

    public function removeParametre(Parametres $parametre): self
    {
        if ($this->parametres->contains($parametre)) {
            $this->parametres->removeElement($parametre);
            // set the owning side to null (unless already changed)
            if ($parametre->getProfesseur() === $this) {
                $parametre->setProfesseur(null);
            }
        }

        return $this;
    }


}
