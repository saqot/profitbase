<?php

namespace App\DataFixtures;

use App\Entity\SprintEntity;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

/**
 * class:  SprintFixtures
 * -----------------------------------------------------
 * @author  Saqot (Mihail Shirnin) <saqott@gmail.com>
 * @package  App\DataFixtures
 * -----------------------------------------------------
 */
class SprintFixtures extends Fixture
{
	/**
	 * php bin/console doctrine:fixtures:load
	 * @param ObjectManager $manager
	 * @return void
	 */
	public function load(ObjectManager $manager): void
	{

		$spIds = [];
		for ($i = 0; $i < 20; $i++) {
			$day = mt_rand(1, 29);
			$month = mt_rand(1, 12);
			$year = mt_rand(2021, 2023);
			$at = new DateTime("{$year}-{$month}-{$day}");
			$w = (int)$at->format("W");
			$y = $at->format("y");

			$spId = "{$y}-{$w}";
			if (in_array($spId, $spIds)) {
				continue;
			}

			$createAt = $at->modify('-1 day');
			$sp = (new SprintEntity())
				->setId($spId)
				->setIsActive(boolval(mt_rand(0, 1)))
				->setWeek($w)
				->setYear($year)
				->setCreateAt($createAt)
				->setStartAt($at);

			$manager->persist($sp);
			$manager->flush();

			$spIds[] = $spId;
		}

	}

}
