<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Entity\Order;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StripeController extends AbstractController
{
    #[Route('/commande/create-session/{reference}', name: 'app_stripe_create_session')]
    public function index(EntityManagerInterface $entityManager,Cart $cart, $reference): Response
    {
        $YOUR_DOMAIN = 'http://localhost:8000';
        $products_for_stripe = [];
        //$ref=$reference;
        //dd("reference = ".$ref);
//dd('reference = '.$reference); //OK : reference = 23052022-628b9pm1pm31pmb6
$order = $entityManager->getRepository(Order::class)->findOneByReference($reference);
        //$order = $entityManager->getRepository(Order::class)->findOneByReference('23052022-628b9pm1pm31pmb6');
        //$order = $entityManager->getRepository(Order::class)->findOneById(32);
        //dd($order);

        if (!$order){
            new JsonResponse(['error' => 'order']);
        }
        //dd($order->getOrderDetails->getValues());

        //foreach ($cart->getFull() as $product) {
        foreach ($order->getOrderDetails()->getValues() as $product) {
            $product_object = $entityManager->getRepository(Product::class)->findOneByName($product->getProduct());
            $products_for_stripe[] = [
                'price_data' => [
                    'currency' => 'eur',
                    //'unit_amount' => $product['product']->getPrice(),
                    'unit_amount' => $product->getPrice(),
                    'product_data' => [
                        //'name' => $product['product']->getName(),
                        'name' => $product->getProduct(),
                        'images' => [$YOUR_DOMAIN.'/uploads/'.$product_object->getIllustration()],
                    ],
                ],
                //'quantity' => $product['quantity'],
                'quantity' => $product->getQuantity(),
            ];
        }

        //Cout du transporteur
        $products_for_stripe[] = [
            'price_data' => [
                'currency' => 'eur',
                'unit_amount' => $order->getCarrierPrice(),
                'product_data' => [
                    'name' => $order->getCarrierName(),
                    'images' => [$YOUR_DOMAIN],
                ],
            ],
            'quantity' => 1,
        ];

        //dd($products_for_stripe);

        //Clé Secrète:
        Stripe::setApiKey('sk_test_51L1wpDDVIJ13lx1RERCtQSGvI5plIMJPDAT2i3wdQgm326KgRdtIusLfO0UJg9Bk97CpfIo9rVq7okxuLNRvpxZM00bVQzzEhO');

        $checkout_session = Session::create([
            'customer_email' => $this->getUser()->getEmail(),
            'payment_method_types' => ['card'],
            'line_items' => [[
                $products_for_stripe
            ]],
            'mode' => 'payment',
            'success_url' => $YOUR_DOMAIN . '/commande/merci/{CHECKOUT_SESSION_ID}',
            'cancel_url' => $YOUR_DOMAIN . '/commande/erreur/{CHECKOUT_SESSION_ID}',
        ]);

        $order->setStripeSessionId($checkout_session->id);
        $entityManager->flush();

        $response = new JsonResponse(['id' => $checkout_session->id]);
        return $response;
    }
}