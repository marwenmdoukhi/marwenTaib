<?php


namespace App\Security;


use App\Entity\User;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Hslavich\OneloginSamlBundle\Security\Authentication\Token\SamlTokenInterface;
use Hslavich\OneloginSamlBundle\Security\User\LegacySamlUserFactoryInterface;
use Hslavich\OneloginSamlBundle\Security\User\SamlUserFactoryInterface;
use phpDocumentor\Reflection\Types\This;
use Symfony\Component\Security\Core\User\UserInterface;

class UserFactory implements SamlUserFactoryInterface
{

    private $em;


    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param $username
     * @param array $attributes
     * @return UserInterface
     */
    public function createUser($username, array $attributes = []): UserInterface
    {
        $attributes = $username->getAttributes();
        $cnbId = $attributes['cnb_id'][0];
        $connectedUser = $this->em->getRepository(User::class)->findOneBy(['cnbId'=>$cnbId]);
        if ($connectedUser){
            return $connectedUser;
        }else{
            $user = new User();
            $user->setRoles(array('ROLE_USER'));
            $user->setUsername($attributes['cnb_prenom'][0].' '.$attributes['cnb_nom'][0]);
            $user->setPassword('notused');
            $user->setEmail($cnbId . 'cnb@cnb.fr');
            $user->setName(mb_convert_encoding($attributes['cnb_nom'][0], "UTF-8", "auto"));
            $user->setLastName(mb_convert_encoding($attributes['cnb_prenom'][0], "UTF-8", "auto"));
            $user->setCnbId($attributes['cnb_id'][0]);
            $this->em->persist($user);
            $this->em->flush();
            return $user;
        }
    }
}