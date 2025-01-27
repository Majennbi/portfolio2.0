<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\ProjectsRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ProjectsRepository::class)]
class Projects
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["getProjects"])]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Groups(["getProjects"])]
    private ?string $Title = null;

    #[ORM\Column(length: 1000, nullable: true)]
    #[Groups(["getProjects"])]
    private ?string $Description = null;

    #[ORM\Column(length: 255)]
    #[Groups(["getProjects"])]
    private ?string $Image = null;

    #[ORM\Column(length: 255)]
    #[Groups(["getProjects"])]
    private ?string $Link = null;

    #[ORM\Column(length: 255)]
    #[Groups(["getProjects"])]
    private ?string $Duration = null;

    #[ORM\Column]
    #[Groups(["getProjects"])]
    private ?\DateTimeImmutable $CreatedAt = null;

    #[ORM\Column]
    #[Groups(["getProjects"])]
    private ?\DateTimeImmutable $UpdatedAt = null;

    #[ORM\ManyToMany(targetEntity: Skills::class, inversedBy: 'projects')]
    #[Groups(["getProjects"])]
    private $skills;

    public function __construct()
    {
        $this->skills = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->Title;
    }

    public function setTitle(string $Title): static
    {
        $this->Title = $Title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->Description;
    }

    public function setDescription(?string $Description): static
    {
        $this->Description = $Description;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->Image;
    }

    public function setImage(string $Image): static
    {
        $this->Image = $Image;

        return $this;
    }

    public function getLink(): ?string
    {
        return $this->Link;
    }

    public function setLink(string $Link): static
    {
        $this->Link = $Link;

        return $this;
    }

    public function getDuration(): ?string
    {
        return $this->Duration;
    }

    public function setDuration(string $Duration): static
    {
        $this->Duration = $Duration;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->CreatedAt;
    }

    public function setCreatedAt(\DateTimeImmutable $CreatedAt): static
    {
        $this->CreatedAt = $CreatedAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->UpdatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $UpdatedAt): static
    {
        $this->UpdatedAt = $UpdatedAt;

        return $this;
    }

    /**
     * @return Collection<int, Skills>
     */
    public function getSkills(): Collection
    {
        return $this->skills;
    }

    public function addSkill(Skills $skill): static
    {
        if (!$this->skills->contains($skill)) {
            $this->skills->add($skill);
        }

        return $this;
    }

    public function removeSkill(Skills $skill): static
    {
        $this->skills->removeElement($skill);

        return $this;
    }
}