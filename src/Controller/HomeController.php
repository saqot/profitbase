<?php

namespace App\Controller;

use App\Repository\SprintRepository;
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
	public function listSprintTasksAction(SprintRepository $repoSprint)
	{
		$sprints = $repoSprint->getFullListForHomePage();

		return $this->render('home.html.twig', [
			'title'   => 'Agile Board',
			'sprints' => $sprints,
		]);
	}
}