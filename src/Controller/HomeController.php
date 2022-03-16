<?php

namespace App\Controller;

use App\Entity\SprintEntity;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * class:  HomeController
 * -----------------------------------------------------
 * @author  Saqot (Mihail Shirnin) <saqott@gmail.com>
 * @package  App\Controller
 * -----------------------------------------------------
 */
class HomeController extends AbstractController
{
	public function listSprintTasksAction(ManagerRegistry $doctrine)
	{
		$repoSprint = $doctrine->getRepository(SprintEntity::class);
		$sprints = $repoSprint->getFullListForHomePage();

		return $this->render('home.html.twig', [
			'title'   => 'Agile Board',
			'sprints' => $sprints,
		]);
	}
}