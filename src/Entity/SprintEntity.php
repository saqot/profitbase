<?php

namespace App\Entity;

use App\Repository\SprintRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;

/**
 * class:  SprintEntity
 * -----------------------------------------------------
 * @author  Saqot (Mihail Shirnin) <saqott@gmail.com>
 * @package  App\Entity
 * -----------------------------------------------------
 * @orm\Table( name="sprints" )
 * @ORM\Entity(repositoryClass=SprintRepository::class)
 * -----------------------------------------------------
 */
class SprintEntity
{
	/**
	 * @var string
	 * @ORM\Id
	 * @ORM\Column(name="id", type="string", length=50)
	 */
	private string $id;

	/**
	 * @var integer
	 * @ORM\Column(name="week", type="smallint")
	 */
	private int $week;

	/**
	 * @var integer
	 * @ORM\Column(name="year", type="smallint")
	 */
	private int $year;

	/**
	 * @var bool
	 * @ORM\Column(name="is_active", type="boolean", options={"default" = 0})
	 */
	private bool $isActive = false;

	/**
	 * @var DateTime
	 * @ORM\Column(name="create_at", type="date")
	 */
	private DateTime $createAt;

	/**
	 * @var DateTime
	 * @ORM\Column(name="start_at", type="date")
	 */
	private DateTime $startAt;

	/**
	 * @var PersistentCollection|ArrayCollection|TaskEntity[]
	 * @ORM\OneToMany(targetEntity="App\Entity\TaskEntity", mappedBy="sprint", cascade={"persist", "remove"})
	 */
	private PersistentCollection $tasks;

	/**
	 * @return string
	 */
	public function getId(): string
	{
		return $this->id;
	}

	/**
	 * @param string $id
	 * @return SprintEntity
	 */
	public function setId(string $id): SprintEntity
	{
		$this->id = $id;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getWeek(): int
	{
		return $this->week;
	}

	/**
	 * @param int $week
	 * @return SprintEntity
	 */
	public function setWeek(int $week): SprintEntity
	{
		$this->week = $week;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getYear(): int
	{
		return $this->year;
	}

	/**
	 * @param int $year
	 * @return SprintEntity
	 */
	public function setYear(int $year): SprintEntity
	{
		$this->year = $year;
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
	 * @return SprintEntity
	 */
	public function setIsActive(bool $isActive): SprintEntity
	{
		$this->isActive = $isActive;
		return $this;
	}

	/**
	 * @return DateTime
	 */
	public function getCreateAt(): DateTime
	{
		return $this->createAt;
	}

	/**
	 * @param DateTime $createAt
	 * @return SprintEntity
	 */
	public function setCreateAt(DateTime $createAt): SprintEntity
	{
		$this->createAt = $createAt;
		return $this;
	}

	/**
	 * @return DateTime
	 */
	public function getStartAt(): DateTime
	{
		return $this->startAt;
	}

	/**
	 * @param DateTime $startAt
	 * @return SprintEntity
	 */
	public function setStartAt(DateTime $startAt): SprintEntity
	{
		$this->startAt = $startAt;
		return $this;
	}

	/**
	 * @return TaskEntity[]|ArrayCollection|PersistentCollection
	 */
	public function getTasks()
	{
		return $this->tasks;
	}

	/**
	 * @param TaskEntity[]|ArrayCollection|PersistentCollection $tasks
	 * @return SprintEntity
	 */
	public function setTasks($tasks)
	{
		$this->tasks = $tasks;
		return $this;
	}


}