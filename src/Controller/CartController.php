<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Entity\Product;
use App\Repository\OrderRepository;
//use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ProductRepository;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    //private $entityManager;

    // public function __construct(EntityManagerInterface $entityManager)
    // {
    //     $this->entityManager = $entityManager;
    // }

    #[Route('/mon-panier/{motif}', name: 'app_cart', defaults: [ 'motif' => null ])]
    public function index(Cart $cart, $motif): Response
    {
        //$cartComplete = [];
        //
        //if ($cart->get())
        //{
        //    foreach ($cart->get('cart') as $id => $quantity)
        //    {
        //        $cartComplete[] = [
        //            'product' => $this->entityManager->getRepository(Product::class)->findOneById($id),
        //            'quantity' => $quantity
        //        ];
        //    }
        //}
        //
        //dd($cartComplete);
        //return $this->render('cart/index.html.twig', [
        //    'cart' => $cartComplete
        //]);
        if ($motif='annulation'){
            $this->addFlash(
                'info',
                'Paiement annulé : Vpus pouvez mettre à jour votre panier et votre commande.'
            );
        }

        return $this->render('cart/index.html.twig', [
            'cart' => $cart->getCart(),
            'totalWt' => $cart->getTotalWt()
        ]);
    }

    #[Route('/cart/add/{id}', name: 'app_add_to_cart')]
    public function add(Cart $cart, $id, ProductRepository $productRepository, Request $request)
    {
        //dd($request->headers->get('referer'));
        $product = $productRepository->findOneById($id); 

        //$cart->add($id);
        $cart->add($product);

        $this->addFlash(
            'success',
            "Produit correctement ajouté à votre panier."
        );

        //dd($cart);
        // return $this->redirectToRoute('app_product', [
        //     'slug' => $product->getSlug()
        // ]);
        return $this->redirect($request->headers->get('referer'));
    }

    #[Route('/cart/remove', name: 'app_remove_my_cart')]
    public function remove(Cart $cart): Response
    {
        $cart->remove();

        return $this->redirectToRoute('app_products');
    }

    #[Route('/cart/delete/{id}', name: 'app_delete_to_cart')]
    public function delete(Cart $cart, $id)
    {
        $cart->delete($id);      

        return $this->redirectToRoute('app_cart');        
    }

    #[Route('/cart/decrease/{id}', name: 'app_decrease_to_cart')]
    public function decrease(Cart $cart, $id, Request $request)
    {
        $cart->decrease($id);

        $this->addFlash(
            'success',
            "Produit correctement supprimée de votre panier."
        );

        //return $this->redirectToRoute('app_cart');
        return $this->redirect($request->headers->get('referer'));
    }
}
