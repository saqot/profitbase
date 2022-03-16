<?php

namespace App\Command;

use App\Entity\SprintEntity;
use App\Entity\TaskEntity;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * class:  CloseSprintsCommand
 * -----------------------------------------------------
 * @author  Saqot (Mihail Shirnin) <saqott@gmail.com>
 * @package  App\Command
 * -----------------------------------------------------
 */
class CloseSprintsCommand extends Command
{
	/**
	 * php bin/console cron:closeSprints
	 * @var string
	 */
	protected static $defaultName = 'cron:closeSprints';
	protected OutputInterface $output;

	protected EntityManager $em;
	protected Connection $conn;

	/**
	 * UploadImageAppCommand constructor.
	 * @param ManagerRegistry $doctrine
	 */
	public function __construct(ManagerRegistry $doctrine)
	{
		$conn = $doctrine->getConnection();
		/** @var $conn Connection */
		$this->conn = $conn;

		$em = $doctrine->getManager();
		/** @var $em EntityManager */
		$this->em = $em;
		parent::__construct();
	}

	protected function configure()
	{

	}

	/**
	 * php bin/console cron:closeSprints
	 * @param InputInterface $input
	 * @param OutputInterface $output
	 * @return int
	 * @throws Exception
	 */
	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$io = new SymfonyStyle($input, $output);
		$this->output = $output;

		$repoSprint = $this->em->getRepository(SprintEntity::class);
		$repoTask = $this->em->getRepository(TaskEntity::class);

		$sprints = $repoSprint->getEndingsForClose();
		$nearSprintId = $repoSprint->getIdNearSprint();
		if (!$nearSprintId and $sprints) {
			$io->warning("Есть не закрытые спринты, но не найден спринт для переноса задач ((( Останавливаемся, требуется ручное вмешательство.");
			return Command::SUCCESS;
		}


		$closeSprints = 0;
		$movedTasks = 0;

		if ($sprints) {
			$bar = new ProgressBar($this->output, count($sprints));
			$bar->start();

			foreach ($sprints as $sp) {
				$bar->advance();
				foreach ($sp['tasks'] as $task) {
					$repoTask->changeSprint($task['id'], $nearSprintId);
					$movedTasks++;
				}
				$repoSprint->setClose($sp['id']);
				$closeSprints++;
			}

			$bar->finish();
			$io->newLine(2);
		}

		$io->success("Работа закончена. Закрыто спринтов:{$closeSprints} Перенесено просьб:{$movedTasks}");
		return Command::SUCCESS;

	}
}