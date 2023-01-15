<?php


namespace App\Service;


use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use PhpParser\Error;
use Swift_Mailer;
use Symfony\Component\Ldap\Exception\LdapException;
use Symfony\Component\Ldap\Ldap;

class LdapService
{
    private $entityManager;
    private $mailer;



    public function __construct(EntityManagerInterface $entityManager , Swift_Mailer $mailer)
    {
        $this->entityManager = $entityManager;
        $this->mailer = $mailer;
    }

    public function lawyerDirectory()
    {
//        $dn='cn=u_app_preprod_02,ou=users,o=administration,c=fr';
//        $password = 'uhLxQmAyqyP6ZkVoK7tz';
//        $scope = 'l=0190,c=fr';
        $scope = $_ENV['SCOPE_PP'];
        $scopes = explode("|",$scope);
        $scopeLength = count($scopes);
        $i = 0;
        while ($i<$scopeLength) {
            $this->loopResults1($scopes , $i);
            $i++;
        }
    }



    function loopResults1($scopes , $i ){
        $ldap = Ldap::create('ext_ldap', ['connection_string' => $_ENV['HOST_PREPROD']]);
        $ldap->bind($_ENV['DN_PREPREPROD'],$_ENV['PASSWORD_PP']);
        $query = $ldap->query($scopes[$i], '(&(uid=*))');
        //ldap query for beta testing-> only on prod environment
        //$query = $ldap->query('(|(uid=098521)(uid=098521)(uid=301591)(uid=050175)(uid=058351)(uid=056518)(uid=105680)(uid=044078)(uid=095403)(uid=029465)(uid=029919)(uid=088720)(uid=052006)(uid=093195)(uid=029172)(uid=071159)(uid=069525)(uid=058691)(uid=045198)(uid=083304)(uid=027581)(uid=064160)(uid=057133)(uid=018113)(uid=088237)(uid=086070)(uid=044077)(uid=048381)(uid=029465)(uid=088270)(uid=043781)(uid=029172)(uid=301823)(uid=069525)(uid=301825)(uid=037248)(uid=072958)(uid=999012)(uid=999002)(uid=999013)(uid=999003)(uid=999001)(uid=099999)(uid=999999)(uid=999014)(uid=124588)(uid=999011)(uid=999010))');
        try{
            $results = $query->execute()->toArray();
            $this->fetchResults($results);
        }catch (LdapException $exception){
            if ($exception){
                $scopes[$i+1];
            }
        }
        return 'done';
    }
    function loopResults2(){
        $ldap = Ldap::create('ext_ldap', ['connection_string' => $_ENV['HOST_PREPROD']]);
        $ldap->bind($_ENV['DN_PREPREPROD'],$_ENV['PASSWORD_PP']);
        $query = $ldap->query('l=0191,c=fr', '(&(uid=*))');
        //ldap query for beta testing-> only on prod environment
        //$query = $ldap->query('(|(uid=098521)(uid=098521)(uid=301591)(uid=050175)(uid=058351)(uid=056518)(uid=105680)(uid=044078)(uid=095403)(uid=029465)(uid=029919)(uid=088720)(uid=052006)(uid=093195)(uid=029172)(uid=071159)(uid=069525)(uid=058691)(uid=045198)(uid=083304)(uid=027581)(uid=064160)(uid=057133)(uid=018113)(uid=088237)(uid=086070)(uid=044077)(uid=048381)(uid=029465)(uid=088270)(uid=043781)(uid=029172)(uid=301823)(uid=069525)(uid=301825)(uid=037248)(uid=072958)(uid=999012)(uid=999002)(uid=999013)(uid=999003)(uid=999001)(uid=099999)(uid=999999)(uid=999014)(uid=124588)(uid=999011)(uid=999010))');
        $results = $query->execute()->toArray();
        $this->fetchResults($results);
        return 'done';
    }

    function fetchResults($results){
        foreach ($results as $result) {
            $cnbId = $result->getAttribute('avCnbfCode')[0];
            $prenom = $result->getAttribute('avNom')[0];
            $nom = $result->getAttribute('avPrenom')[0];
            $mobile = $result->getAttribute('avMobile')[0];
            $mail2 = $result->getAttribute('avAdresseMsg2')[0];
            $lawyer = $this->entityManager->getRepository(User::class)->findOneBy(['cnbId' => $cnbId]);
            if ($lawyer) {
                if ($lawyer->getName() != $prenom) {
                    $lawyer->setName($prenom);
                }
                if ($lawyer->getLastName() != $nom) {
                    $lawyer->setLastName($nom);
                }
                if ($lawyer->getUserName() != $prenom . ' ' . $nom) {
                    $lawyer->setUserName($prenom . ' ' . $nom);
                }
                    if($mobile!=null){
                        if(substr( $mobile, 0, 1 )=="+"){
                            $pos = strpos($mobile, ' ');
                            $codeCountry=strstr($mobile, ' ', true);
                            if ($pos !== false) {
                                $mobile = str_replace(' ','',substr($mobile, $pos));
                            }
                            $lawyer->setCodeCountry($codeCountry);
                            $lawyer->setPhoneNumber($mobile);
                        }elseif (substr( $mobile, 0, 2 )=="00"){
                            $pos = strpos($mobile, ' ');
                            $mobile = str_replace(' ','',$mobile);
                            $codeCountry=strstr($mobile, ' ', true);
                            $mobile = preg_replace('/' . '00' . '/', '', $mobile, 1);
                            if ($pos !== false) {
                                $mobile = str_replace(' ','',substr($mobile, $pos));
                            }
                            $lawyer->setCodeCountry($codeCountry);
                            $lawyer->setPhoneNumber($mobile);
                        }elseif (substr( $mobile, 0, 1 )=="0") {
                            $mobile = str_replace(' ', '', $mobile);
                            $mobile = preg_replace('/' . '0' . '/', '', $mobile, 1);
                            $lawyer->setCodeCountry("+33");
                            $lawyer->setPhoneNumber($mobile);
                        }else{
                        }
                    }
                if ($mail2) {
                    $lawyer->setEmail($mail2);
                }
            }
            if (!$lawyer) {
                $user = new User();
                $user->setCnbId($cnbId);
                $user->setName($prenom);
                $user->setLastName($nom);
                $user->setUsername($prenom . ' ' . $nom);
                if($mobile!=null){
                        if(substr( $mobile, 0, 1 )=="+"){
                            $pos = strpos($mobile, ' ');
                            $codeCountry=strstr($mobile, ' ', true);
                            if ($pos !== false) {
                                $mobile = str_replace(' ','',substr($mobile, $pos));
                            }
                            $user->setCodeCountry($codeCountry);
                            $user->setPhoneNumber($mobile);
                        }elseif (substr( $mobile, 0, 2 )=="00"){
                            $pos = strpos($mobile, ' ');
                            $mobile = str_replace(' ','',$mobile);
                            $codeCountry=strstr($mobile, ' ', true);
                            $mobile = preg_replace('/' . '00' . '/', '', $mobile, 1);
                            if ($pos !== false) {
                                $mobile = str_replace(' ','',substr($mobile, $pos));
                            }
                            $user->setCodeCountry($codeCountry);
                            $user->setPhoneNumber($mobile);
                        }elseif (substr( $mobile, 0, 1 )=="0") {
                            $mobile = str_replace(' ', '', $mobile);
                            $mobile = preg_replace('/' . '0' . '/', '', $mobile, 1);
                            $user->setCodeCountry("+33");
                            $user->setPhoneNumber($mobile);
                        }else{
                            $user->setCodeCountry("+33");
                            $user->setPhoneNumber($mobile);
                        }
                }
                if ($mail2) {
                    $lawyer->setEmail($mail2);
                } else {
                    $user->setEmail($cnbId . 'cnb@cnb.fr');
                }
                $user->setRoles(array('ROLE_USER'));
                $user->setPassword('mdpavocat');
                $user->setEnabled(true);
                $this->entityManager->persist($user);
                $this->entityManager->flush();
                dump('done');
            }
        }
    }

    public function getAllLawyers()
    {
        $ldap = Ldap::create('ext_ldap', ['connection_string' => $_ENV['HOST_PREPROD']]);
        $ldap->bind($_ENV['DN_PROD'],$_ENV['PASSWORD_PROD']);
        $scopes = [
            'l=0001,c=fr',
            'l=0002,c=fr',
            'l=0003,c=fr',
            'l=0004,c=fr',
            'l=0005,c=fr',
            'l=0006,c=fr',
            'l=0007,c=fr',
            'l=0008,c=fr',
            'l=0009,c=fr',
            'l=0010,c=fr',
            'l=0011,c=fr',
            'l=0012,c=fr',
            'l=0013,c=fr',
            'l=0014,c=fr',
            'l=0015,c=fr',
            'l=0016,c=fr',
            'l=0017,c=fr',
            'l=0018,c=fr',
            'l=0019,c=fr',
            'l=0020,c=fr',
            'l=0021,c=fr',
            'l=0022,c=fr',
            'l=0023,c=fr',
            'l=0024,c=fr',
            'l=0025,c=fr',
            'l=0026,c=fr',
            'l=0027,c=fr',
            'l=0028,c=fr',
            'l=0029,c=fr',
            'l=0030,c=fr',
            'l=0031,c=fr',
            'l=0032,c=fr',
            'l=0033,c=fr',
            'l=0034,c=fr',
            'l=0035,c=fr',
            'l=0036,c=fr',
            'l=0037,c=fr',
            'l=0038,c=fr',
            'l=0039,c=fr',
            'l=0040,c=fr',
            'l=0041,c=fr',
            'l=0042,c=fr',
            'l=0043,c=fr',
            'l=0044,c=fr',
            'l=0045,c=fr',
            'l=0046,c=fr',
            'l=0047,c=fr',
            'l=0048,c=fr',
            'l=0049,c=fr',
            'l=0050,c=fr',
            'l=0051,c=fr',
            'l=0052,c=fr',
            'l=0053,c=fr',
            'l=0054,c=fr',
            'l=0055,c=fr',
            'l=0056,c=fr',
            'l=0057,c=fr',
            'l=0058,c=fr',
            'l=0059,c=fr',
            'l=0060,c=fr',
            'l=0061,c=fr',
            'l=0062,c=fr',
            'l=0063,c=fr',
            'l=0064,c=fr',
            'l=0065,c=fr',
            'l=0066,c=fr',
            'l=0067,c=fr',
            'l=0068,c=fr',
            'l=0069,c=fr',
            'l=0070,c=fr',
            'l=0071,c=fr',
            'l=0072,c=fr',
            'l=0073,c=fr',
            'l=0074,c=fr',
            'l=0075,c=fr',
            'l=0076,c=fr',
            'l=0077,c=fr',
            'l=0078,c=fr',
            'l=0079,c=fr',
            'l=0080,c=fr',
            'l=0081,c=fr',
            'l=0082,c=fr',
            'l=0083,c=fr',
            'l=0084,c=fr',
            'l=0085,c=fr',
            'l=0086,c=fr',
            'l=0087,c=fr',
            'l=0088,c=fr',
            'l=0089,c=fr',
            'l=0090,c=fr',
            'l=0091,c=fr',
            'l=0092,c=fr',
            'l=0093,c=fr',
            'l=0094,c=fr',
            'l=0095,c=fr',
            'l=0096,c=fr',
            'l=0097,c=fr',
            'l=0098,c=fr',
            'l=0099,c=fr',
            'l=0100,c=fr',
            'l=0101,c=fr',
            'l=0102,c=fr',
            'l=0103,c=fr',
            'l=0104,c=fr',
            'l=0105,c=fr',
            'l=0106,c=fr',
            'l=0107,c=fr',
            'l=0108,c=fr',
            'l=0109,c=fr',
            'l=0110,c=fr',
            'l=0111,c=fr',
            'l=0112,c=fr',
            'l=0113,c=fr',
            'l=0114,c=fr',
            'l=0115,c=fr',
            'l=0116,c=fr',
            'l=0117,c=fr',
            'l=0118,c=fr',
            'l=0119,c=fr',
            'l=0120,c=fr',
            'l=0121,c=fr',
            'l=0122,c=fr',
            'l=0123,c=fr',
            'l=0124,c=fr',
            'l=0125,c=fr',
            'l=0126,c=fr',
            'l=0127,c=fr',
            'l=0128,c=fr',
            'l=0129,c=fr',
            'l=0130,c=fr',
            'l=0131,c=fr',
            'l=0132,c=fr',
            'l=0133,c=fr',
            'l=0134,c=fr',
            'l=0135,c=fr',
            'l=0136,c=fr',
            'l=0137,c=fr',
            'l=0138,c=fr',
            'l=0139,c=fr',
            'l=0140,c=fr',
            'l=0141,c=fr',
            'l=0142,c=fr',
            'l=0143,c=fr',
            'l=0144,c=fr',
            'l=0145,c=fr',
            'l=0146,c=fr',
            'l=0147,c=fr',
            'l=0148,c=fr',
            'l=0149,c=fr',
            'l=0150,c=fr',
            'l=0151,c=fr',
            'l=0152,c=fr',
            'l=0153,c=fr',
            'l=0154,c=fr',
            'l=0155,c=fr',
            'l=0156,c=fr',
            'l=0157,c=fr',
            'l=0158,c=fr',
            'l=0159,c=fr',
            'l=0160,c=fr',
            'l=0161,c=fr',
            'l=0162,c=fr',
            'l=0163,c=fr',
            'l=0164,c=fr',
            'l=0165,c=fr',
            'l=0166,c=fr',
            'l=0167,c=fr',
            'l=0168,c=fr',
            'l=0169,c=fr',
            'l=0170,c=fr',
            'l=0171,c=fr',
            'l=0172,c=fr',
            'l=0173,c=fr',
            'l=0174,c=fr',
            'l=0175,c=fr',
            'l=0176,c=fr',
            'l=0177,c=fr',
            'l=0178,c=fr',
            'l=0179,c=fr',
            'l=0180,c=fr',
            'l=0181,c=fr',
            'l=0182,c=fr',
            'l=0183,c=fr',
            'l=0190,c=fr',
            'l=0191,c=fr',
        ];
        $scopeLength = count($scopes);
        $i = 0;
        while ($i<$scopeLength){
            $query = $ldap->query($scopes[$i],'(&(uid=*))');
            $results = $query->execute()->toArray();
            foreach ($results as $result){
                $cnbId = $result->getAttribute('avCnbfCode')[0];
                $nom = $result->getAttribute('avNom')[0];
                $prenom = $result->getAttribute('avPrenom')[0];
                $mobile = $result->getAttribute('avMobile')[0];
                $mail2 = $result->getAttribute('avAdresseMsg2')[0];
                $lawyer = $this->entityManager->getRepository(User::class)->findOneBy(['cnbId'=>$cnbId]);
                if ($lawyer){
                    if ($lawyer->getName() != $prenom){
                        $lawyer->setName($prenom);
                    }
                    if ($lawyer->getLastName() != $nom){
                        $lawyer->setLastName($nom);
                    }
                    if ($lawyer->getUserName() != $prenom.' '.$nom ){
                        $lawyer->setUserName($prenom.' '.$nom);
                    }if ($lawyer->getPhoneNumber() != $mobile){
                            if(substr( $mobile, 0, 1 )=="+"){
                                $pos = strpos($mobile, ' ');
                                $codeCountry=strstr($mobile, ' ', true);
                                if ($pos !== false) {
                                    $mobile = str_replace(' ','',substr($mobile, $pos));
                                }
                                $lawyer->setCodeCountry($codeCountry);
                                $lawyer->setPhoneNumber($mobile);
                            }elseif (substr( $mobile, 0, 2 )=="00"){
                                $pos = strpos($mobile, ' ');
                                $mobile = str_replace(' ','',$mobile);
                                $codeCountry=strstr($mobile, ' ', true);
                                $mobile = preg_replace('/' . '00' . '/', '', $mobile, 1);
                                if ($pos !== false) {
                                    $mobile = str_replace(' ','',substr($mobile, $pos));
                                }
                                $lawyer->setCodeCountry($codeCountry);
                                $lawyer->setPhoneNumber($mobile);
                            }elseif (substr( $mobile, 0, 1 )=="0") {
                                $mobile = str_replace(' ', '', $mobile);
                                $mobile = preg_replace('/' . '0' . '/', '', $mobile, 1);
                                $lawyer->setCodeCountry("+33");
                                $lawyer->setPhoneNumber($mobile);
                            }else{
                                $lawyer->setCodeCountry("+33");
                                $lawyer->setPhoneNumber($mobile);
                            }
                    }
                    if ($mail2){
                        $lawyer->setEmail($mail2);
                    }
                }
                if(!$lawyer){
                    $user = new User();
                    $user->setCnbId($cnbId);
                    $user->setName($prenom);
                    $user->setLastName($nom);
                    $user->setUsername($prenom.' '.$nom);
                    if($mobile!=null){
                        if(substr( $mobile, 0, 1 )=="+"){
                            $pos = strpos($mobile, ' ');
                            $codeCountry=strstr($mobile, ' ', true);
                            if ($pos !== false) {
                                $mobile = str_replace(' ','',substr($mobile, $pos));
                            }
                            $lawyer->setCodeCountry($codeCountry);
                            $lawyer->setPhoneNumber($mobile);
                        }elseif (substr( $mobile, 0, 2 )=="00"){
                            $pos = strpos($mobile, ' ');
                            $mobile = str_replace(' ','',$mobile);
                            $codeCountry=strstr($mobile, ' ', true);
                            $mobile = preg_replace('/' . '00' . '/', '', $mobile, 1);
                            if ($pos !== false) {
                                $mobile = str_replace(' ','',substr($mobile, $pos));
                            }
                            $lawyer->setCodeCountry($codeCountry);
                            $lawyer->setPhoneNumber($mobile);
                        }elseif (substr( $mobile, 0, 1 )=="0") {
                            $mobile = str_replace(' ', '', $mobile);
                            $mobile = preg_replace('/' . '0' . '/', '', $mobile, 1);
                            $lawyer->setCodeCountry("+33");
                            $lawyer->setPhoneNumber($mobile);
                        }else{
                            $lawyer->setCodeCountry("+33");
                            $lawyer->setPhoneNumber($mobile);
                        }
                    }
                    if ($mail2){
                        $user->setEmail($mail2);
                    }else{
                        $user->setEmail($cnbId.'cnb@cnb.fr');
                    }
                    $user->setRoles(array('ROLE_USER'));
                    $user->setPassword('mdpavocat');
                    $user->setEnabled(true);
                    $this->entityManager->persist($user);
                    $this->entityManager->flush();
                    dump('done');
                }
            }
        }

    }

}