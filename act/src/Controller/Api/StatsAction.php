<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Helpers\DateFormat;
use App\Repository\ActRepository;
use App\Repository\ActUserRepository;
use App\Service\MailService;
use Symfony\Component\HttpFoundation\JsonResponse;

class StatsAction
{
    /**
     * @var ActRepository
     */
    private ActRepository $actRepository;

    /**
     * @var ActUserRepository
     */
    private ActUserRepository $actUserRepository;

    /**
     * @var MailService
     */
    private MailService $mail;

    /**
     * @param ActRepository $actRepository
     * @param ActUserRepository $actUserRepository
     * @param MailService $mail
     */
    public function __construct(ActRepository $actRepository,ActUserRepository $actUserRepository,MailService $mail){

        $this->actRepository = $actRepository;
        $this->actUserRepository = $actUserRepository;
        $this->mail = $mail;
    }

    /**
     * @return JsonResponse
     */
    public function __invoke(): JsonResponse
    {
        $countSignedAct =  $this->actRepository ->countActSignedLastMonth(
            date(DateFormat::BEGINNINGMONTH,strtotime(DateFormat::MONTHPREVIOUS)),
            date(DateFormat::BEGINNINGMONTH)
        );
        $countValidatedAct =$this->actUserRepository->countActValidatedLastMonth(
            date(DateFormat::BEGINNINGMONTH,strtotime(DateFormat::MONTHPREVIOUS)),
            date(DateFormat::BEGINNINGMONTH)
        );
        $countWaitingSignAct = $this->actRepository->countActWaitingSigning();
        $countOfSignatures =$this->actUserRepository->countSignaturesCurrentYear(date(DateFormat::CURRENTYEAR));
        $countInitiator = $this->actRepository->countInitiator();
        $this->mail->sendStatsMail(
            $countSignedAct["nbActSigned"],
            $countValidatedAct["nbActValidated"],
            $countWaitingSignAct["nbActWaitingSign"],
            $countOfSignatures["nbSignature"],
            $countInitiator["nbInitiator"],
            date(DateFormat::PREVMONTH,strtotime(DateFormat::LASTMONTH)),
            date(DateFormat::YEAR)
        );
        return new JsonResponse('Mail sent with success', 200);
    }
}