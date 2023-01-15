<?php


namespace App\Controller\Api;


use App\Entity\Act;
use App\Entity\Contact;
use App\Entity\User;
use App\Service\CounselService;
use App\Service\SignatoryService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/**
 * Class ApiBasicUserController
 * @package App/Controller/Api
 * @Route("/api/basic_users",name="api_basic_user_")
 */
class ApiBasicUserController extends AbstractController
{
    /**
     * @var SignatoryService
     */
    private $signatoryService;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var CounselService
     */
    private $counselService;

    public function __construct(SignatoryService $signatoryService, CounselService $counselService, EntityManagerInterface $entityManager)
    {
        $this->signatoryService = $signatoryService;
        $this->entityManager = $entityManager;
        $this->counselService = $counselService;

    }

    /**
     * @Route("/",name="new",options={"expose"=true},methods={"POST"})
     * @param $request
     * @return JsonResponse
     */
    public function newBasicUserAction(Request $request): JsonResponse
    {
        if (!$this->getUser()) {
            return new JsonResponse('Operation not permitted', 400);
        }
        $result = false;
        $resultCounsel = false;
        $user = $this->getUser();
        $basicUserArray = json_decode($request->getContent(), true);
        $request->request->replace(is_array($basicUserArray) ? $basicUserArray : array());
        if ($basicUserArray == '') {
            return new JsonResponse('no body found', 404);
        }
        $id = $basicUserArray['actId'];
        $act = $this->entityManager->getRepository(Act::class)->find($id);
        if ($this->checkRole() == true || ($act->getStatus() === 'Signee' && $act->getStatus() === 'En cour de signature')) {
            return new JsonResponse('wrong role or wrong status', 400);
        }
        if ($basicUserArray['role'] == 'signatory' or $basicUserArray['role'] == 'enterprise') {
            $result = $this->signatoryService->newSignatory($basicUserArray, $user);
        }
        if ($result != false) {
            return new JsonResponse($result, 200);
        }
        if ($basicUserArray['role'] == 'counsel') {
            $resultCounsel = $this->counselService->newCounsel($basicUserArray, $user);
        }
        if ($resultCounsel == 'exist') {
            return new JsonResponse('exist', 500);
        }
        if ($resultCounsel != false) {
            return new JsonResponse($resultCounsel, 200);
        }
        return new JsonResponse('error while assigning user to this act', 500);
    }

    /**
     * @Route("/signatories" ,name="get_signatories",options={"expose"=true},methods={"GET"})
     * @return JsonResponse
     */
    public function getSignatoriesAction(): JsonResponse
    {
        if (!$this->getUser()) {
            return new JsonResponse('Operation not permitted', 400);
        }
        $user = $this->getUser();
        if ($user->getRoles()[0] == "ROLE_COUNSEL") {
            $basicUsers = $this->entityManager->getRepository(User::class)->findSignatoryForCounsel($this->getUser());
        } else {
            $basicUsers = $this->entityManager->getRepository(User::class)->findSignatoryByCreatedBy($this->getUser());
        }


        return new JsonResponse($basicUsers, 200);

    }

    /**
     * @Route("/counsels" ,name="get_counsels",options={"expose"=true},methods={"GET"})
     * @return JsonResponse
     */
    public function getCounselsAction(): JsonResponse
    {
        if (!$this->getUser()) {
            return new JsonResponse('Operation not permitted', 400);
        }
        $user = $this->getUser();
        if ($user->getRoles()[0] == "ROLE_COUNSEL") {
            $basicUsers = $this->entityManager->getRepository(User::class)->findCounselForCounsel($this->getUser());
        } else {
            $basicUsers = $this->entityManager->getRepository(User::class)->findCounselByCreatedBy($this->getUser());
        }

        return new JsonResponse($basicUsers, 200);

    }

    /**
     * @Route("/autocomplete" ,name="autocomplete",options={"expose"=true},methods={"POST"})
     * @return JsonResponse
     */
    public function autocompleteAction(Request $request): JsonResponse
    {
        if ($this->checkRole() == true) {
            return new JsonResponse('wrong role', 400);
        }
        $name = $request->get('name');
        $lastname = $request->get('lastname');
        $user = $this->getUser();
        $annuaireUsers = $this->entityManager->getRepository(User::class)->findEdentitasByCnbIdAuto($user, $name, $lastname);
        $basicUsers = $this->entityManager->getRepository(User::class)->findCounselByCreatedByAuto($this->getUser(), $name, $lastname);
        $basicUsers = array_merge($basicUsers, $annuaireUsers);
        $result = $this->unique_multidimensional_array($basicUsers);

        return new JsonResponse($result, 200);

    }

    /**
     * @Route("/signatories_by_act/{id}" ,name="get_signatories_by_act",options={"expose"=true},methods={"GET"})
     * @ParamConverter("Act", options={"id" = "id"})
     * @param Act $act
     * @return JsonResponse
     */
    public function getSignatoriesByActAction(Act $act): JsonResponse
    {
        if (!$this->getUser()) {
            return new JsonResponse('Operation not permitted', 400);
        }
        $basicUsers = $this->entityManager->getRepository(User::class)->findSignatoryByAct($act->getId());
        $basicUserJson = [];
        foreach ($basicUsers as $basicUser) {
            $basicUser['birthDate'] = $basicUser['birthDate'] != null ? $basicUser['birthDate'] : null;
            $basicUser['validatedAt'] = $basicUser['validatedAt'] != null ? $basicUser['validatedAt'] : null;
            $basicUser['signedAt'] = $basicUser['signedAt'] != null ? $basicUser['signedAt'] : null;
            array_push($basicUserJson, $basicUser);
        }

        return new JsonResponse($basicUserJson, 200);

    }

    /**
     * @Route("/counsels_by_act/{id}" ,name="get_counsels_by_act",options={"expose"=true},methods={"GET"})
     * @ParamConverter("Act", options={"id" = "id"})
     * @param Act $act
     * @return JsonResponse
     */
    public function getCounselsByActAction(Act $act): JsonResponse
    {
        if (!$this->getUser()) {
            return new JsonResponse('Operation not permitted', 400);
        }
        $basicUsers = $this->entityManager->getRepository(User::class)->findCounselByAct($act->getId());
        $basicUserJson = [];
        foreach ($basicUsers as $basicUser) {
            $basicUser['birthDate'] = $basicUser['birthDate'] != null ? $basicUser['birthDate'] : null;
            $basicUser['validatedAt'] = $basicUser['validatedAt'] != null ? $basicUser['validatedAt'] : null;
            $basicUser['signedAt'] = $basicUser['signedAt'] != null ? $basicUser['signedAt'] : null;
            array_push($basicUserJson, $basicUser);
        }

        return new JsonResponse($basicUserJson, 200);

    }

    /**
     * @Route("/delete_signatory", name="delete_signatory" , options={"expose"=true},methods={"DELETE"})
     * @param Request $request
     * @return JsonResponse
     */
    public function deleteSignatoryAction(Request $request): JsonResponse
    {
        if (!$this->getUser()) {
            return new JsonResponse('Operation not permitted', 400);
        }

        $signatoryArray = json_decode($request->getContent(), true);
        $request->request->replace(is_array($signatoryArray) ? $signatoryArray : array());
        $id = $signatoryArray['actId'];
        $act = $this->entityManager->getRepository(Act::class)->find($id);
        if ($this->checkRole() == true || ($act->getStatus() === 'Signee' && $act->getStatus() === 'En cour de signature')) {
            return new JsonResponse('wrong role or wrong status', 400);
        }

        $result = $this->signatoryService->deleteSignatory($signatoryArray);
        if ($result) {
            return new JsonResponse('Signatory deleted', 200);
        } else {
            return new JsonResponse('Error', 500);
        }
    }

    /**
     * @Route("/delete_counsel", name="delete_counsel" , options={"expose"=true},methods={"DELETE"})
     * @param Request $request
     * @return JsonResponse
     */
    public function deleteCounselAction(Request $request): JsonResponse
    {
        if (!$this->getUser()) {
            return new JsonResponse('Operation not permitted', 400);
        }

        $counselArray = json_decode($request->getContent(), true);
        $request->request->replace(is_array($counselArray) ? $counselArray : array());
        $id = $counselArray['actId'];
        $act = $this->entityManager->getRepository(Act::class)->find($id);
        if ($this->checkRole() == true || ($act->getStatus() === 'Signee' && $act->getStatus() === 'En cour de signature')) {
            return new JsonResponse('wrong role or wrong status', 400);
        }

        $result = $this->counselService->deleteCounsel($counselArray);
        if ($result) {
            return new JsonResponse('counsel deleted', 200);
        } else {
            return new JsonResponse('Error', 500);
        }
    }

    /**
     * @Route("/",name="edit",options={"expose"=true},methods={"PUT"})
     * @param $request
     * @return JsonResponse
     */
    public function editBasicUserAction(Request $request): JsonResponse
    {
        if (!$this->getUser()) {
            return new JsonResponse('Operation not permitted', 400);
        }

        $basicUserArray = json_decode($request->getContent(), true);
        $request->request->replace(is_array($basicUserArray) ? $basicUserArray : array());
        $id = $basicUserArray['actId'];
        $act = $this->entityManager->getRepository(Act::class)->find($id);
        if ($this->checkRole() == true || ($act->getStatus() === 'Signee' && $act->getStatus() === 'En cour de signature')) {
            return new JsonResponse('wrong role or wrong status', 400);
        }

        if ($basicUserArray == '') {
            return new JsonResponse('no body found', 404);
        }
        $result = $this->signatoryService->editBasicUser($basicUserArray);
        if ($result != false) {
            return new JsonResponse('User has been modified', 200);
        }
        return new JsonResponse('error while modifying user ', 500);
    }

    /**
     * @Route("/edit_email_phone",name="edit_email_phone",options={"expose"=true},methods={"POST"})
     * @param $request
     * @return JsonResponse
     */
    public function editBasicUserEmailAndPhoneAction(Request $request): JsonResponse
    {
        if (!$this->getUser()) {
            return new JsonResponse('Operation not permitted', 400);
        }

        if ($this->checkRole() == true) {
            return new JsonResponse('wrong role', 400);
        }
        $email = $request->get('email');
        $phone = $request->get('phone');
        $codeCountry = $request->get('codeCountry');
        $user = $this->entityManager->getRepository(User::class)->find($this->getUser()->getId());
        if ($email && (ltrim($user->getEmail(), '0') == $user->getCnbId() . 'cnb@cnb.fr' || ltrim($user->getEmail(), '0') == $user->getCnbId() . '@cnb.fr')) {
            $user->setEmailApp($email);
            $user->setEmail($email);
        } else if ($email) {
            $user->setEmailApp($email);
        }else if ($email == ""){
            $user->setEmailApp(null);
        }

        if ($phone) {
            $user->setPhoneNumberApp($phone);
            $user->setCodeCountryApp($codeCountry);
        }else if (empty($phone)){
            $user->setPhoneNumberApp(null);
        }
        $user->setIpaddress(isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR']);
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return new JsonResponse('user updated', 200);
    }

    private function unique_multidimensional_array($array)
    {
        $resultTable = [];

        foreach ($array as $val) {
            if ($this->exist($val, $resultTable) == false) {
                array_push($resultTable, $val);
            }

        }

        return $resultTable;
    }

    public function exist($var, $array)
    {
        foreach ($array as $v) {
            if ($v['id'] == $var['id']) {
                return true;
            }
        }
        return false;
    }

    private function checkRole()
    {
        $result = false;
        if ($this->getUser()) {
            if ($this->getUser()->getRoles()[0] == 'ROLE_SIGNATORY' or $this->getUser()->getRoles()[0] == 'ROLE_ENTERPRISE') {
                $result = true;
            }
        }
        return $result;
    }


}