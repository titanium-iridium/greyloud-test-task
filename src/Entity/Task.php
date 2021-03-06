<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TaskRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Task
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    private $title;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created;

    /**
     * @ORM\Column(type="datetime")
     */
    private $updated;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User", inversedBy="tasks")
     */
    private $executors;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="task", orphanRemoval=true)
     */
    private $comments;

    public function __construct()
    {
        $this->executors = new ArrayCollection();
        $this->comments = new ArrayCollection();
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function updateTimestamps()
    {
        $this->setUpdated(new \DateTime('now'));

        if ($this->getCreated() === null) {
            $this->setCreated(new \DateTime('now'));
        }
    }

    public function getId()
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

    public function getCreated(): ?\DateTimeInterface
    {
        return $this->created;
    }

    public function setCreated(\DateTimeInterface $created): self
    {
        $this->created = $created;

        return $this;
    }

    public function getUpdated(): ?\DateTimeInterface
    {
        return $this->updated;
    }

    public function setUpdated(\DateTimeInterface $updated): self
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getExecutors(): Collection
    {
        return $this->executors;
    }

    public function addExecutor(User $executor): self
    {
        if (!$this->executors->contains($executor)) {
            $this->executors[] = $executor;
        }

        return $this;
    }

    public function removeExecutor(User $executor): self
    {
        if ($this->executors->contains($executor)) {
            $this->executors->removeElement($executor);
        }

        return $this;
    }

    /**
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }
    
    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setTask($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getTask() === $this) {
                $comment->setTask(null);
            }
        }

        return $this;
    }
    
    /**
     * @Assert\Callback
     * 
     * Note: Actually this validator will never generate a violation 
     * as the duplicates will be automatically droped from executors collection in Form::submit
     */
    public function validate(ExecutionContextInterface $context, $payload)
    {
        // check if no duplicates among executors
        $assignedExecs = [];
        foreach($this->getExecutors() as $exec) {
            $id = $exec->getId();
            /** @var User $exec */
            if (isset($assignedExecs[$id])) {
                $context->buildViolation('No duplicates allowed in executors list!')
                    ->atPath('executors')
                    ->addViolation();
                break;
            }
            $assignedExecs[$id] = true;
        }
    }

}
