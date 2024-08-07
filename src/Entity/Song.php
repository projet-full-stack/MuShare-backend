<?php

namespace App\Entity;

use App\Repository\SongRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: SongRepository::class)]
class Song
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["song", "user", "like", "playlist"])]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Groups(["song", "user", "like", "playlist"],)]
    private ?string $title = null;

    #[ORM\Column(length: 25)]
    #[Groups(["song"])]
    private ?string $status = null;

    #[ORM\Column(type: Types::DATETIMETZ_MUTABLE)]
    #[Groups(["song"])]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: Types::DATETIMETZ_MUTABLE)]
    #[Groups(["song"])]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\Column(length: 50)]
    #[Groups(["song"])]
    
    private ?string $author = null;

    /**
     * @var Collection<int, Like>
     */
    #[ORM\OneToMany(targetEntity: Like::class, mappedBy: 'song')]
    #[Groups(["song"])]
    private Collection $likes;

    #[ORM\OneToOne(mappedBy: 'song', cascade: ['persist', 'remove'])]
    #[Groups(["song"])]
    private ?DownloadedFile $downloadedFile = null;

    #[ORM\ManyToOne(inversedBy: 'songs')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(["song"])]
    private ?User $owner = null;

    public function __construct()
    {
        $this->likes = new ArrayCollection();
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

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getAuthor(): ?string
    {
        return $this->author;
    }

    public function setAuthor(string $author): static
    {
        $this->author = $author;

        return $this;
    }

    /**
     * @return Collection<int, Like>
     */
    public function getLikes(): Collection
    {
        return $this->likes;
    }

    public function addLike(Like $like): static
    {
        if (!$this->likes->contains($like)) {
            $this->likes->add($like);
            $like->setSong($this);
        }

        return $this;
    }

    public function removeLike(Like $like): static
    {
        if ($this->likes->removeElement($like)) {
            // set the owning side to null (unless already changed)
            if ($like->getSong() === $this) {
                $like->setSong(null);
            }
        }

        return $this;
    }

    public function getDownloadedFile(): ?DownloadedFile
    {
        return $this->downloadedFile;
    }

    public function setDownloadedFile(DownloadedFile $downloadedFile): static
    {
        // set the owning side of the relation if necessary
        if ($downloadedFile->getSong() !== $this) {
            $downloadedFile->setSong($this);
        }

        $this->downloadedFile = $downloadedFile;

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): static
    {
        $this->owner = $owner;

        return $this;
    }
}
