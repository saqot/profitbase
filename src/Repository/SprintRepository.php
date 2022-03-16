<?php

namespace App\Repository;

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
            LEFT JOIN (select * from `tasks`  WHERE is_active = '1') t ON s.id = t.sprint_id
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
}