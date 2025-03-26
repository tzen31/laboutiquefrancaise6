<?php

namespace App\Controller\Account;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class WishlistController extends AbstractController
{
    #[Route('/compte/liste-de-souhait', name: 'app_account_wishlist')]
    public function index(): Response
    {
        return $this->render('account/wishlist/index.html.twig');
    }

    #[Route('/compte/liste-de-souhait/add/{id}', name: 'app_account_wishlist_add')]
    public function add(ProductRepository $productRepository, EntityManagerInterface $entityManager, Request $request, $id): Response
    {
        //1. Récupérer l'objet du produit souhaité
        $product = $productRepository->findOneById($id);

        //2. Si produit existant, ajouter le produit à la wishlist
        if ($product) {
            $this->getUser()->addWishlist($product);
            
            //3. Sauvegarder en base de données
            $entityManager->flush();
        }

        $this->addFlash(
            'success',
            "Produit correctement ajouté à votre liste de souhait."
        );

        return $this->redirect($request->headers->get('referer'));
    }

    #[Route('/compte/liste-de-souhait/remove/{id}', name: 'app_account_wishlist_remove')]
    public function remove(ProductRepository $productRepository, EntityManagerInterface $entityManager, Request $request, $id): Response
    {
        //1. Récupérer l'objet du produit à supprimer
        $product = $productRepository->findOneById($id);

        //2. Si produit existant, supprimer le produit de la wishlist
        if ($product) {
            $this->addFlash('success', 'Produit correctement supprimé de votre liste de souhait');

            $this->getUser()->removeWishlist($product);
            
            //3. Sauvegarder en base de données
            $entityManager->flush();
        } else {
            $this->addFlash('danger', 'Produit introuvable');

        }

        return $this->redirect($request->headers->get('referer'));
    }
}

