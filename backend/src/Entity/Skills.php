<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\SkillsRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: SkillsRepository::class)]
class Skills
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["getProjects", "getSkills"])]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank(message: 'Name is required')]
    #[Assert\Length(max: 100, maxMessage: 'Name cannot be longer than {{ limit }} characters')]
    #[Groups(["getProjects", "getSkills"])]
    private ?string $Name = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Length(max: 255, maxMessage: 'Icon cannot be longer than {{ limit }} characters')]
    #[Groups(["getProjects", "getSkills"])]
    private ?string $Icon = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank(message: 'Level is required')]
    #[Assert\Length(max: 100, maxMessage: 'Level cannot be longer than {{ limit }} characters')]
    #[Groups(["getProjects", "getSkills"])]
    private ?string $Level = null;

    #[ORM\Column]
    #[Assert\NotNull(message: 'CreatedAt is required')]
    #[Assert\Type(\DateTimeImmutable::class, message: 'CreatedAt must be a valid datetime')]
    #[Groups(["getProjects", "getSkills"])]
    private ?\DateTimeImmutable $CreatedAt = null;

    #[ORM\ManyToMany(targetEntity: Projects::class, mappedBy: 'skills')]
    #[Groups(["getSkills"])]
    private Collection $projects;

    public function __construct()
    {
        $this->projects = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->Name;
    }

    public function setName(string $Name): static
    {
        $this->Name = $Name;

        return $this;
    }

    public function getIcon(): ?string
    {
        return $this->Icon;
    }

    public function setIcon(?string $Icon): static
    {
        $this->Icon = $Icon;

        return $this;
    }

    public function getLevel(): ?string
    {
        return $this->Level;
    }

    public function setLevel(string $Level): static
    {
        $this->Level = $Level;

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

    /**
     * @return Collection<int, Projects>
     */
    public function getProjects(): Collection
    {
        return $this->projects;
    }

    public function setProjects(?Collection $projects): static
    {
        $this->projects = $projects ?? new ArrayCollection();

        return $this;
    }

    public function addProject(Projects $project): static
    {
        if (!$this->projects->contains($project)) {
            $this->projects->add($project);
            $project->addSkill($this);
        }

        return $this;
    }

    public function removeProject(Projects $project): static
    {
        if ($this->projects->removeElement($project)) {
            $project->removeSkill($this);
        }

        return $this;
    }
}