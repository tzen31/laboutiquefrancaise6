<?php

namespace App\Controller;

use App\Classe\Mail;

use App\Entity\User;
use App\Form\RegisterType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegisterController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/inscription', name: 'app_register')]
    public function index(Request $request, ManagerRegistry $doctrine, UserPasswordHasherInterface $encoder): Response
    {
        $notification = null;

        $user = new User();
        $form = $this->createForm(RegisterType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $user = $form->getData();

            $search_email = $this->entityManager->getRepository(User::class)->findOneByEmail($user->getEmail());
            if (!$search_email) {
                $password = $user->getPassword();
                //Symfony 6
                $hashedPassword = $encoder->hashPassword($user, $password);
                $user->setPassword($hashedPassword);

//            $em = $doctrine->getManager();;
//            $em->persist($user);
//            $em->flush();

                $this->entityManager->persist($user);
                $this->entityManager->flush();

                //Envoi d'un email de confirmation d'inscription
                $mail = new Mail();
                $vars = [
                    'firstname' => $user->getFirstname(),
                ];
                //$content = "Bonjour ".$user->getFirstname()."<br>Bienvenue sur la première boutique dédiée au made in france.<br><br>";
                $mail->send($user->getEmail(), $user->getFirstname().' '.$user->getLastname(), 'Bienvenue sur la boutique française', 'welcome.html', $vars);

                $notification = "Votre inscription s'est correctement déroulée. Vous pouvez dès à présent vous connecter à votre compte.";
            }
            else
            {
                $notification = "L'email que vous avez renseigné existe déjà";
            }
        }

        return $this->render('register/index.html.twig', [
            'form' => $form->createView(),
            'notification' => $notification
        ]);
    }
}
