<?php
/**
 * Created by PhpStorm.
 * User: Helmi
 * Date: 31/01/2020
 * Time: 11:13
 */

namespace App\Service;


use App\Entity\Act;
use App\Entity\Archive;
use App\Entity\Document;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Twig\Environment;

class DocumentService
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var \Knp\Snappy\Pdf
     */
    private $snappy;
    /**
     * @var Environment
     */
    private $templating;
    protected $projectDir;
    private $logger;

    public function __construct(EntityManagerInterface $entityManager, \Knp\Snappy\Pdf $snappy, Environment $templating, KernelInterface $kernel, LoggerInterface $appLogger)
    {
        $this->entityManager = $entityManager;
        $this->snappy = $snappy;
        $this->templating = $templating;
        $this->projectDir = $kernel->getProjectDir();
        $this->logger = $appLogger;

    }

    /**
     * @param $documentArray
     * @return mixed
     */
    public function newDocument($documentArray)
    {
        @chmod($this->projectDir."/src/assets/documents", 0777);

        $result = false;
        $base64 = $documentArray['file'];
        $name = str_replace(' ', '', $documentArray['name']);
        $actID = $documentArray['actId'];
        if (is_dir($this->projectDir.'/src/assets/documents/' . $actID) === false) {
            mkdir($this->projectDir.'/src/assets/documents/' . $actID);
        }

        $extension = $documentArray['extension'];
        $act = $this->entityManager->getRepository(Act::class)->find($actID);


        if ($_ENV['DEV_MODE']=='true') {
            $filePath = $this->projectDir.'/src/assets/documents/' . $actID . '/' . $name . '.pdf';
        }
        else {
            $filePath = $this->projectDir.'/src/assets/documents/' . $actID . '/' . $name . 'a.pdf';
        }

        $filePathOutput = $this->projectDir.'/src/assets/documents/' . $actID . '/' . $name . '.pdf';

        if ($extension == 'pdf') {
            $pdfDecoded = base64_decode($base64);
            file_put_contents($filePath, $pdfDecoded);

            if ($_ENV['DEV_MODE']=='true') {
                $rs = true;
            }
            else {
                $rs = $this->pdfToPdfA1b($filePath, $filePathOutput);
            }

            if ($rs == true) {

                if ($_ENV['DEV_MODE']=='false') {
                    $res = $this->pdfAValidator($filePathOutput);
                }
                else{
                    $res = true;
                }

                if ($res == true) {
                    if ($_ENV['DEV_MODE']=='true'){
                        $document = new Document();
                        $document->setName($name);
                        $document->setAct($act);
                        $document->setSize(filesize($filePathOutput));
                        $document->setStatus("CREATED");
                        $document->setType($extension);
                        $document->setConvertedType("pdfa2b");
                        $this->entityManager->persist($document);

                        $date = new \DateTime();
                        $timezone = new \DateTimeZone('Europe/Paris');
                        $date->setTimezone($timezone);

                        $archive = new Archive();
                        $archive->setAct($act);
                        $archive->setUser($act->getInitiator());
                        $archive->setActor($act->getInitiator()->getLastName() . ' ' . $act->getInitiator()->getName());
                        $archive->setAction('Action: Modification de l\'acte :Ajout d\'un document  ' . $document->getName());
                        $archive->setActionDate($date);
                        $this->entityManager->persist($archive);
                        $this->entityManager->flush();
                        $result = $this->entityManager->getRepository(Document::class)->findDocById($document->getId());

                        $this->logger->info('Adding new document(pdf) '.$result[0]['name'].' for act '.$act->getId().' by cnbId '.$act->getInitiator()->getCnbId());
                    }else{
                        $exec_commandExport = "export AWS_PROFILE=cloudian";
                        $exec_command = " AWS_ACCESS_KEY_ID=" . $_ENV["AWS_ACCESS_KEY_ID"] .
                            " AWS_SECRET_ACCESS_KEY=" . $_ENV["AWS_SECRET_ACCESS_KEY"] .
                            " aws --endpoint-url=" . $_ENV["ENDPOINT_URL"] .
                            " s3 cp " . $this->projectDir.'/src/assets/documents/' . $actID . '/' . $name . '.pdf ' . $_ENV["ACTS_BUCKET"] .'/'.$actID.$_ENV["DOCUMENT"]. " --no-verify-ssl";
                        $verify_exec_command = " AWS_ACCESS_KEY_ID=" . $_ENV["AWS_ACCESS_KEY_ID"] .
                            " AWS_SECRET_ACCESS_KEY=" . $_ENV["AWS_SECRET_ACCESS_KEY"] .
                            " aws --endpoint-url=" . $_ENV["ENDPOINT_URL"] .
                            " s3 ls " . $_ENV["ACTS_BUCKET"] .'/'.$actID.$_ENV["DOCUMENT"]. " --no-verify-ssl";
                        shell_exec($exec_commandExport);
                        exec($exec_command, $command_output, $return_val);
                        exec($verify_exec_command,$command_verify_output,$return_value);
                        $fileName = $name.".pdf";
                        $exist = false;
                        foreach ($command_verify_output as $item){
                            if (strpos($item,$fileName) != false){
                                $exist = true;
                            }
                        }
                        if ($return_val) {
                            $this->logger->error('Error while adding new document(pdf) '.$name.' for act '.$act->getId().' by cnbId '.$act->getInitiator()->getCnbId().' error : S3 unreachable');
                            return 'error when uploading new document to S3';
                        }
                        if ($exist){
                            $document = new Document();
                            $document->setName($name);
                            $document->setAct($act);
                            $document->setSize(filesize($filePathOutput));
                            $document->setStatus("CREATED");
                            $document->setType($extension);
                            $document->setConvertedType("pdfa2b");
                            $this->entityManager->persist($document);

                            $date = new \DateTime();
                            $timezone = new \DateTimeZone('Europe/Paris');
                            $date->setTimezone($timezone);

                            $archive = new Archive();
                            $archive->setAct($act);
                            $archive->setUser($act->getInitiator());
                            $archive->setActor($act->getInitiator()->getLastName() . ' ' . $act->getInitiator()->getName());
                            $archive->setAction('Action: Modification de l\'acte :Ajout d\'un document  ' . $document->getName());
                            $archive->setActionDate($date);
                            $this->entityManager->persist($archive);
                            $this->entityManager->flush();
                            $result = $this->entityManager->getRepository(Document::class)->findDocById($document->getId());
                            $this->logger->info('Adding new document(pdf) '.$result[0]['name'].' for act '.$act->getId().' by cnbId '.$act->getInitiator()->getCnbId());
                            unlink($this->projectDir."/src/assets/documents/" . $actID . '/' . $name . ".pdf");
                        }else{
                            $this->logger->error('Error while adding new document(pdf) '.$name.' for act '.$act->getId().' by cnbId '.$act->getInitiator()->getCnbId().' error : upload to s3 bucket failed');
                            return  'error while upload document to s3';
                        }
                    }
                } else {
                    $this->logger->error('Error while adding new document(pdf) '.$name.' for act '.$act->getId().' by cnbId '.$act->getInitiator()->getCnbId().' error : not a valid pdfa2b');
                    return 'not a valid pdfa2b';
                }
            }
        }
        if ($extension == 'jpg' or $extension == 'png' or $extension == 'jpeg' or $extension == 'bmp') {
            $imageData = base64_decode($base64);
            $source = imagecreatefromstring($imageData);
            $rotate = imagerotate($source, 0, 0);
            $image = imagejpeg($rotate, $this->projectDir.'/src/assets/documents/' . $actID . '/' . $name . '.jpg', 100);
            imagedestroy($source);
            if ($_ENV['DEV_MODE']=='true') {
                $exec_command = "GSWIN64C.EXE  -dFIXEDMEDIA -dNOSAFER -sDEVICE=pdfwrite -o " . $filePath . " viewjpeg.ps -c (".$this->projectDir."/src/assets/documents/" . $actID . '/' . $name . ".jpg)  viewJPEG showpage";
            } else {
                $exec_command = "gs -dFIXEDMEDIA -dNOSAFER -sDEVICE=pdfwrite -o " . $filePath . " viewjpeg.ps -c '(".$this->projectDir."/src/assets/documents/" . $actID . '/' . $name . ".jpg)  viewJPEG' ";
            }

            exec($exec_command, $command_output, $return_val);
            if (!$return_val) {
                unlink($this->projectDir."/src/assets/documents/" . $actID . '/' . $name . ".jpg");
                $rs = $this->pdfToPdfA1b($filePath, $filePathOutput);
                if ($rs == true) {
                    $res = $this->pdfAValidator($filePathOutput);
                    if ($res == true) {
                        $document = new Document();
                        $document->setName($name);
                        $document->setAct($act);
                        $document->setSize(filesize($filePathOutput));
                        $document->setStatus("CREATED");
                        $document->setType($extension);
                        $document->setConvertedType("pdfa2b");
                        $this->entityManager->persist($document);

                        $date = new \DateTime();
                        $timezone = new \DateTimeZone('Europe/Paris');
                        $date->setTimezone($timezone);

                        $archive = new Archive();
                        $archive->setAct($act);
                        $archive->setUser($act->getInitiator());
                        $archive->setActor($act->getInitiator()->getLastName() . ' ' . $act->getInitiator()->getName());
                        $archive->setAction('Action: Modification de l\'acte : Ajout d\'un document ' . $document->getName());
                        $archive->setActionDate($date);
                        $this->entityManager->persist($archive);

                        $this->entityManager->flush();
                        $result = $this->entityManager->getRepository(Document::class)->findDocById($document->getId());
                        $this->logger->info('Adding new document(image) '.$result[0]['name'].' for act '.$act->getId().' by cnbId '.$act->getInitiator()->getCnbId());
                        if ($_ENV['DEV_MODE']=='false'){
                            $exec_commandExport = "export AWS_PROFILE=cloudian";
                            $exec_command_upload_image = " AWS_ACCESS_KEY_ID=" . $_ENV["AWS_ACCESS_KEY_ID"] .
                                " AWS_SECRET_ACCESS_KEY=" . $_ENV["AWS_SECRET_ACCESS_KEY"] .
                                " aws --endpoint-url=" . $_ENV["ENDPOINT_URL"] .
                                " s3 cp " . $this->projectDir.'/src/assets/documents/' . $actID . '/' . $name . '.pdf ' . $_ENV["ACTS_BUCKET"] .'/'.$actID.$_ENV["DOCUMENT"]. " --no-verify-ssl";
                            shell_exec($exec_commandExport);
                            exec($exec_command_upload_image, $command_output_upload_image, $return_val_upload_image);
                            if (!$return_val_upload_image){
                                unlink($this->projectDir."/src/assets/documents/" . $actID . '/' . $name . ".pdf");
                            }
                        }
                    } else {
                        $this->logger->error('Error while adding new document(image) '.$name.' for act '.$act->getId().' by cnbId '.$act->getInitiator()->getCnbId().' error : not a valid pdfa2b');

                        return 'not a valid pda2b';
                    }
                }
            }
        }
        return $result;

    }

    /**
     * @param $file
     * @return bool
     */
    private function isPdf($file): bool
    {
        $file_content = file_get_contents($file);

        if (preg_match("/^%PDF-[0-1]\.[0-9]+/", $file_content)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param $filePath
     * @param $filePathOutput
     * @return bool
     */
    private function pdfToPdfA1b($filePath, $filePathOutput): bool
    {
        $result = false;
        $validator = $this->isPdf($filePath);
        if ($validator == true) {
            if ($_ENV['DEV_MODE']=='true') {
                $exec_command = "GSWIN64C.EXE -dPDFA=2  -sProcessColorModel=DeviceRGB -sDEVICE=pdfwrite -dNOPAUSE -dNOSAFER -dPDFACompatibilityPolicy=1 -dBATCH -dQUIET  -sOutputFile=" . $filePathOutput . " " . $filePath." -f ".$this->projectDir."/public/PDFA/PDFA_def.ps";
            } else {
                $exec_command = 'gs -dPDFA=2  -sProcessColorModel=DeviceRGB -sDEVICE=pdfwrite -dNOPAUSE -dNOSAFER -dPDFACompatibilityPolicy=1 -dBATCH -sOutputFile="' . $filePathOutput . '" "' . $filePath . '" -f "'.$this->projectDir.'/public/PDFA/PDFA_def.ps"';

            }
            exec($exec_command, $command_output, $return_val);
            if (!$return_val) {
                $result = true;
                unlink($filePath);
            }
        }

        return $result;
    }

    /**
     * @param $filePathOutput
     * @return bool
     */
    private function pdfAValidator($filePathOutput): bool
    {
        $exists=false;
        $result = false;
        if ($_ENV['DEV_MODE']=='true') {
            return true;
        } else {
            if (file_exists($this->stripAccents($filePathOutput))) {
                $exists = true;
            }
            copy($filePathOutput, $this->stripAccents($filePathOutput));
            $exec_command = $this->projectDir . "/public/verapdf/corpus/veraPDF-corpus-staging/PDF_A-2b/6.5\ Action/6.5.1\ General/veraPDF\ test\ suite\ 6-5-1-t0 veraPDF test suite " . "'" . $this->stripAccents($filePathOutput) . "'";
            exec($exec_command, $command_output, $return_val);
            dump($exec_command , $command_output , $return_val);
            if (!$return_val) {
                $line = $command_output[12];
                $success = strpos($line, 'true', false) && strpos($line, 'isCompliant', false);
                if ($success != false) {
                    $result = true;
                    if ($exists == false) {
                        unlink($this->stripAccents($filePathOutput));
                    }
                }else{
                    $result = false;
                    unlink($this->stripAccents($filePathOutput));
                    unlink($filePathOutput);
                }

            }

        }



        return $result;
    }

    /**
     * @param $documentArray
     * @return bool
     */
    public function deleteDocument($documentArray): bool
    {
        $name = str_replace(' ', '', $documentArray['name']);
        $actId = $documentArray['actId'];
        $filePath = $this->projectDir . "/src/assets/documents/" . $actId . '/' . $name . ".pdf";
        $document = $this->entityManager->getRepository(Document::class)->findByDocumentByNameAndActId($name, $actId);
        $this->entityManager->remove($document);
        $archive = new Archive();
        $act = $this->entityManager->getRepository(Act::class)->find($actId);
        $this->logger->info('Deleting document '.$document->getName().' for act '.$document->getAct()->getFolderNumber().' by cnbId '.$document->getAct()->getInitiator()->getCnbId());
        $archive->setAct($act);
        $archive->setUser($act->getInitiator());
        $archive->setActor($act->getInitiator()->getLastName().' '.$act->getInitiator()->getName() );

        $archive->setAction('Action: Modification de l\'acte : Suppression d\'un document '.$document->getName());
        $archive->setActionDate(new \DateTime());
        $this->entityManager->persist($archive);
        $this->entityManager->flush();
        if ($_ENV['DEV_MODE']=='true'){
            unlink($filePath);
        }else{
            $exec_commandExport = "export AWS_PROFILE=cloudian";
            $exec_command = " AWS_ACCESS_KEY_ID=" . $_ENV["AWS_ACCESS_KEY_ID"] .
                " AWS_SECRET_ACCESS_KEY=" . $_ENV["AWS_SECRET_ACCESS_KEY"] .
                " aws --endpoint-url=" . $_ENV["ENDPOINT_URL"] .
                " s3 rm " . $_ENV["ACTS_BUCKET"] .'/'.$actId.$_ENV["DOCUMENT"].$name . '.pdf'. " --no-verify-ssl";
            shell_exec($exec_commandExport);
            exec($exec_command, $command_output, $return_val);
            if (file_exists($this->projectDir."/src/assets/documents/" . $actId . '/' . $name . ".pdf")){
                unlink($this->projectDir."/src/assets/documents/" . $actId . '/' . $name . ".pdf");
            }
        }
        return true;
    }

    /**
     * @param $id
     * @return array
     */
    public function getDocumentsByAct($id): array
    {
        $documents = $this->entityManager->getRepository(Document::class)->findDocumentsByAct($id);

        return $documents;
    }

    /**
     * @param $documentArray
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function mergeDocuments($documentArray): string
    {
        $result = 'error while merging pdf';
        $name = '';
        $actId = (int)array_shift($documentArray);

        $act = $this->entityManager->getRepository(Act::class)->find($actId);
        $documents = $this->entityManager->getRepository(Document::class)->findBy(array('act' => $actId), array('position' => 'ASC'));
        $outputPath = $this->projectDir.'/src/assets/documents/' . $act->getFolderNumber() . '.pdf';
        if ($_ENV['DEV_MODE']=='true') {
            foreach ($documents as $doc) {
                $name = $name ." ".$this->projectDir."/src/assets/documents/" . $actId . "/" . str_replace(' ', '', $doc->getName()) . ".pdf";
            }
        } else {
            foreach ($documents as $doc) {
                $exec_commandExport = "export AWS_PROFILE=cloudian";
                $exec_command = " AWS_ACCESS_KEY_ID=" . $_ENV["AWS_ACCESS_KEY_ID"] .
                    " AWS_SECRET_ACCESS_KEY=" . $_ENV["AWS_SECRET_ACCESS_KEY"] .
                    " aws --endpoint-url=" . $_ENV["ENDPOINT_URL"] .
                    " s3 cp ". $_ENV["ACTS_BUCKET"] .'/'.$actId.$_ENV["DOCUMENT"].$doc->getName().'.pdf '. $this->projectDir.'/src/assets/documents/' . $actId . '/ ' . " --no-verify-ssl";
                shell_exec($exec_commandExport);
                exec($exec_command, $command_output, $return_val);
                $name = $name .' "'.$this->projectDir.'/src/assets/documents/' . $actId . '/' . str_replace(' ', '', $doc->getName()) . '.pdf" ';
            }
        }

        if ($_ENV['DEV_MODE']=='true') {
            $exec_command = "GSWIN64C.EXE -dPDFA=2  -sProcessColorModel=DeviceRGB -sDEVICE=pdfwrite -dNOPAUSE -dNOSAFER -dPDFACompatibilityPolicy=1 -dBATCH -dQUIET  -sOutputFile=" . $outputPath . " " . $name." -f ".$this->projectDir."/public/PDFA/PDFA_def.ps ";
        } else {
            $exec_command = 'gs -dPDFA=2  -sProcessColorModel=DeviceRGB -sDEVICE=pdfwrite -dNOPAUSE -dNOSAFER -dPDFACompatibilityPolicy=1 -dBATCH -dQUIET  -sOutputFile="' . $outputPath . '" ' . $name.' -f "'.$this->projectDir.'/public/PDFA/PDFA_def.ps"';

        }
        exec($exec_command, $command_output, $return_val);
        if (!$return_val) {
            $this->logger->info('Merging documents for act '.$act->getFolderNumber().' for cnbId '.$act->getInitiator()->getCnbId());
            $result = $this->FrontPageMergeDocuments($actId);
            if ($_ENV['DEV_MODE']=='true'){
                foreach ($documents as $document){
                    unlink($this->projectDir.'/src/assets/documents/'.$act->getId().'/'.$document->getName().'.pdf');
                }
            }

        }else{
            $this->logger->error('Error while merging documents for act '.$act->getFolderNumber().' for cnbId '.$act->getInitiator()->getCnbId());
        }

        return $result;
    }

    /**
     * @param $documentArray
     * @return string
     */
    public function mergeDocumentsSigned($documentArray): string
    {
        $name = '';
        $actId = (int)array_shift($documentArray);
        $base64 = $documentArray[0];
        $base64 = str_replace('data:application/pdf;base64,', '', $base64);
        $pdfDecoded = base64_decode($base64);
        file_put_contents($this->projectDir.'/src/assets/documents/signature.pdf', $pdfDecoded);

        $act = $this->entityManager->getRepository(Act::class)->find($actId);
        $outputPath = $this->projectDir.'/src/assets/documents/' . $act->getFolderNumber() . 'signed.pdf';
        $exec_commandExport = "export AWS_PROFILE=cloudian";
        $exec_command = " AWS_ACCESS_KEY_ID=" . $_ENV["AWS_ACCESS_KEY_ID"] .
            " AWS_SECRET_ACCESS_KEY=" . $_ENV["AWS_SECRET_ACCESS_KEY"] .
            " aws --endpoint-url=" . $_ENV["ENDPOINT_URL"] .
            " s3 cp ". $_ENV["ACTS_BUCKET"] .'/merged/'.$actId.'/'.$act->getFolderNumber().'.pdf '. $this->projectDir.'/src/assets/documents/' . " --no-verify-ssl";
        shell_exec($exec_commandExport);
        exec($exec_command, $command_output, $return_val);
        if (!$return_val){
            $this->logger->info('merge signed file for act '.$act->getId().' downloaded to server');
        }else{
            $this->logger->error('merge file for act '.$act->getId().' did not get downloaded to server');
        }
        $name = $name .' '.$this->projectDir.'/src/assets/documents/' . $act->getFolderNumber() . '.pdf';
        $signed = $this->projectDir.'/src/assets/documents/signature.pdf';

        if ($_ENV['DEV_MODE']=='true') {
            $exec_command = "GSWIN64C.EXE -dPDFA=2  -sProcessColorModel=DeviceRGB -sDEVICE=pdfwrite -dNOPAUSE -dNOSAFER -dPDFACompatibilityPolicy=1 -dBATCH -dQUIET    -sOutputFile=" . $outputPath . "  " . $name . ' ' . $signed." -f ".$this->projectDir."/public/PDFA/PDFA_def.ps";
        } else {
            $exec_command = "gs -dPDFA=2  -sProcessColorModel=DeviceRGB -sDEVICE=pdfwrite -dNOPAUSE -dNOSAFER -dPDFACompatibilityPolicy=1 -dBATCH -dQUIET   -sOutputFile='" . $outputPath . "' '" . $name . "' '" . $signed . "' -f ".$this->projectDir."/public/PDFA/PDFA_def.ps";

        }
        exec($exec_command, $command_output, $return_val);
        if (!$return_val) {
            $result = stripslashes(base64_encode(file_get_contents($outputPath)));
            unlink($outputPath);
            unlink($this->projectDir.'/src/assets/documents/signature.pdf');
        } else {
            $result = 'error while merging pdf';
        }
        return $result;
    }

    /**
     * @param $documentArray
     * @return bool
     */
    public function positionDocument($documentArray)
    {
        foreach ($documentArray as $doc) {
            $document = $this->entityManager->getRepository(Document::class)->find($doc['id']);
            $document->setPosition($doc['position']);
            $this->logger->info('Changing Document '.$document->getName().' position by cnbId '.$document->getAct()->getInitiator()->getCnbId().' to '.$doc['position'].' in act '.$document->getAct()->getFolderNumber());
            $this->entityManager->persist($document);
            $this->entityManager->flush();
        }
        return true;

    }

    /**
     * @param $actId
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function FrontPageMergeDocuments($actId): string
    {

        $act = $this->entityManager->getRepository(Act::class)->find($actId);

        $outputPath = $this->projectDir.'/src/assets/documents/' . $act->getFolderNumber() . '.pdf';
        $outputPathTest = $this->projectDir.'/src/assets/documents/' . $act->getFolderNumber() . 'ForSigning.pdf';
        $name = $this->projectDir.'/src/assets/documents/' . $this->pdfFrontPage($act) . '.pdf';
        $documents = $this->entityManager->getRepository(Document::class)->findBy(array('act' => $actId), array('position' => 'ASC'));

        if ($_ENV['DEV_MODE']=='true') {
            $exec_command = "GSWIN64C.EXE -dPDFA=2  -sProcessColorModel=DeviceRGB -sDEVICE=pdfwrite -dNOPAUSE -dNOSAFER -dPDFACompatibilityPolicy=1 -dBATCH -dQUIET   -sOutputFile=" . $outputPathTest . " " . $outputPath . " " . $name." -f ".$this->projectDir."/public/PDFA/PDFA_def.ps";
        } else {
            $exec_command = "gs -dPDFA=2  -sProcessColorModel=DeviceRGB -sDEVICE=pdfwrite -dNOPAUSE -dNOSAFER -dPDFACompatibilityPolicy=1 -dBATCH -dQUIET  -sOutputFile=" . $outputPathTest . " " . $outputPath . " " . $name." -f ".$this->projectDir."/public/PDFA/PDFA_def.ps ";

        }
        exec($exec_command, $command_output, $return_val);
        if (!$return_val) {
            if ($_ENV['DEV_MODE']=='true'){
                $result = 'blank page added';
            }else{
                $this->logger->info('Merging signature page with documents for act '.$act->getFolderNumber().' for cnbId '.$act->getInitiator()->getCnbId());
                $exec_commandExport = "export AWS_PROFILE=cloudian";
                $exec_command_output = " AWS_ACCESS_KEY_ID=" . $_ENV["AWS_ACCESS_KEY_ID"] .
                    " AWS_SECRET_ACCESS_KEY=" . $_ENV["AWS_SECRET_ACCESS_KEY"] .
                    " aws --endpoint-url=" . $_ENV["ENDPOINT_URL"] .
                    " s3 cp " . $this->projectDir.'/src/assets/documents/'  . $act->getFolderNumber() . '.pdf ' . $_ENV["ACTS_BUCKET"] .'/'.$actId.'/merged/'. " --no-verify-ssl";
                $exec_command_Path_test = " AWS_ACCESS_KEY_ID=" . $_ENV["AWS_ACCESS_KEY_ID"] .
                    " AWS_SECRET_ACCESS_KEY=" . $_ENV["AWS_SECRET_ACCESS_KEY"] .
                    " aws --endpoint-url=" . $_ENV["ENDPOINT_URL"] .
                    " s3 cp " . $this->projectDir.'/src/assets/documents/'  . $act->getFolderNumber() . 'ForSigning.pdf ' . $_ENV["ACTS_BUCKET"] .'/'.$actId.'/merged/'. " --no-verify-ssl";
                $exec_command_Path_front_page = " AWS_ACCESS_KEY_ID=" . $_ENV["AWS_ACCESS_KEY_ID"] .
                    " AWS_SECRET_ACCESS_KEY=" . $_ENV["AWS_SECRET_ACCESS_KEY"] .
                    " aws --endpoint-url=" . $_ENV["ENDPOINT_URL"] .
                    " s3 cp " . $this->projectDir.'/src/assets/documents/'  . $this->pdfFrontPage($act) . '.pdf ' . $_ENV["ACTS_BUCKET"] .'/'.$actId.'/merged'. " --no-verify-ssl";
                shell_exec($exec_commandExport);
                exec($exec_command_output, $command_output, $return_output);
                exec($exec_command_Path_test, $command_output, $return_output_path);
                exec($exec_command_Path_front_page, $command_output, $return_output_front_page);
                if (!$return_output and !$return_output_path and !$return_output_front_page){
                    unlink($outputPath);
                    unlink($outputPathTest);
                    unlink($name);
                    $result = 'blank page added';
                }else{
                    $this->logger->error('Error while uploading merged signature page with documents for act '.$act->getFolderNumber().' for cnbId '.$act->getInitiator()->getCnbId());
                    $result = 'error while merging pdsdfsdff';
                }

            }

        } else {
            $this->logger->error('Error while merging signature page with documents for act '.$act->getFolderNumber().' for cnbId '.$act->getInitiator()->getCnbId());
            $result = 'error while merging pdf';
        }
        return $result;
    }

    /**
     * @param $act
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function pdfFrontPage($act)
    {
        $fileName = 'frontPage' . $act->getFolderNumber();
        $signatories = $this->entityManager->getRepository(User::class)->findSignatoryByAct($act,$act->getInitiator());
        if (file_exists($this->projectDir.'/src/assets/documents/' . $fileName . '.pdf')) {
            unlink($this->projectDir."/src/assets/documents/" . $fileName . ".pdf");
        }
        $pdftext = file_get_contents($this->projectDir.'/src/assets/documents/' . $act->getFolderNumber() . '.pdf');
        $num = preg_match_all("/\/Page\W/", $pdftext, $dummy);
        $num = $num + (intdiv(sizeof($signatories), 7)) + 1;
        $user = $this->entityManager->getRepository(User::class)->find($act->getInitiator()->getId());
        if ($_ENV['DEV_MODE']=='true'){
            $user->setIpaddress(null);
        }else{
            $user->setIpaddress($_SERVER['HTTP_X_FORWARDED_FOR']);
        }
        $this->entityManager->persist($user);
        $this->entityManager->flush();
        $html = $this->templating->render('act/frontPage.html.twig', array(
            'currentAct' => 1,
            'listSignataire' => $signatories,
            'base_dir' => $this->projectDir . '\public',
            'documents' => $act->getName().'_'.$act->getFolderNumber(),
            'user' => $user,
            'email'=>$user->getEmailApp() ? $user->getEmailApp() : $user->getEmail(),
            'nbPages' => $num,
            'actFolderNumber'=>$act->getFolderNumber()
        ));
        $this->snappy->generateFromHtml($html, $this->projectDir.'/src/assets/documents/' . $fileName . '.pdf');
        $this->logger->info('Creating signature page for act '.$act->getFolderNumber().' for cnbId '.$act->getInitiator()->getCnbId());

        return $fileName;
    }
    private function stripAccents($stripAccents){
        $array=['/à/','/á/','/â/','/ã/','/ä/','/ç/','/è/','/é/','/ê/','/ë/','/ì/','/í/','/î/','/ï/','/ñ/','/ò/','/ó/','/ô/','/õ/','/ö/','/ù/','/ú/','/û/','/ü/','/ý/','/ÿ/','/À/'
            ,'/Á/','/Â/','/Ã/','/Ä/','/Ç/','/È/','/É/','/Ê/','/Ë/','/Ì/','/Í/','/Î/', '/Ï/','/Ñ/','/Ò/','/Ó/','/Ô/','/Õ/','/Ö/','/Ù/','/Ú/','/Û/','/Ü/','/Ý/',"/'/",'/"/'];
        return preg_replace($array,'',$stripAccents);
    }


}