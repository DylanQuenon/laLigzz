<?php

namespace App\Entity;

use Cocur\Slugify\Slugify;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\TeamRepository;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TeamRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[UniqueEntity(fields:['name'], message:"Une autre équipe possède déjà ce nom, merci de le modifier")]

class Team
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\Length(min: 10, max: 255, minMessage:"Le nom doit faire plus de 10 caractères", maxMessage: "Le nom ne doit pas faire plus de 255 caractères")]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    #[ORM\Column(length: 255,nullable: true)]
    #[Assert\Image(mimeTypes:['image/png','image/jpeg', 'image/jpg', 'image/gif', 'image/webp'], mimeTypesMessage:"Vous devez upload un fichier jpg, jpeg, webp, png ou gif")]
    #[Assert\File(maxSize:"1024k", maxSizeMessage: "La taille du fichier est trop grande")]
    private ?string $logo = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\Length(min: 10,max:500, minMessage:'Votre description doit faire plus de 10 caractères',maxMessage:"Votre description ne doit pas faire plus de 500 caractères")]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    #[Assert\Length(min: 2, max: 255, minMessage:"La devise doit faire plus de 2 caractères", maxMessage: "La devise ne doit pas faire plus de 255 caractères")]
    private ?string $devise = null;

    #[ORM\Column]
    private ?\DateTime $fondation = null;

    #[ORM\Column(length: 255)]
    #[Assert\Length(min: 2, max: 255, minMessage:"Le coach doit faire plus de 2 caractères", maxMessage: "Le coach ne doit pas faire plus de 255 caractères")]
    private ?string $coach = null;

    #[ORM\Column(length: 255)]
    #[Assert\Length(min: 2, max: 255, minMessage:"Le meilleur buteur doit faire plus de 2 caractères", maxMessage: "Le meilleur buteur ne doit pas faire plus de 255 caractères")]
    private ?string $goalscorer = null;

    #[ORM\Column(length: 255)]
    #[Assert\Length(min: 2, max: 255, minMessage:"Le président ne doit faire plus de 2 caractères", maxMessage: "Le président ne doit pas faire plus de 255 caractères")]
    private ?string $president = null;

   

    /**
     * @var Collection<int, News>
     */
    #[ORM\ManyToMany(targetEntity: News::class, mappedBy: 'team')]
    private Collection $news;

    /**
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'FollowedTeams')]
    private Collection $users;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Image(mimeTypes:['image/png','image/jpeg', 'image/jpg', 'image/gif', 'image/webp'], mimeTypesMessage:"Vous devez upload un fichier jpg, jpeg, webp, png ou gif")]
    #[Assert\File(maxSize:"1024k", maxSizeMessage: "La taille du fichier est trop grande")]
    private ?string $logoBackground = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Image(mimeTypes:['image/png','image/jpeg', 'image/jpg', 'image/gif', 'image/webp'], mimeTypesMessage:"Vous devez upload un fichier jpg, jpeg, webp, png ou gif")]
    #[Assert\File(maxSize:"1024k", maxSizeMessage: "La taille du fichier est trop grande")]
    private ?string $cover = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Image(mimeTypes:['image/png','image/jpeg', 'image/jpg', 'image/gif', 'image/webp'], mimeTypesMessage:"Vous devez upload un fichier jpg, jpeg, webp, png ou gif")]
    #[Assert\File(maxSize:"1024k", maxSizeMessage: "La taille du fichier est trop grande")]
    private ?string $newsPicture = null;

    /**
     * @var Collection<int, Matches>
     */
    #[ORM\OneToMany(targetEntity: Matches::class, mappedBy: 'homeTeam', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $matches;

      /**
     * @var Collection<int, Matches>
     */
    #[ORM\OneToMany(targetEntity: Matches::class, mappedBy: 'awayTeam', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $awayMatches;


    #[ORM\OneToOne(mappedBy: 'team', cascade: ['persist', 'remove'])]
    private ?Ranking $ranking = null;

    #[ORM\Column(length: 255)]
    private ?string $stadium = null;

    public function __construct()
    {
        $this->images = new ArrayCollection();
        $this->news = new ArrayCollection();
        $this->users = new ArrayCollection();
        $this->matches = new ArrayCollection();
        $this->awayMatches = new ArrayCollection();
    }

     /**
     * Permet d'intialiser le slug automatiquement si on ne le donne pas
     *
     * @return void
     */
    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function initializeSlug(): void
    {
        if(empty($this->slug))
        {
            $slugify = new Slugify();
            $this->slug = $slugify->slugify($this->name);
        }
    }
    

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    public function getLogo(): ?string
    {
        return $this->logo;
    }

    public function setLogo(string $logo): static
    {
        $this->logo = $logo;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getDevise(): ?string
    {
        return $this->devise;
    }

    public function setDevise(string $devise): static
    {
        $this->devise = $devise;

        return $this;
    }

    public function getFondation(): ?\DateTime
    {
        return $this->fondation;
    }

    public function setFondation(\DateTime $fondation): static
    {
        $this->fondation = $fondation;

        return $this;
    }

    public function getCoach(): ?string
    {
        return $this->coach;
    }

    public function setCoach(string $coach): static
    {
        $this->coach = $coach;

        return $this;
    }

    public function getGoalscorer(): ?string
    {
        return $this->goalscorer;
    }

    public function setGoalscorer(string $goalscorer): static
    {
        $this->goalscorer = $goalscorer;

        return $this;
    }

    public function getPresident(): ?string
    {
        return $this->president;
    }

    public function setPresident(string $president): static
    {
        $this->president = $president;

        return $this;
    }

   

    /**
     * @return Collection<int, News>
     */
    public function getNews(): Collection
    {
        return $this->news;
    }

    public function addNews(News $news): static
    {
        if (!$this->news->contains($news)) {
            $this->news->add($news);
            $news->addTeam($this);
        }

        return $this;
    }

    public function removeNews(News $news): static
    {
        if ($this->news->removeElement($news)) {
            $news->removeTeam($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): static
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->addFollowedTeam($this);
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        if ($this->users->removeElement($user)) {
            $user->removeFollowedTeam($this);
        }

        return $this;
    }

    public function getLogoBackground(): ?string
    {
        return $this->logoBackground;
    }

    public function setLogoBackground(?string $logoBackground): static
    {
        $this->logoBackground = $logoBackground;

        return $this;
    }

    public function getCover(): ?string
    {
        return $this->cover;
    }

    public function setCover(?string $cover): static
    {
        $this->cover = $cover;

        return $this;
    }

    public function getNewsPicture(): ?string
    {
        return $this->newsPicture;
    }

    public function setNewsPicture(?string $newsPicture): static
    {
        $this->newsPicture = $newsPicture;

        return $this;
    }

    /**
     * @return Collection<int, Matches>
     */
    public function getMatches(): Collection
    {
        return $this->matches;
    }

    public function addMatch(Matches $match): static
    {
        if (!$this->matches->contains($match)) {
            $this->matches->add($match);
            $match->setHomeTeam($this);
        }

        return $this;
    }

    public function removeMatch(Matches $match): static
    {
        if ($this->matches->removeElement($match)) {
            // set the owning side to null (unless already changed)
            if ($match->getHomeTeam() === $this) {
                $match->setHomeTeam(null);
            }
        }

        return $this;
    }
    
    /**
     * @return Collection<int, Matches>
     */
    public function getAwayMatches(): Collection
    {
        return $this->awayMatches;
    }

    public function addAwayMatch(Matches $match): static
    {
        if (!$this->awayMatches->contains($match)) {
            $this->awayMatches->add($match);
            $match->setAwayTeam($this);
        }

        return $this;
    }

    public function removeAwayMatch(Matches $match): static
    {
        if ($this->awayMatches->removeElement($match)) {
            // set the owning side to null (unless already changed)
            if ($match->getAwayTeam() === $this) {
                $match->setAwayTeam(null);
            }
        }

        return $this;
    }

    public function getRanking(): ?Ranking
    {
        return $this->ranking;
    }

    public function setRanking(?Ranking $ranking): static
    {
        // unset the owning side of the relation if necessary
        if ($ranking === null && $this->ranking !== null) {
            $this->ranking->setTeam(null);
        }

        // set the owning side of the relation if necessary
        if ($ranking !== null && $ranking->getTeam() !== $this) {
            $ranking->setTeam($this);
        }

        $this->ranking = $ranking;

        return $this;
    }

    public function getStadium(): ?string
    {
        return $this->stadium;
    }

    public function setStadium(string $stadium): static
    {
        $this->stadium = $stadium;

        return $this;
    }
}
