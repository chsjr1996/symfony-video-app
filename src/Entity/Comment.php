<?php

namespace App\Entity;

use App\Repository\CommentRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommentRepository::class)]
#[ORM\Table(name: 'comments')]
#[ORM\HasLifecycleCallbacks()]
class Comment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'text')]
    private $content;

    #[ORM\Column(type: 'datetime_immutable')]
    private $created_at;

    // "Fake column"
    private $created_at2;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false, name: 'owner_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private $owner;

    #[ORM\ManyToOne(targetEntity: Video::class, inversedBy: 'comments')]
    #[ORM\JoinColumn(nullable: false, name: 'video_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private $video;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    #[ORM\PrePersist()]
    public function setCreatedAt(): self
    {
        if (isset($this->created_at2)) {
            $this->created_at = $this->created_at2;
        } else {
            $this->created_at = new \DateTimeImmutable();
        }

        return $this;
    }

    public function setCreatedAtForFixtures(\DateTimeImmutable $created_at): self
    {
        $this->created_at2 = $created_at;

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    public function getVideo(): ?Video
    {
        return $this->video;
    }

    public function setVideo(?Video $video): self
    {
        $this->video = $video;

        return $this;
    }
}
