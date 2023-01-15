<?php
/**
 * Created by PhpStorm.
 * User: Helmi
 * Date: 27/01/2020
 * Time: 14:19
 */

namespace App\Controller\Api;


use App\Entity\Act;
use App\Entity\ActUser;
use App\Entity\Contact;
use App\Entity\Piwik;
use App\Entity\User;
use App\Service\ActService;
use App\Service\MailService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use DateTime;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;


/**
 * Class ApiActController
 * @package App/Controller/Api
 * @Route("/api/piwik",name="api_piwik_")
 */
class ApiPiwikController extends AbstractController
{
    /**
     * @var ActService
     */
    private $actService;
    /**
     * @var MailService
     */
    private $mailService;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    protected $projectDir;


    public function __construct(ActService $actService, MailService $mailService, EntityManagerInterface $entityManager, KernelInterface $kernel)
    {
        $this->actService = $actService;
        $this->mailService = $mailService;
        $this->entityManager = $entityManager;
        $this->projectDir = $kernel->getProjectDir();
    }

    /**
     * @Route("/add-piwik",name="add_piwik",options={"expose"=true},methods={"POST"})
     * @param $request
     * @return JsonResponse
     */
    public function addPiwik(Request $request): JsonResponse
    {
        if (!$this->getUser()) {
            return new JsonResponse('Operation not permitted', 400);
        }
        $piwikArray = json_decode($request->getContent(), true);
        $piwik = new Piwik();
        $piwik->setDate(new DateTime());
        $piwik->setGuid($piwikArray['guid']);
        $piwik->setNavigateur($piwikArray['navigateur']);
        $piwik->setPiwikIgnore($piwikArray['piwikIgnore']);
        $piwik->setUser($this->getUser());
        $this->entityManager->persist($piwik);
        $this->entityManager->flush();
        return new JsonResponse('success', 200);
    }

    /**
     * @Route("/get-piwik",name="get_piwik",options={"expose"=true},methods={"GET"})
     * @return JsonResponse
     */
    public function getPiwik()
    {
        if (!$this->getUser()) {
            return new JsonResponse('Operation not permitted', 400);
        }
        $user = $this->getUser();
        $piwik = $this->entityManager->getRepository(Piwik::class)->findBy(array('user' => $user), array('date' => 'DESC'));
        if (sizeof($piwik) != 0 and (new DateTime())->diff($piwik[0]->getDate())->m < 13) {
            return new JsonResponse(false);
        } else if (sizeof($piwik) != 0 and (new DateTime())->diff($piwik[0]->getDate())->y > 5) {
            $this->entityManager->remove($piwik[0]);
            $this->entityManager->flush();
        }
        return new JsonResponse(true);
    }

}