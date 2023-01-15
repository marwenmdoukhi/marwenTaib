<?php
/**
 * Created by PhpStorm.
 * User: MAZ-USER5
 * Date: 07/11/2019
 * Time: 11:55
 */

namespace App\Controller\Api;


use App\Entity\Act;
use App\Entity\CguUser;
use App\Entity\Search;
use App\Entity\User;
use App\Service\ActService;
use App\Service\CounselService;
use App\Service\SignatoryService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;


/**
 * Class ApiSearchController
 * @package App/Controller/Api
 * @Route("/api/search",name="api_search_")
 */
class ApiSearchController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/",name="get",options={"expose"=true},methods={"GET"})
     * @return JsonResponse
     */
    public function getSearchAction(): JsonResponse
    {
        if (!$this->getUser()) {
            return new JsonResponse('Operation not permitted', 400);
        }
        $result = $this->entityManager->getRepository(Search::class)->findBySearch();
        return new JsonResponse($result, 200);
    }

    /**
     * @Route("/",name="new",options={"expose"=true},methods={"POST"})
     * @param $request
     * @return JsonResponse
     */
    public function newSearchAction(Request $request): JsonResponse
    {
        if (!$this->getUser()) {
            return new JsonResponse('Operation not permitted', 400);
        }
        $array = json_decode($request->getContent(), true);
        $request->request->replace(is_array($array) ? $array : array());

        $search = new Search();
        $search->setType($array['type']);
        $search->setIdEntity($array['idEntity']);
        $search->setIdUser($array['idUser']);
        $this->entityManager->persist($search);
        $this->entityManager->flush();


        return new JsonResponse('', 200);
    }

    /**
     * @Route("/",name="delete",options={"expose"=true},methods={"DELETE"})
     * @param $request
     * @return JsonResponse
     */
    public function deleteSearchAction(Request $request): JsonResponse
    {
        if (!$this->getUser()) {
            return new JsonResponse('Operation not permitted', 400);
        }
        $array = json_decode($request->getContent(), true);
        $request->request->replace(is_array($array) ? $array : array());

        $search = $this->entityManager->getRepository(Search::class)->find($array['id']);
        $this->entityManager->remove($search);
        $this->entityManager->flush();


        return new JsonResponse('', 200);
    }


}