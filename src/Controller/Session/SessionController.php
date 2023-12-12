<?php

namespace App\Controller\Session;

use App\Controller\BaseController;
use App\Form\Session\SessionType;
use App\Repository\Session\SessionRepository;
use App\Service\Session\SessionService;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Requirement\Requirement;

class SessionController extends BaseController
{
    /**
     * @throws EntityNotFoundException
     */
    #[Route('/join/{id}', name: 'session_by_id', requirements: ['id' => Requirement::UUID], methods: ['GET'])]
    public function share(Request $request, SessionRepository $sessionRepository): Response
    {
        // TODO show website
        $session = $sessionRepository->findBySessionId($request->get('id'));

        return $this->jsonResponse($session);
    }

    #[Route('/session', methods: ['GET'])]
    public function getAll(SessionRepository $sessionRepository): Response
    {
        $sessions = $sessionRepository->getAllByUser($this->getUser());

        return $this->jsonResponse($sessions);
    }

    /**
     * @throws EntityNotFoundException
     */
    #[Route('/session/{id}', requirements: ['id' => Requirement::UUID], methods: ['GET'])]
    public function get(Request $request, SessionRepository $sessionRepository): Response
    {
        $session = $sessionRepository->findBySessionId($request->get('id'));

        return $this->jsonResponse($session);
    }

    #[Route('/session', methods: ['POST'])]
    public function create(Request $request, SessionService $sessionService, SessionRepository $sessionRepository): Response
    {
        $session = $sessionService->createSession();
        $form = $this->createForm(SessionType::class, $session);
        $form->submit($request->toArray());

        if ($form->isValid()) {
            $sessionRepository->save($session);

            return $this->jsonResponse($session);
        }

        // response error
        return $this->jsonResponse([
            'message' => 'Error',
        ]);
    }

    /**
     * @throws EntityNotFoundException
     */
    #[Route('/session/join', methods: ['POST'])]
    public function join(Request $request, SessionService $sessionService): Response
    {
        $sessionService->joinSession($request->get('id'));

        return $this->jsonResponse([]);
    }
}