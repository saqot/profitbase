<?php

namespace App\Controller;

use App\DTO\RequestTaskDTO;
use App\Entity\TaskEntity;
use App\Repository\SprintRepository;
use App\Repository\TaskRepository;
use DateTime;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * class:  TaskController
 * -----------------------------------------------------
 * @author  Saqot (Mihail Shirnin) <saqott@gmail.com>
 * @package  App\Controller
 * -----------------------------------------------------
 */
class TaskController extends AbstractController
{
	/**
	 * @param TaskRepository $repoTask
	 * @param $sprintId
	 * @param $taskId
	 * @return Response
	 */
	public function showForm(TaskRepository $repoTask, $sprintId = null, $taskId = null)
	{
		if ($sprintId != null) {
			$title = "Новое задание для спринта #{$sprintId}";
			$isNewTask = true;
		} else {
			$title = "Редактируем задание #{$taskId}";
			$isNewTask = false;
		}

		$value = [
			'id'          => null,
			'sprintId'    => $sprintId,
			'estimation'  => null,
			'title'       => null,
			'description' => null,

		];

		if ($taskId) {
			$task = $repoTask->find($taskId);
			if (!$task) {
				throw $this->createNotFoundException("Задача #{$taskId} не найдена");
			}

			$value = [
				'id'          => $task->getId(),
				'sprintId'    => $sprintId,
				'estimation'  => $task->getEstimation(),
				'title'       => $task->getTitle(),
				'description' => $task->getDescription(),

			];
		}

		return $this->render('task_form.html.twig', [
			'title'     => $title,
			'isNewTask' => $isNewTask,
			'path'      => $this->generateUrl('api.task.update'),
			'value'     => $value
		]);
	}

	/**
	 * @param Request $request
	 * @param TaskRepository $repoTask
	 * @param SprintRepository $repoSprint
	 * @param EntityManagerInterface $em
	 * @return JsonResponse|RedirectResponse
	 * @throws Exception
	 */
	public function updateAction(Request $request, TaskRepository $repoTask, SprintRepository $repoSprint, EntityManagerInterface $em)
	{
		if (!$request->isXmlHttpRequest()) {
			return $this->redirectToRoute('web.task.add_new_form');
		}
		$oRrequest = $request->request;

		$token = $oRrequest->get('token');
		if (!$this->isCsrfTokenValid('task', $token)) {
			return $this->invalidJsonResponse('Csrf token не валиден. Обновите страницу и попробуйте еще раз.');
		}

		$rTaskDTO = new RequestTaskDTO($oRrequest->all());


		// -- taskId ---------------------------------
		if ($taskId = $rTaskDTO->getId()) {
			$task = $repoTask->find($taskId);
			if (!$task) {
				return $this->invalidJsonResponse("Задача #{$taskId} в базе не найдена.");
			}
		} else {
			$task = (new TaskEntity())
				->setIsActive(true)
				->setAt(new DateTime());


			$taskIdNew = $this->genNewTaskId($repoTask);
			if (!$taskIdNew) {
				return $this->invalidJsonResponse('Возникла проблема с генерацией ID задачи');
			}
			$task->setId($taskIdNew);
		}

		if ($rTaskDTO->isNewEntry()) {
			if (!$rTaskDTO->getSprintId()) {
				return $this->invalidJsonResponse('Не указан спринт для задачи.');
			}
			if (!$rTaskDTO->getTitle()) {
				return $this->invalidJsonResponse('Заголовок обязателен для выполнения.');
			}
			if (!$rTaskDTO->getDescription()) {
				return $this->invalidJsonResponse('Описание обязательно для заполнения.');
			}
			if (!$rTaskDTO->getEstimation()) {
				return $this->invalidJsonResponse('Оценка обязательна для выполнения.');
			}
		}

		// -- sprintId ---------------------------------
		if ($sprintId = $rTaskDTO->getSprintId()) {
			$sprint = $repoSprint->find($sprintId);
			if (!$sprint) {
				return $this->invalidJsonResponse("Спринт #{$sprintId} в базе не найден.");
			}
			if (!$sprint->isActive()) {
				return $this->invalidJsonResponse("Спринт #{$sprintId} закрыт. Зачем что то редактировать в задаче ?");
			}

			$task->setSprint($sprint);
		}

		// -- title ---------------------------------
		if ($title = $rTaskDTO->getTitle()) {
			$txtLen = mb_strlen($title, 'UTF-8');
			if ($txtLen >= 250) {
				return $this->invalidJsonResponse("Длина заголовка большая ({$txtLen}) и может быть обрезана в базе. Попробуйте сократь до 250 символов.");
			}
			$task->setTitle($title);
		}

		// -- description ---------------------------------
		if ($description = $rTaskDTO->getDescription()) {
			$task->setDescription($description);
		}

		// -- estimation ---------------------------------
		if ($estimation = $rTaskDTO->getEstimation()) {
			if (!is_numeric($estimation)) {
				return $this->invalidJsonResponse('Оценочное время должно быть числом');
			}
			if ($estimation > 365) {
				return $this->invalidJsonResponse('Оценочное время очень большое. Зачем так много ? :)');
			}
			$task->setEstimation($estimation);
		}

		$em->persist($task);
		$em->flush();

		if ($rTaskDTO->isNewEntry()) {
			$msg = "Задача #{$task->getId()} создана. Вы сейчас будете перенаправлены на главную страницу.";
		} else {
			$msg = "Задача обновлена. Вы сейчас будете перенаправлены на главную страницу.";
		}

		$response = new JsonResponse([
			'id'       => $task->getId(),
			'msg'      => $msg,
			'redirect' => $this->generateUrl('web.homepage.index'),
			'success'  => true,
		]);
		$response->setStatusCode(200);
		return $response;

	}

	public function closeAction(Request $request, TaskRepository $repoTask, EntityManagerInterface $em)
	{
		if (!$request->isXmlHttpRequest()) {
			return $this->redirectToRoute('web.homepage.index');
		}
		$oRrequest = $request->request;

		$token = $oRrequest->get('token');
		if (!$this->isCsrfTokenValid('task', $token)) {
			return $this->invalidJsonResponse('Csrf token не валиден. Обновите страницу и попробуйте еще раз.');
		}

		if (!$oRrequest->get('isConfirm')) {
			return $this->invalidJsonResponse('Нет подтверждения операции');
		}

		$taskId = $oRrequest->get('taskId');
		if (!$taskId) {
			return $this->invalidJsonResponse('Не найден ID задачи');
		}

		$task = $repoTask->find($taskId);
		if (!$task) {
			return $this->invalidJsonResponse("Задача #{$taskId} не найдена");
		}

		$task->setIsActive(false);

		$em->persist($task);
		$em->flush();

		$response = new JsonResponse([
			'id'       => $task->getId(),
			'msg'      => "Задача #{$task->getId()} закрыта.",
			'redirect' => $this->generateUrl('web.homepage.index'),
			'success'  => true,
		]);
		$response->setStatusCode(200);
		return $response;
	}

	/**
	 * @param string $message
	 * @return JsonResponse
	 */
	private function invalidJsonResponse(string $message)
	{
		$response = new JsonResponse($message);
		$response->setStatusCode(400);
		return $response;
	}

	/**
	 * @param TaskRepository $repoTask
	 * @return string|null
	 * @throws Exception
	 */
	public function genNewTaskId(TaskRepository $repoTask)
	{
		for ($i = 0; $i <= 10; $i++) {
			$taskId = uniqid("TASK-");
			if ($repoTask->isUnique($taskId)) {
				return $taskId;
			}
		}
		return null;
	}

}