<?php

declare(strict_types=1);

namespace App\Controller\Api\Archive;

use App\Entity\Act;
use App\Security\AuthService;
use App\Service\ArchiveService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class ArchivesByUserAction
{
    /**
     * @var ArchiveService
     */
    private ArchiveService $archiveService;

    /**
     * @param ArchiveService $archiveService
     */
    public function __construct (ArchiveService $archiveService){

        $this->archiveService = $archiveService;
    }

    /**
     * @Route("/api/acts/get-archive",name="get_archive",options={"expose"=true},methods={"GET"})
     * @return JsonResponse
     */
    public function __invoke(): JsonResponse
    {
        return new JsonResponse($this->archiveService->actArchive(), 200);
    }
}
