<?php

namespace App\Controller\Account;

use App\Form\ChangePasswordType;
use App\Form\ForgotPasswordFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
//use function Symfony\Component\Form\handleRequest;

class PasswordController extends AbstractController
{
    private $entityManager;

    /**
     * AccountPasswordController constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
    }

    #[Route('/mot-de-passe_oublie', name: 'app_password')]
    public function index(Request $request): Response
    {
        //1. Formulaire
        $form = $this->createForm(ForgotPasswordFormType::class);
        $form->handleRequest($request);

        //2. Traitement formulaire
        if ($form->isSubmitted() && $form->isValid()){
            //3. Si l'email renseigné par l'utilisateur est en base de données
    
            //4. Si c'est le cas, on reset le password et on envoie par email le nouveau mot de passe
    
            //5. Si aucun email trouvé, on push une notification : Email introuvable
        }

        
    
        return $this->render('password/index.html.twig', [
            'forgotPasswordForm' => $form->createView()
        ]);
    }

    #[Route('/compte/modifier-mon-mot-de-passe', name: 'app_account_password')]
    public function modifier(Request $request, UserPasswordHasherInterface $encoder): Response
    {
        $notification = 'valeur par defaut';

        $user = $this->getUser();
        $form = $this->createForm(ChangePasswordType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $old_pwd = $form->get('old_password')->getData();
            if ($encoder->isPasswordValid($user, $old_pwd)) {
                $new_pwd = $form->get('new_password')->getData();

                //$password = $encoder->encodePassword($user, $new_pwd); //Symfony 5
                $password = $encoder->hashPassword($user, $new_pwd); //Symfony 6
                $user->setPassword($password);
                $this->entityManager->persist($user);
                $this->entityManager->flush();

                $notification = 'Votre mot de passe a bien été mis à jour';
            }
            else
                $notification = "Votre mot de passe actuel n'est pas le bon";
        }

        return $this->render('account/password/index.html.twig', [
            'form' => $form->createView(),
            'notification' => $notification
        ]);
    }
}
