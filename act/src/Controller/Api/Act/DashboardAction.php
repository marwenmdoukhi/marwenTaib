<?php

namespace App\Controller\Api\Act;

use App\Repository\ActRepository;
use App\Security\AuthService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
class DashboardAction
{
    /**
     * @var ActRepository
     */
    private $actRepository;
    /**
     * @var AuthService
     */
    private $authService;

    /**
     * @param ActRepository $actRepository
     * @param AuthService $authService
     */
    public function __construct(ActRepository $actRepository, AuthService $authService)
    {
        $this->actRepository = $actRepository;
        $this->authService = $authService;
    }

    /**
     * @Route("/api/acts/dashboard-count",name="dashboard_count",options={"expose"=true},methods={"GET"})
     */
    public function __invoke(): JsonResponse
    {
        if (!$this->authService->getUser()) {
            return new JsonResponse('Operation not permitted', 400);
        }
        $nbActInProgress = $this->actRepository->countActInProgress($this->authService->getUser());
        $nbActSigning = $this->actRepository->countActSigning($this->authService->getUser());
        $nbActRefused = $this->actRepository->countActSigned($this->authService->getUser());
        $result = array_merge($nbActInProgress, $nbActSigning, $nbActRefused);
        $firstBlockSigned = $this->actRepository->getLastThreeSignedActs($this->authService->getUser()->getId());
        $firstBlockValidated = $this->actRepository->getLastThreeValidatedActs($this->authService->getUser()->getId());
        $firstBlock = array_merge($firstBlockSigned, $firstBlockValidated);

        return new JsonResponse([$firstBlock, $result], 200);
    }
}
