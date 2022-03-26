<?php

namespace App\Command;

use App\Repository\SprintRepository;
use App\Repository\TaskRepository;
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
	private TaskRepository $repoTask;
	private SprintRepository $repoSprint;

	/**
	 * UploadImageAppCommand constructor.
	 * @param TaskRepository $repoTask
	 * @param SprintRepository $repoSprint
	 */
	public function __construct(TaskRepository $repoTask, SprintRepository $repoSprint)
	{
		$this->repoTask = $repoTask;
		$this->repoSprint = $repoSprint;

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

		$sprints = $this->repoSprint->getEndingsForClose();
		$nearSprintId = $this->repoSprint->getIdNearSprint();

		if (!$nearSprintId and $sprints) {
			$io->warning("Есть не закрытые спринты, но не найден спринт для переноса задач ((( Останавливаемся, требуется ручное вмешательство.");
			return Command::SUCCESS;
		}

		$closeSprints = 0;
		$movedTasks = 0;

		if ($sprints) {
			$bar = new ProgressBar($output, count($sprints));
			$bar->start();

			foreach ($sprints as $sp) {
				$bar->advance();
				foreach ($sp['tasks'] as $task) {
					$this->repoTask->changeSprint($task['id'], $nearSprintId);
					$movedTasks++;
				}
				$this->repoSprint->setClose($sp['id']);
				$closeSprints++;
			}

			$bar->finish();
			$io->newLine(2);
		}

		$io->success("Работа закончена. Закрыто спринтов:{$closeSprints} Перенесено просьб:{$movedTasks}");
		return Command::SUCCESS;

	}
}