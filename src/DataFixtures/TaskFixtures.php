<?php

namespace App\DataFixtures;

use App\Entity\SprintEntity;
use App\Entity\TaskEntity;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

/**
 * class:  TaskFixtures
 * -----------------------------------------------------
 * @author  Saqot (Mihail Shirnin) <saqott@gmail.com>
 * @package  App\DataFixtures
 * -----------------------------------------------------
 */
class TaskFixtures extends Fixture implements FixtureGroupInterface
{
	/**
	 * php bin/console doctrine:fixtures:load --group=task
	 * @param ObjectManager $manager
	 * @return void
	 */
	public function load(ObjectManager $manager): void
	{

		$repoSprint = $manager->getRepository(SprintEntity::class);
		$sprints = $repoSprint->findAll();

		foreach ($sprints as $sprint) {
			$cnt = mt_rand(3, 7);
			$hourMax = 2;
			for ($i = 0; $i < $cnt; $i++) {
				$hourMax = max($hourMax, $i);
				$hour = mt_rand($i, $hourMax);
				$hourMax = max($hourMax, $hour);
				$tsId = uniqid("TASK-");


				$ts = (new TaskEntity())
					->setId($tsId)
					->setIsActive(boolval(mt_rand(0, 1)))
					->setDescription("Description {$i} A card is a flexible and extensible content container. It includes options for headers and footers, a wide variety of content, contextual background colors, and powerful display options. If you’re familiar with Bootstrap 3, cards replace our old panels, wells, and thumbnails. Similar functionality to those components is available as modifier classes for cards.")
					->setTitle("Title {$i}")
					->setEstimation(mt_rand(1, 7))
					->setAt($sprint->getCreateAt()->modify("+{$hour} hour"))
					->setSprint($sprint);

				$manager->persist($ts);
				$manager->flush();

			}
		}


	}

	public static function getGroups(): array
	{
		return ['task'];
	}
}