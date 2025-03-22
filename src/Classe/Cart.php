<?php

namespace App\Classe;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class Cart
{
    //private $session; //(RequestStack)
    //private $entityManager;

    //public function __construct(EntityManagerInterface $entityManager, RequestStack $session)
    public function __construct(private RequestStack $requestStack)
    {
        //$session = $this->requestStack = $requestStack;
        //$this->entityManager = $entityManager;
    }

    //public function add($id)
    public function add($product)
    {
         //On récupère la session
        //$session = $this->session->getCurrentRequest()->getSession();
        // Ajouter une qtity +1 à mon produit
        // $cart = $session->get('cart', []);
        // if (!empty($cart[$id])){
        //     $cart[$id]++;
        // }
        // else {
        //     $cart[$id]=1;
        // }
        //$session->set('cart', $cart);

        // Appeler la session CART de symfony
        $cart = $this->requestStack->getSession()->get('cart');

        //Ajouter une quantité +1 à mon produit
        if (isset($cart[$product->getId()])) {

            $cart[$product->getId()] = [
                'object' => $product,
                'qty' => $cart[$product->getId()]['qty'] + 1
            ];
            
        } else {
            $cart[$product->getId()] = [
                'object' => $product,
                'qty' => 1
            ];
        }

        // Créer ma session Cart
        $this->requestStack->getSession()->set('cart', $cart); 
        //dd($this->requestStack->getSession()->get('cart', $cart));         
    }

    public function get()
    {
        $session=$this->requestStack->getCurrentRequest()->getSession();
        return $session->get('cart');
    }

    public function remove()
    {
        return $this->requestStack->getSession()->remove('cart');
    }

    public function delete($id)
    {
        $session=$this->requestStack->getCurrentRequest()->getSession();
        $cart = $session->get('cart', []);
        unset($cart[$id]);

        return $session->set('cart', $cart);
    }

    public function decrease($id)
    {
        //Vérifier si la quantité de notre produit != 1
        $cart = $this->requestStack->getSession()->get('cart');

        if ($cart[$id]['qty'] > 1) {
            $cart[$id]['qty'] = $cart[$id]['qty'] - 1;
        } else {
            unset($cart[$id]);
        }

        $this->requestStack->getSession()->set('cart', $cart);
    }

    // public function getCart()
    // {
    //     $cartComplete = [];

    //     if ($this->get())
    //     {
    //         foreach ($this->get('cart') as $id => $quantity)
    //         {
    //             $product_object = $this->entityManager->getRepository(Product::class)->findOneById($id);
    //             if (!$product_object) {
    //                 $this->delete($id);
    //                 continue; //permet de sortir de la boucle.
    //             }
    //             $cartComplete[] = [
    //                 'product' => $product_object,
    //                 'quantity' => $quantity
    //             ];
    //         }
    //     }

    //     return $cartComplete;
    // }
    
    public function getCart()
    {
        return $this->requestStack->getSession()->get('cart');
    }

    /*
     * fullQuantity()
     * Fonction retournant le nombre total de produit au panier
     */
    public function fullQuantity()
    {
        $cart = $this->getCart();
        $quantity = 0;

        if (!isset($cart)) {
            return $quantity;
        }

        foreach ($cart as $product) {
            $quantity = $quantity + $product['qty'];
        }

        return $quantity;
    }

        /*
     * getTotalWt()
     * Fonction retournant le prix total des produits au panier
     */
    public function getTotalWt()
    {
        $cart = $this->getCart();
        $price = 0;

        if (!isset($cart)) {
            return $price;
        }

        foreach ($cart as $product) {
            $price = $price + ($product['object']->getPriceWt() * $product['qty']);
        }

        return $price;
    }
}