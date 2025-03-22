<?php

namespace App\Controller;

use Stripe\Stripe;
use Stripe\Checkout\Session;

use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;
use App\Classe\Cart;

class PayementController extends AbstractController
{
    #[Route('/commande/paiement/{id_order}', name: 'app_payment')]
    public function index($id_order, OrderRepository $orderRepository, EntityManagerInterface $entityManagerInterface): Response
    {
        Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);
        $YOUR_DOMAIN = $_ENV['DOMAIN'];

        //$order = $orderRepository->findOneById($id_order);
        $order = $orderRepository->findOneBy([
            'id' => $id_order,
            'user' => $this->getUser()
        ]);

        if (!$order) {
            return $this->redirectToRoute('app_home');
        }

        $products_for_stripe = [];

        foreach ($order->getOrderDetails() as $product) {
            $products_for_stripe[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'unit_amount' => number_format($product->getProductPriceWt() * 100, 0, '', ''),
                    'product_data' => [
                        'name' =>  $product->getProductName(),
                        'images' => [
                            $YOUR_DOMAIN.'/uploads/'.$product->getProductIllustration()
                        ]
                    ]
                ],
                'quantity' =>  $product->getProductQuantity()
            ]; 
        }

        $products_for_stripe[] = [
            'price_data' => [
                'currency' => 'eur',
                'unit_amount' => number_format($order->getCarrierPrice() * 100, 0, '', ''),
                'product_data' => [
                    'name' =>  'Transporteur : '. $order->getCarrierName(),                    
                ]
            ],
            'quantity' => 1
        ]; 

        $checkout_session = Session::create([
            'line_items' => [[
                $products_for_stripe
            ]],
            'customer_email' => $this->getUser()->getEmail(),
            'payment_method_types' => ['card'],
            'mode' => 'payment',
            'success_url' => $YOUR_DOMAIN . '/commande/merci/{CHECKOUT_SESSION_ID}',
            'cancel_url' => $YOUR_DOMAIN . '/commande/erreur/{CHECKOUT_SESSION_ID}',
        ]);

        //header('HTTP/1.1 303 See Other');
        //header('Location:', $checkout_session->url);

        $order->setStripeSessionId($checkout_session->id);
        $entityManagerInterface->flush(); //Enregistre l'id de la session en base

        return $this->redirect($checkout_session->url);
        $response = new JsonResponse(['id' => $checkout_session->id ]);
        //return $response;
    }

    #[Route('/commande/merci/{stripe_session_id}', name: 'app_payment_succes')]
    public function success(Cart $cart, $stripe_session_id, OrderRepository $orderRepository, EntityManagerInterface $entityManagerInterface): Response
    {
        $order = $orderRepository->findOneBy([
            'stripe_session_id' => $stripe_session_id,
            'user' => $this->getUser()
        ]);

        if (!$order) {
            return $this->redirectToRoute('app_home');
        }

        if ($order->getState() == 1) {
            $order->setState(2);
            $cart->remove();
            $entityManagerInterface->flush();
        }

        return $this->render('payment/success.html.twig', [            
            'order' => $order,           
        ]);

    }
}
