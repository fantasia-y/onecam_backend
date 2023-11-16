<?php

namespace App\Controller\Session;

use App\Controller\BaseController;
use App\Form\Session\SessionType;
use App\Repository\Session\SessionRepository;
use App\Service\Session\SessionService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SessionController extends BaseController
{
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
}