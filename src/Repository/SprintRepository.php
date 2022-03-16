<?php

namespace App\Repository;

use App\Helper\App;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityRepository;

/**
 * class:  SprintsRepository
 * -----------------------------------------------------
 * @author  Saqot (Mihail Shirnin) <saqott@gmail.com>
 * @package  App\Repository
 * -----------------------------------------------------
 */
class SprintRepository extends EntityRepository
{
	/**
	 * @param string $id
	 * @return bool
	 * @throws Exception
	 */
	public function isUnique(string $id)
	{
		$conn = $this->_em->getConnection();
		$id = $conn->quote($id);
		$rows = $conn->fetchAllAssociative("SELECT id FROM `sprints` WHERE id = {$id}");

		return !boolval(count($rows));
	}

	/**
	 * @return array
	 * @throws Exception
	 */
	public function getFullListForHomePage()
	{
		$conn = $this->_em->getConnection();

		$sql = "SELECT
       			s.id AS `s.id`,
				s.is_active AS `s.isActive`,
				s.week AS `s.week`,
				s.year AS `s.year`,
				s.create_at AS `s.at`,
                t.id AS `t.id`,
				t.is_active AS `t.isActive`,
				t.title AS `t.title`,
				t.description AS `t.description`,
				t.estimation AS `t.estimation`
            FROM
                `sprints` s
            LEFT JOIN (select id, is_active, title, description, estimation, sprint_id, at  from `tasks`  WHERE is_active = '1') t ON s.id = t.sprint_id
            WHERE  s.is_active = '1'
            ORDER BY s.year, s.week, t.at";

		$sql = preg_replace('/[\s\n\r]+/', ' ', $sql);
		$rows = $conn->fetchAllAssociative($sql);

		$itmes = [];
		foreach ($rows as $row) {
			$spId = $row['s.id'];
			if (empty($itmes[$spId])) {
				$itmes[$spId] = [
					'id'       => $spId,
					'year'     => $row['s.year'],
					'week'     => $row['s.week'],
					'at'       => $row['s.at'],
					'isActive' => boolval($row['s.isActive']),
					'tasks'    => [],
				];
			}

			if ($row['t.id']) {
				$itmes[$spId]['tasks'][] = [
					'id'          => $row['t.id'],
					'isActive'    => boolval($row['t.isActive']),
					'title'       => $row['t.title'],
					'description' => $row['t.description'],
					'estimation'  => $row['t.estimation'],
				];
			}


		}
		$itmes = array_values($itmes);

		return $itmes;
	}

	/**
	 * @return array
	 * @throws Exception
	 */
	public function getReadyIds()
	{
		$startAt = date("Y-m-d", mktime(0, 0, 0, 1, ((App::getCurWeek() - 1) * 7)));

		$conn = $this->_em->getConnection();
		$ids = $conn->fetchAllAssociative("SELECT s.id FROM `sprints` s WHERE  s.start_at >= '{$startAt}'");

		if ($ids) {
			$ids = array_column($ids, 'id');
		}
		return $ids;
	}

	public function getEndingsForClose()
	{
		$conn = $this->_em->getConnection();

		$startAt = date("Y-m-d");

		$sql = "SELECT
       			s.id AS `s.id`,
				s.is_active AS `s.isActive`,
				s.start_at AS `s.startAt`,
                t.id AS `t.id`,
				t.is_active AS `t.isActive`
            FROM
                `sprints` s
            LEFT JOIN (select id, is_active, sprint_id  from `tasks`  WHERE is_active = '1') t ON s.id = t.sprint_id
            WHERE  s.is_active = '1' AND s.start_at < '{$startAt}'";

		$sql = preg_replace('/[\s\n\r]+/', ' ', $sql);
		$rows = $conn->fetchAllAssociative($sql);

		$itmes = [];
		foreach ($rows as $row) {
			$spId = $row['s.id'];
			if (empty($itmes[$spId])) {
				$itmes[$spId] = [
					'id'       => $spId,
					'startAt'  => $row['s.startAt'],
					'isActive' => boolval($row['s.isActive']),
					'tasks'    => [],
				];
			}

			if ($row['t.id']) {
				$itmes[$spId]['tasks'][] = [
					'id'       => $row['t.id'],
					'isActive' => boolval($row['t.isActive']),
				];
			}


		}
		$itmes = array_values($itmes);

		return $itmes;
	}

	/**
	 * @param string $sprintId
	 * @return int|string
	 * @throws Exception
	 */
	public function setClose(string $sprintId)
	{
		$conn = $this->_em->getConnection();
		$sprintId = $conn->quote($sprintId);
		return $conn->executeStatement("UPDATE `sprints` SET `is_active` = '0' WHERE `id` = {$sprintId}; ");
	}

	/**
	 * @return mixed|null
	 * @throws Exception
	 */
	public function getIdNearSprint()
	{
		$startAt = date("Y-m-d", mktime(0, 0, 0, 1, (App::getCurWeek() * 7)));

		$conn = $this->_em->getConnection();
		$id = $conn->fetchFirstColumn("SELECT s.id FROM `sprints` s WHERE  s.start_at >= '{$startAt}' AND s.is_active >= '1' LIMIT 1");

		if ($id) {
			$id = $id[0];
		} else {
			$id = null;
		}

		return $id;
	}
}