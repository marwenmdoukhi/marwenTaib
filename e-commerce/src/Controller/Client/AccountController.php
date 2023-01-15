<?php

namespace App\Controller\Client;

use App\Entity\User;
use App\Form\RegistrationType;
use App\Repository\RolesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AccountController extends AbstractController
{
    /**
     * @Route("/login", name="account_login")
     * @param AuthenticationUtils $utils
     * @return Response
     */
    public function index(AuthenticationUtils $utils)
    {
        $authError = $utils->getLastAuthenticationError();
        if ($authError !== null) {
            return new Response(0);
        }
        return $this->render('client/account/login.html.twig', [
            'authError' => $authError !== null,

        ]);
    }



    /**
     * @Route("/inscription", name="account_register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $encoder, EntityManagerInterface $manager, RolesRepository $repository)
    {
        $notification = null;
        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);
        $roles=$repository->findOneById(2);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $email=$form->getData()->getEmail();
            $search_email = $manager->getRepository(User::class)->findOneByEmail($user->getEmail());
            if (!$search_email) {
                $hash = $encoder->encodePassword($user, $user->getHash());
                $user->setHash($hash)
                    ->addUsersRole($roles);
                ;
                $manager->persist($user);
                $manager->flush();
//                $message = (new \Swift_Message('inscription'))
//                    ->setFrom('wf dali')
//                    ->setTo($email);
//                $message->setBody(
//                    $this-> $this->templating->render(
//                        'email/inscription.html.twig'
//                    ), 'text/html'
//                );
//                try {
//                    $this->get('mailer')->send($message);
//                } catch (FileException $e) {
//                    return new Response(0);
//                }
                $notification = "Votre inscription s'est correctement déroulée. Vous pouvez dès à présent vous connecter à votre compte.";
                return  $this->redirectToRoute('account_login');
            } else {
                $notification = "L'email que vous avez renseigné existe déjà.";
            }
        }
        return $this->render('client/account/registration.html.twig', [
            'form' => $form->createView(),
            'notification' => $notification,
        ]);
    }

    /**
     * @Route("/logout", name="account_logout")
     */
    public function logout(Request  $request)
    {
    }
}
