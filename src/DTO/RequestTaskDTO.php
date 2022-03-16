<?php

namespace App\DTO;


/**
 * class:  ShowTaskDTO
 * -----------------------------------------------------
 * @author  Saqot (Mihail Shirnin) <saqott@gmail.com>
 * @package  App\DTO
 * -----------------------------------------------------
 * 16.03.2022
 */
class RequestTaskDTO
{
	private ?string $id;
	private ?string $sprintId;
	private ?string $estimation;
	private ?string $title;
	private ?string $description;
	private bool $isNewEntry;


	public function __construct(array $data)
	{
		$id = isset($data['id']) ? trim($data['id']) : null;
		$this->id = $id ?? null;

		$sprintId = isset($data['sprintId']) ? trim($data['sprintId']) : null;
		$this->sprintId = $sprintId ?? null;

		$estimation = isset($data['estimation']) ? trim($data['estimation']) : null;
		$this->estimation = $estimation ?? null;

		$title = isset($data['title']) ? trim($data['title']) : null;
		$this->title = $title ?? null;

		$description = isset($data['description']) ? trim($data['description']) : null;
		$this->description = $description ?? null;

		$this->isNewEntry = $id === null;
	}

	/**
	 * @return string|null
	 */
	public function getId(): ?string
	{
		return $this->id;
	}

	/**
	 * @param string|null $id
	 * @return RequestTaskDTO
	 */
	public function setId(?string $id): RequestTaskDTO
	{
		$this->id = $id;
		return $this;
	}

	/**
	 * @return string|null
	 */
	public function getSprintId(): ?string
	{
		return $this->sprintId;
	}

	/**
	 * @param string|null $sprintId
	 * @return RequestTaskDTO
	 */
	public function setSprintId(?string $sprintId): RequestTaskDTO
	{
		$this->sprintId = $sprintId;
		return $this;
	}

	/**
	 * @return int|string|null
	 */
	public function getEstimation()
	{
		return $this->estimation;
	}

	/**
	 * @param int|string|null $estimation
	 * @return RequestTaskDTO
	 */
	public function setEstimation($estimation)
	{
		$this->estimation = $estimation;
		return $this;
	}

	/**
	 * @return string|null
	 */
	public function getTitle(): ?string
	{
		return $this->title;
	}

	/**
	 * @param string|null $title
	 * @return RequestTaskDTO
	 */
	public function setTitle(?string $title): RequestTaskDTO
	{
		$this->title = $title;
		return $this;
	}

	/**
	 * @return string|null
	 */
	public function getDescription(): ?string
	{
		return $this->description;
	}

	/**
	 * @param string|null $description
	 * @return RequestTaskDTO
	 */
	public function setDescription(?string $description): RequestTaskDTO
	{
		$this->description = $description;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isNewEntry(): bool
	{
		return $this->isNewEntry;
	}

	/**
	 * @param bool $isNewEntry
	 * @return RequestTaskDTO
	 */
	public function setIsNewEntry(bool $isNewEntry): RequestTaskDTO
	{
		$this->isNewEntry = $isNewEntry;
		return $this;
	}


}