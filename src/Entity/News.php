<?php

namespace App\Entity;

use App\Entity\User;
use DateTimeImmutable;
use App\Entity\Comment;
use Cocur\Slugify\Slugify;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\NewsRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: NewsRepository::class)]
#[ORM\HasLifecycleCallbacks]
class News
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\Length(min: 5, max: 255, minMessage:"Le titre doit faire plus de 5 caractères", maxMessage: "Le titre ne doit pas faire plus de 255 caractères")]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    #[Assert\Length(min: 5, max: 255, minMessage:"Le sous-titre doit faire plus de 5 caractères", maxMessage: "Le sous-titre ne doit pas faire plus de 255 caractères")]
    private ?string $subtitle = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\Length(min: 50, minMessage:"Votre description doit faire plus de 50 caractères")]
    private ?string $text = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(length: 255)]
    private ?string $cover = null;

    #[ORM\ManyToOne(inversedBy: 'news')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $author = null;

    #[ORM\Column(length: 255)]
    private ?string $status = null;

    /**
     * @var Collection<int, Team>
     */
    #[ORM\ManyToMany(targetEntity: Team::class, inversedBy: 'news')]
    private Collection $team;

    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    /**
     * @var Collection<int, Comment>
     */
    #[ORM\OneToMany(targetEntity: Comment::class, mappedBy: 'news', orphanRemoval: true)]
    private Collection $comments;

    public function __construct()
    {
        $this->team = new ArrayCollection();
        $this->comments = new ArrayCollection();
    }
        /**
     * Permet de mettre ne place la date de création automatiquement
     *
     * @return void
     */
    #[ORM\PrePersist]
    public function prePersist(): void
    {
        if(empty($this->createdAt))
        {
            $this->createdAt = new \DateTimeImmutable();
        }
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
            $this->slug = $slugify->slugify($this->title);
        }
    }

     /**
     * Permet de récup la note d'une annonce
     *
     * @return integer
     */
    public function getAvgRatings(): int
    {
        // calculer la somme des notations
        // la fonction array_reduce permet de réduire le tableau à une seule valeur (attention il faut un tableau pas une array Collection)1er param c'est le tableau à reduire et en 2ème paramètre de la fonction c'est la fonction à faire pour chaque valeur, 3ème c'est la valeur par défaut
        $sum = array_reduce($this->comments->toArray(),function($total,$comment){
            return $total + $comment->getRating();
        },0);

        // faire la division pour avoir la moyenne (ternaire)
        if(count($this->comments) > 0) return $moyenne = round($sum / count($this->comments));

        return 0;
    }

    /**
     * Permet de récupérer le commentaire d'un auteur par rapport à une annonce
     *
     * @param User $author
     * @return Comment|null
     */
    public function getCommentFromAuthor(User $author): ?Comment
    {
        foreach($this->comments as $comment)
        {
            if($comment->getAuthor() === $author) return $comment;
        }

        return null;
    }
    

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getSubtitle(): ?string
    {
        return $this->subtitle;
    }

    public function setSubtitle(string $subtitle): static
    {
        $this->subtitle = $subtitle;

        return $this;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): static
    {
        $this->text = $text;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getCover(): ?string
    {
        return $this->cover;
    }

    public function setCover(string $cover): static
    {
        $this->cover = $cover;

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): static
    {
        $this->author = $author;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return Collection<int, Team>
     */
    public function getTeam(): Collection
    {
        return $this->team;
    }

    public function addTeam(Team $team): static
    {
        if (!$this->team->contains($team)) {
            $this->team->add($team);
        }

        return $this;
    }

    public function removeTeam(Team $team): static
    {
        $this->team->removeElement($team);

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

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): static
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setNews($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getNews() === $this) {
                $comment->setNews(null);
            }
        }

        return $this;
    }
}
