<?php
/**
 * Created by PhpStorm.
 * User: MAZ-USER5
 * Date: 07/11/2019
 * Time: 11:55
 */

namespace App\Controller\Api;


use App\Entity\Act;
use App\Entity\Document;
use App\Service\DocumentService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Validator\Constraints\Json;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Class ApiDocumentController
 * @package App/Controller/Api
 * @Route("/api/documents",name="api_document_")
 */
class ApiDocumentController extends AbstractController
{
    /**
     * @var DocumentService
     */
    private $documentService;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    protected $projectDir;
    private $logger;



    public function __construct(DocumentService $documentService, EntityManagerInterface $entityManager, KernelInterface $kernel, LoggerInterface $oodriveLogger)
    {
        $this->documentService = $documentService;
        $this->entityManager = $entityManager;
        $this->projectDir = $kernel->getProjectDir();
        $this->logger = $oodriveLogger;

    }

    /**
     * @Route("/",name="new",options={"expose"=true},methods={"POST"})
     * @param $request
     * @return JsonResponse
     */
    public function newDocumentAction(Request $request): JsonResponse
    {
        $documentArray = json_decode($request->getContent(), true);
        $request->request->replace(is_array($documentArray) ? $documentArray : array());
        $id = $documentArray['actId'];
        $act = $this->entityManager->getRepository(Act::class)->find($id);
        if ($this->checkRole() == true || ($act->getStatus() !== 'Cree' && $act->getStatus() !== 'En Projet' && $act->getStatus() !== 'Validation refusee') && $act->getStatus() !== 'Signature refusee') {
            return new JsonResponse('wrong role or wrong status here', 400);
        }
        if ($documentArray == '') {
            return new JsonResponse('no param document found', 404);
        }
        if (sizeof($documentArray) == 0) {
            return new JsonResponse('no document to add', 404);
        }
        if (!array_key_exists('file', $documentArray) or !array_key_exists('extension', $documentArray) or !array_key_exists('name', $documentArray) or !array_key_exists('actId', $documentArray)) {
            return new JsonResponse('a required key in body is missing', 404);
        }
        if ($documentArray['file'] == '' or $documentArray['extension'] == '' or $documentArray['name'] == '' or $documentArray['actId'] == null) {
            return new JsonResponse('a required value in body is null or empty', 404);
        }

        $result = $this->documentService->newDocument($documentArray);
        if ($result == false) {
            return new JsonResponse('error while creating the documents', 400);
        } elseif ($result == 'not a valid pdfa2b') {
            return new JsonResponse($result, 200);
        } elseif ($result == 'error while upload document to s3'){
            return new JsonResponse($result, 200);
        } elseif ($result == 'error when uploading new document to S3'){
            return new JsonResponse($result, 400);
        }
        return new JsonResponse($result, 200);
    }

    /**
     * @Route("/",name="delete",options={"expose"=true},methods={"DELETE"})
     * @param $request
     * @return JsonResponse
     */
    public function deleteDocumentAction(Request $request): JsonResponse
    {
        $documentArray = json_decode($request->getContent(), true);
        $request->request->replace(is_array($documentArray) ? $documentArray : array());
        $id = $documentArray['actId'];
        $act = $this->entityManager->getRepository(Act::class)->find($id);
        if ($this->checkRole() == true || ($act->getStatus() === 'Signee' && $act->getStatus() === 'En cour de signature' && $act->getStatus() === 'En cour de validation')) {
            return new JsonResponse('wrong role or wrong status', 400);
        }
        if ($documentArray == '') {
            return new JsonResponse('no param document found', 404);
        }
        if (sizeof($documentArray) == 0) {
            return new JsonResponse('no document to delete', 404);
        }
        if (!array_key_exists('name', $documentArray) or !array_key_exists('actId', $documentArray)) {
            return new JsonResponse('a required key in body is missing', 404);
        }
        $result = $this->documentService->deleteDocument($documentArray);
        if ($result != true) {
            return new JsonResponse('error while deleting the documents', 400);
        }
        return new JsonResponse('document deleted', 200);
    }

    /**
     * @Route("/document-position",name="document_position",options={"expose"=true},methods={"PUT"})
     * @param $request
     * @return JsonResponse
     */
    public function updateDocumentPositionAction(Request $request): JsonResponse
    {
        if (!$this->getUser()) {
            return new JsonResponse('Operation not permitted', 400);
        }

        $documentArray = json_decode($request->getContent(), true);
        $request->request->replace(is_array($documentArray) ? $documentArray : array());
        $id = $documentArray[0]['actId'];
        $act = $this->entityManager->getRepository(Act::class)->find($id);
        if ($this->checkRole() == true || ($act->getStatus() === 'Signee' && $act->getStatus() === 'En cour de signature')) {
            return new JsonResponse('wrong role or wrong status', 400);
        }
        $result = $this->documentService->positionDocument($documentArray);
        if ($result != true) {
            return new JsonResponse('positioning error', 400);
        }

        return new JsonResponse('positioning done', 200);
    }


    /**
        * @Route("/",name="get_documents",options={"expose"=true},methods={"GET"})
        * @return JsonResponse
    */
    public function getDocumentsAction(): JsonResponse
    {
      return new JsonResponse('Operation not permitted', 404);
    }

    /**
     * @Route("/merge",name="merge_pdf",options={"expose"=true},methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function mergeDocumentsAction(Request $request): JsonResponse
    {
        if (!$this->getUser()) {
            return new JsonResponse('Operation not permitted', 400);
        }
        $documentArray = json_decode($request->getContent(), true);
        $request->request->replace(is_array($documentArray) ? $documentArray : array());
        $result = $this->documentService->mergeDocuments($documentArray);
        if ($result == 'error while merging pdf') {
            return new JsonResponse($result, 500);
        }
        return new JsonResponse($result, 200);
    }

    /**
     * @Route("/merge-signedmerge-signed",name="merge_pdf_signed",options={"expose"=true},methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function mergeDocumentsSignedAction(Request $request): JsonResponse
    {
        if (!$this->getUser()) {
            return new JsonResponse('Operation not permitted', 400);
        }
        $documentArray = json_decode($request->getContent(), true);
        $request->request->replace(is_array($documentArray) ? $documentArray : array());
        $result = $this->documentService->mergeDocumentsSigned($documentArray);
        if ($result == 'error while merging pdf') {
            return new JsonResponse($result, 500);
        }
        return new JsonResponse($result, 200);
    }

//    /**
//     * @Route("/",name="get_documents",options={"expose"=true},methods={"GET"})
//     * @return JsonResponse
//     */
//    public function getDocumentsAction(): JsonResponse
//    {
//        if (!$this->getUser()) {
//            return new JsonResponse('Operation not permitted', 400);
//        } else {
//            $documents = $this->entityManager->getRepository(Document::class)->findAllDocuments();
//            return new JsonResponse($documents, 200);
//        }
//    }

    /**
     * @Route("/acts-documents",name="get_acts_documents",options={"expose"=true},methods={"POST"})
     * @return JsonResponse
     */
    public function getActsDocumentsAction(Request $request): JsonResponse
    {
        if (!$this->getUser()) {
            return new JsonResponse('Operation not permitted', 400);
        } else {
            $actArray = json_decode($request->getContent(), true);
            $request->request->replace(is_array($actArray) ? $actArray : array());
            $user = $this->getUser()->getId();
            if ($this->getUser()->getRoles()[0]=="ROLE_COUNSEL"){
                $documents = $this->entityManager->getRepository(Document::class)->findDocumentForAct($actArray);
            }else{
                $documents = $this->entityManager->getRepository(Document::class)->findDocumentByActs($actArray,$user);
            }
            return new JsonResponse($documents, 200);
        }
    }
    /**
     * @Route("/documents-act",name="get_documents_for_act",options={"expose"=true},methods={"POST"})
     * @return JsonResponse
     */
    public function getDocumentsForActAction(Request $request): JsonResponse
    {
        if (!$this->getUser()) {
            return new JsonResponse('Operation not permitted', 400);
        } else {
            $actArray = json_decode($request->getContent(), true);
            $request->request->replace(is_array($actArray) ? $actArray : array());
            $documents = $this->entityManager->getRepository(Document::class)->findDocumentForAct($actArray);
            return new JsonResponse($documents, 200);
        }
    }

    /**
     * @Route("/get-merge",name="get_merge",options={"expose"=true},methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function getMergeDocumentsAction(Request $request): JsonResponse
    {
        if (!$this->getUser()) {
            return new JsonResponse('Operation not permitted', 400);
        }

        if ($_ENV['DEV_MODE']=='true') {
            $documentArray = json_decode($request->getContent(), true);
            $request->request->replace(is_array($documentArray) ? $documentArray : array());
            $act = $this->entityManager->getRepository(Act::class)->find($documentArray[0]);
            $documents = $this->entityManager->getRepository(Document::class)->findBy(array('act' => $act->getId()), array('position' => 'ASC'));

            $name = "";
            foreach ($documents as $doc) {
                $name = $doc->getName() . ".pdf";
                break;
            }
            $result = stripslashes(base64_encode(file_get_contents($this->projectDir . '/src/assets/documents/' . $act->getId() . '/' . $name)));
        }
        else {
            $documentArray = json_decode($request->getContent(), true);
            $request->request->replace(is_array($documentArray) ? $documentArray : array());
            $act = $this->entityManager->getRepository(Act::class)->find($documentArray[0]);
            $exec_commandExport = "export AWS_PROFILE=cloudian";
            $exec_command_output = " AWS_ACCESS_KEY_ID=" . $_ENV["AWS_ACCESS_KEY_ID"] .
                " AWS_SECRET_ACCESS_KEY=" . $_ENV["AWS_SECRET_ACCESS_KEY"] .
                " aws --endpoint-url=" . $_ENV["ENDPOINT_URL"] .
                " s3 cp " .$_ENV["ACTS_BUCKET"] .'/'.$act->getId().'/merged/'. $act->getFolderNumber() .'.pdf '. $this->projectDir.'/src/assets/documents/' . " --no-verify-ssl";
            shell_exec($exec_commandExport);
            exec($exec_command_output, $command_output, $return_output);

            if (!$return_output){
                $this->logger->info('merge file for act '.$act->getId().' downloaded to server');
            }else {
                $this->logger->error('merge file for act '.$act->getId().' did not get downloaded to server');

            }

            $result = stripslashes(base64_encode(file_get_contents($this->projectDir . '/src/assets/documents/' . $act->getFolderNumber() . '.pdf')));
        }

        if ($_ENV['DEV_MODE']=='false') {
            unlink($this->projectDir . '/src/assets/documents/' . $act->getFolderNumber() . '.pdf');
        }

        return new JsonResponse($result, 200);
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