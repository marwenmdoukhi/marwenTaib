<?php

declare(strict_types=1);

namespace App\Controller\Api\Archive;

use App\Entity\Act;
use App\Entity\Archive;
use App\Repository\ArchiveRepository;
use App\Service\ActService;
use App\Service\ArchiveService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class ArchivesByActAction extends AbstractController
{
    /**
     * @var ArchiveService
     */
    private ArchiveService $archiveService;

    /**+
     * @param ArchiveService $archiveService
     */
    public function __construct(ArchiveService $archiveService){

        $this->archiveService = $archiveService;
    }

    /**
     * @Route("/api/acts/get-archive-by-act/{id}",name="get_archives_by_act",options={"expose"=true},methods={"GET"})
     */
    public function getArchivesByActAction($id): JsonResponse
    {
        return new JsonResponse($this->archiveService->archivesByActAction($id), 200);
    }
}