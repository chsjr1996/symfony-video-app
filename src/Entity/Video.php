<?php

namespace App\Entity;

use App\Repository\VideoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: VideoRepository::class)]
#[ORM\Table(name: 'videos')]
#[ORM\Index(name: 'title_idx', columns: ['title'])]
class Video
{
    private const VIDEO_FOR_NOT_LOGGED_IN = 113716040;
    private const VIDEO_PATH = 'https://player.vimeo.com/video/';
    public const PER_PAGE = 5;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $title;

    #[ORM\Column(type: 'string', length: 255)]
    private $path;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $duration;

    #[ORM\ManyToOne(targetEntity: Category::class, inversedBy: 'videos')]
    private $category;

    #[ORM\OneToMany(mappedBy: 'video', targetEntity: Comment::class)]
    private $comments;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'likedVideos')]
    #[ORM\JoinTable(name: 'likes')]
    private $usersThatLike;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'dislikedVideos')]
    #[ORM\JoinTable(name: 'dislikes')]
    private $usersThatDontLike;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->usersThatLike = new ArrayCollection();
        $this->usersThatDontLike = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function getVimeoId(?UserInterface $user): string
    {
        if ($user) {
            return $this->path;
        }

        return self::VIDEO_PATH . self::VIDEO_FOR_NOT_LOGGED_IN;
    }

    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(?int $duration): self
    {
        $this->duration = $duration;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setVideo($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getVideo() === $this) {
                $comment->setVideo(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsersThatLike(): Collection
    {
        return $this->usersThatLike;
    }

    public function addUsersThatLike(User $usersThatLike): self
    {
        if (!$this->usersThatLike->contains($usersThatLike)) {
            $this->usersThatLike[] = $usersThatLike;
        }

        return $this;
    }

    public function removeUsersThatLike(User $usersThatLike): self
    {
        $this->usersThatLike->removeElement($usersThatLike);

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsersThatDontLike(): Collection
    {
        return $this->usersThatDontLike;
    }

    public function addUsersThatDontLike(User $usersThatDontLike): self
    {
        if (!$this->usersThatDontLike->contains($usersThatDontLike)) {
            $this->usersThatDontLike[] = $usersThatDontLike;
        }

        return $this;
    }

    public function removeUsersThatDontLike(User $usersThatDontLike): self
    {
        $this->usersThatDontLike->removeElement($usersThatDontLike);

        return $this;
    }
}
