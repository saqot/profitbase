<?php

namespace App\Controller;

use App\Entity\SprintEntity;
use App\Entity\TaskEntity;
use App\Helper\App;
use DateTime;
use Doctrine\DBAL\Exception;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

/**
 * class:  SprintController
 * -----------------------------------------------------
 * @author  Saqot (Mihail Shirnin) <saqott@gmail.com>
 * @package  App\Controller
 * -----------------------------------------------------
 */
class SprintController extends AbstractController
{

	/**
	 * @param ManagerRegistry $doctrine
	 * @return Response
	 * @throws Exception
	 */
	public function addNewForm(ManagerRegistry $doctrine)
	{
		$repoSprint = $doctrine->getRepository(SprintEntity::class);
		$readySprintIds = $repoSprint->getReadyIds();

		$curWeek = App::getCurWeek();
		$weeks = [];
		for ($i = 0; ; $i++) {
			$week = ($curWeek + $i);
			$day = $week * 7;
			$mktime = mktime(0, 0, 0, 1, $day);
			$date = date("d.m.Y", $mktime);
			$year = date("y", $mktime);

			if (in_array("{$year}-{$week}", $readySprintIds)) {
				continue;
			}

			$weeks[$week] = "{$week} (с {$date})";
			if (count($weeks) >= 12) {
				break;
			}
		}

		return $this->render('sprint_form.html.twig', [
			'title'   => 'Новый спринт',
			'path'    => $this->generateUrl('api.sprint.update'),
			'curYear' => date('Y'),
			'curWeek' => $curWeek,
			'weeks'   => $weeks,
			'value'   => [
				'id'   => null,
				'year' => null,
				'week' => null,
			]
		]);
	}

	/**
	 * @param Request $request
	 * @param ManagerRegistry $doctrine
	 * @param CsrfTokenManagerInterface $csrfTokenManager
	 * @return JsonResponse|RedirectResponse
	 * @throws Exception
	 * @throws \Exception
	 */
	public function updateAction(Request $request, ManagerRegistry $doctrine, CsrfTokenManagerInterface $csrfTokenManager)
	{
		if (!$request->isXmlHttpRequest()) {
			return $this->redirectToRoute('web.sprint.add_new_form');
		}
		$oRrequest = $request->request;

		$token = $oRrequest->get('token');
		if (!$this->isCsrfTokenValid('sprint', $token)) {
			return $this->invalidJsonResponse('Csrf token не валиден. Обновите страницу и попробуйте еще раз.');
		}


		// -- week ---------------------------------
		$week = $oRrequest->get('week');
		if (!$week) {
			return $this->invalidJsonResponse('Неделя обязателена к заполнению.');
		}
		if (!is_numeric($week)) {
			return $this->invalidJsonResponse('Неделя должна быть числом');
		}
		$curWeek = App::getCurWeek();
		if ($week < $curWeek) {
			return $this->invalidJsonResponse('Неделя не может быть из прошлого');
		}

		$startAt = date("d.m.Y", mktime(0, 0, 0, 1, $week * 7));
		$startAt = new DateTime($startAt);

		$spId = "{$startAt->format("y")}-{$week}";
		$repoSprint = $doctrine->getRepository(SprintEntity::class);
		if (!$repoSprint->isUnique($spId)) {
			return $this->invalidJsonResponse("В этом году спринт на указанную неделю уже существует.");
		}

		$sp = (new SprintEntity())
			->setId($spId)
			->setIsActive(true)
			->setWeek($week)
			->setYear($startAt->format("Y"))
			->setCreateAt(new DateTime())
			->setStartAt($startAt);

		$em = $doctrine->getManager();

		$em->persist($sp);
		$em->flush();

		$response = new JsonResponse([
			'id'       => $sp->getId(),
			'msg'      => "Спринт #{$sp->getId()} создан. Вы сейчас будете перенаправлены на главную страницу.",
			'redirect' => $this->generateUrl('web.homepage.index'),
			'success'  => true,
		]);
		$response->setStatusCode(200);
		return $response;

	}

	/**
	 * @param Request $request
	 * @param ManagerRegistry $doctrine
	 * @param CsrfTokenManagerInterface $csrfTokenManager
	 * @return JsonResponse|RedirectResponse
	 * @throws Exception
	 */
	public function closeAction(Request $request, ManagerRegistry $doctrine, CsrfTokenManagerInterface $csrfTokenManager)
	{
		if (!$request->isXmlHttpRequest()) {
			return $this->redirectToRoute('web.homepage.index');
		}
		$oRrequest = $request->request;

		$token = $oRrequest->get('token');
		if (!$this->isCsrfTokenValid('sprint', $token)) {
			return $this->invalidJsonResponse('Csrf token не валиден. Обновите страницу и попробуйте еще раз.');
		}

		if (!$oRrequest->get('isConfirm')) {
			return $this->invalidJsonResponse('Нет подтверждения операции');
		}

		$spId = $oRrequest->get('sprintId');
		if (!$spId) {
			return $this->invalidJsonResponse('Не найден ID спринта');
		}

		$repoSprint = $doctrine->getRepository(SprintEntity::class);
		$sp = $repoSprint->find($spId);
		if (!$sp) {
			return $this->invalidJsonResponse("Спринт #{$spId} не найден");
		}

		$repoTaskUpdate = $doctrine->getRepository(TaskEntity::class);
		$cntTasks = $repoTaskUpdate->getCountActiveForSprint($spId);

		if ($cntTasks) {
			return $this->invalidJsonResponse("Закрытие спринта #{$spId} не возможно, в нем есть активные задачи.");
		}

		$sp->setIsActive(false);
		$em = $doctrine->getManager();

		$em->persist($sp);
		$em->flush();

		$response = new JsonResponse([
			'id'       => $sp->getId(),
			'msg'      => "Спринт #{$sp->getId()} закрыт.",
			'redirect' => $this->generateUrl('web.homepage.index'),
			'success'  => true,
		]);
		$response->setStatusCode(200);
		return $response;

	}

	private function invalidJsonResponse(string $message)
	{
		$response = new JsonResponse($message);
		$response->setStatusCode(400);
		return $response;
	}
}