<?php

namespace App\Repository;

use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityRepository;

/**
 * class:  TasksRepository
 * -----------------------------------------------------
 * @author  Saqot (Mihail Shirnin) <saqott@gmail.com>
 * @package  App\Repository
 * -----------------------------------------------------
 */
class TaskRepository extends EntityRepository
{

	/**
	 * @param string $sprintId
	 * @return int
	 * @throws Exception
	 */
	public function getCountActiveForSprint(string $sprintId)
	{
		$conn = $this->_em->getConnection();
		$sprintId = $conn->quote($sprintId);
		$rows = $conn->fetchFirstColumn("SELECT COUNT(id) FROM `tasks` WHERE sprint_id = {$sprintId} AND is_active = '1'");

		return intval($rows[0]);
	}

	/**
	 * @param string $id
	 * @return bool
	 * @throws Exception
	 */
	public function isUnique(string $id)
	{
		$conn = $this->_em->getConnection();
		$id = $conn->quote($id);
		$rows = $conn->fetchFirstColumn("SELECT COUNT(id) FROM `tasks` WHERE id = {$id}");

		return !boolval($rows[0]);
	}
}