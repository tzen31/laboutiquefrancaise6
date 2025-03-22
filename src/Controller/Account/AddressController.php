<?php

namespace App\Controller\Account;

use App\Classe\Cart;
use App\Entity\Address;
use App\Form\AddressType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\AddressRepository;

class AddressController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/compte/adresses', name: 'app_account_addresses')]
    public function index(): Response
    {
        return $this->render('account/address/index.html.twig');
    }

    #[Route('/compte/adresses/ajouter-une-adresse/[$id}', name: 'app_account_address_form', defaults: ['id' => null])]
    public function add(Cart $cart, Request $request, $id, AddressRepository $addressRepository): Response
    {
        if ($id) {
            $address = $addressRepository->findOneById($id);
            if (!$address OR $address->getUser() != $this->getUser()) {
                return $this->redirecttoRoute('app_account_addresses');
            }
        } else {
            $address = new Address();
            $address->setUser($this->getUser());
        }        

        $form = $this->createForm(AddressType::class, $address);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($address);
            $this->entityManager->flush();

            $this->addFlash(
                'success',
                'Votre addresse est correctement sauvegardé'
            );

            //dd($address);
            if ($cart->fullQuantity() > 0) {
                return $this->redirectToRoute('app_order');
            }
            else {
                return $this->redirectToRoute('app_account_addresses');
            }
        }

        return $this->render('account/address/form.html.twig', [
            'addressForm' => $form->createView()
        ]);
    }

    #[Route('/compte/modifier-une-adresse/{id}', name: 'app_account_address_edit')]
    public function edit(Request $request, $id): Response
    {
        $address = $this->entityManager->getRepository(Address::class)->findOneById($id);

        if (!$address || $address->getUser() != $this->getUser()){
            return $this->redirectToRoute('app_account_addresses');
        }

        $form = $this->createForm(AddressType::class, $address);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();
            return $this->redirectToRoute('app_account_addresses');
        }

        return $this->render('account/address/form.html.twig', [
            'addressForm' => $form->createView()
        ]);
    }

    #[Route('/compte/addresses/supprimer-une-adresse/{id}', name: 'app_account_address_delete')]
    public function delete($id, AddressRepository $addressRepository): Response
    {
        if ($id) {
            $address = $addressRepository->findOneById($id);
            if (!$address OR $address->getUser() != $this->getUser()) {
                return $this->redirecttoRoute('app_account_addresses');
            }
        } else {
            $address = new Address();
            $address->setUser($this->getUser());
        }    
        
        // $address = $this->entityManager->getRepository(Address::class)->findOneById($id);

        if ($address && $address->getUser() == $this->getUser()){
            $this->entityManager->remove($address);
            $this->entityManager->flush();

            $this->addFlash(
                'success',
                'Votre addresse est correctement supprimé'
            );
        }
        else
            dd('coucou');

        return $this->redirectToRoute('app_account_addresses');
    }
}
