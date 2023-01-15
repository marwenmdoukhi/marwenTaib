<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Act;
use App\Repository\ActRepository;
use App\Repository\ArchiveRepository;
use App\Security\AuthService;
use Symfony\Component\HttpFoundation\JsonResponse;

class ArchiveService
{

    /**
     * @var ActRepository
     */
    private ActRepository $actRepository;

    /**
     * @var AuthService
     */
    private AuthService $authService;
    private ArchiveRepository $archive;

    public function __construct(ActRepository $actRepository,AuthService  $authService,ArchiveRepository $archive)
    {
        $this->actRepository = $actRepository;
        $this->authService = $authService;
        $this->archive = $archive;
    }

    /**
     * @param $param
     * @return array|null
     */
    public function councelActs($param): ?array
    {
        $acts = $this->actRepository->findCouncelActs($param);
        $councelActs = [];
        foreach ($acts as $act) {
            $act['requestDate']->format('d/m/Y H:i:s');
            (false === isset($act['lastResentDate']) ? null : $act['lastResentDate']->format('d/m/Y H:i'));
            (false === isset($act['signingDate']) ? null : $act['signingDate']->format('d/m/Y'));
            (false === isset($act['receptionDate']) ? null : $act['receptionDate']->format('d/m/Y H:i:s'));
            $councelActs[] =$act;
        }
        return $councelActs;
    }

    /**
     * @return array|float|int|string
     */
    public function actArchive()
    {

        $acts = $this->councelActs($this->authService->getUser()->getId());
        $archives = [];
        foreach ($acts as $act)
        {
            $arch = $this->archive->findArchiveByAct($act['id']);
            if (true === isset($arch)) {
                $archives = array_merge($archives, $arch);
            }
        }
        return $archives;
    }

    public function archivesByActAction($param)
    {
        if (!$this->authService->getUser()) {
            return new JsonResponse('Operation not permitted', 400);
        }
        if ($this->authService->checkRole()) {
            return new JsonResponse('wrong role', 400);
        }
        return $this->archive->findArchiveByAct($param);
    }
}