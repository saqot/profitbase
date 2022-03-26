<?php

namespace App\Entity;

use App\Repository\TaskRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * class:  TaskEntity
 * -----------------------------------------------------
 * @author  Saqot (Mihail Shirnin) <saqott@gmail.com>
 * @package  App\Entity
 * -----------------------------------------------------
 * @orm\Table( name="tasks" )
 * @ORM\Entity(repositoryClass=TaskRepository::class)
 * -----------------------------------------------------
 */
class TaskEntity
{
	/**
	 * @var string
	 * @ORM\Id
	 * @ORM\Column(name="id", type="string", length=50)
	 */
	private string $id;

	/**
	 * @var bool
	 * @ORM\Column(name="is_active", type="boolean", options={"default" = 0})
	 */
	private bool $isActive;

	/**
	 * @var integer
	 * @ORM\Column(name="estimation", type="smallint")
	 */
	private int $estimation;

	/**
	 * @var string
	 * @ORM\Column(name="title", type="string", length=250)
	 */
	private string $title;

	/**
	 * @var string
	 * @ORM\Column(name="description", type="text")
	 */
	private string $description;

	/**
	 * @var DateTime|null
	 * @ORM\Column(name="at", type="date")
	 */
	private DateTime $at;

	/**
	 * @var SprintEntity
	 * @ORM\ManyToOne(targetEntity="App\Entity\SprintEntity", inversedBy="tasks", cascade={"persist", "remove"})
	 * @ORM\JoinColumn(name="sprint_id", referencedColumnName="id", onDelete="CASCADE")
	 */
	private SprintEntity $sprint;

	/**
	 * @return string
	 */
	public function getId(): string
	{
		return $this->id;
	}

	/**
	 * @param string $id
	 * @return TaskEntity
	 */
	public function setId(string $id): TaskEntity
	{
		$this->id = $id;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isActive(): bool
	{
		return $this->isActive;
	}

	/**
	 * @param bool $isActive
	 * @return TaskEntity
	 */
	public function setIsActive(bool $isActive): TaskEntity
	{
		$this->isActive = $isActive;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getEstimation(): int
	{
		return $this->estimation;
	}

	/**
	 * @param int $estimation
	 * @return TaskEntity
	 */
	public function setEstimation(int $estimation): TaskEntity
	{
		$this->estimation = $estimation;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getTitle(): string
	{
		return $this->title;
	}

	/**
	 * @param string $title
	 * @return TaskEntity
	 */
	public function setTitle(string $title): TaskEntity
	{
		$this->title = $title;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getDescription(): string
	{
		return $this->description;
	}

	/**
	 * @param string $description
	 * @return TaskEntity
	 */
	public function setDescription(string $description): TaskEntity
	{
		$this->description = $description;
		return $this;
	}

	/**
	 * @return DateTime|null
	 */
	public function getAt(): ?DateTime
	{
		return $this->at;
	}

	/**
	 * @param DateTime|null $at
	 * @return TaskEntity
	 */
	public function setAt(?DateTime $at): TaskEntity
	{
		$this->at = $at;
		return $this;
	}

	/**
	 * @return SprintEntity
	 */
	public function getSprint(): SprintEntity
	{
		return $this->sprint;
	}

	/**
	 * @param SprintEntity $sprint
	 * @return TaskEntity
	 */
	public function setSprint(SprintEntity $sprint): TaskEntity
	{
		$this->sprint = $sprint;
		return $this;
	}


}