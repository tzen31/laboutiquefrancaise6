<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Entity\Order;
use App\Entity\OrderDetails;
use App\Form\OrderType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\DateTime;
use function Symfony\Bundle\FrameworkBundle\Controller\redirectToRoute;
use function Symfony\Config\Monolog\publisher;

class OrderController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
    }

    /*
     * Choix de l'adresse de livraison et du transporteur
     */
    #[Route('/commande/livraison', name: 'app_order')]
    public function index(): Response
    {
        $addresses = $this->getUser()->getAddresses();

        if (count($addresses) == 0) {
            return $this->redirectToRoute('app_account_address_form');
        }

        $form = $this->createForm(OrderType::class, null, [
            'addresses' => $addresses,
            'action' => $this->generateUrl('app_order_summary')
        ]);

        return $this->render('order/index.html.twig', [
            'deliverForm' => $form->createView(),
        ]);
    }

    /*
     * 2ème étape du tunnel d'achat
     * Récap de la commande de l'utilisateur
     * Insertion en base de donnée
     * Préparation du paiement vers Stripe
     */
    #[Route('/commande/recapitulatif', name: 'app_order_summary')]
    public function add(Request $request, Cart $cart, EntityManagerInterface $entityManager): Response
    {
        if ($request->getMethod() != 'POST') {
             return $this->redirectToRoute('app_cart');
        }

        $products = $cart->getCart();

        $form = $this->createForm(OrderType::class, null, [
            'addresses' => $this->getUser()->getAddresses(),
        ]);

        $form->handleRequest($request);
        
        //dd($form->get('addresses')->getData());
        if ($form->isSubmitted() && $form->isValid()) {
            // Stocker les informations en BDD

            // Création de la chaîne adresse
            $addressObj = $form->get('addresses')->getData();

            $address = $addressObj->getFirstname().' '.$addressObj->getLastname().'<br/>';
            $address .= $addressObj->getAddress().'<br/>';
            $address .= $addressObj->getPostal().' '.$addressObj->getCity().'<br/>';
            $address .= $addressObj->getCountry().'<br/>';
            $address .= $addressObj->getPhone();

            $order = new Order();
            $order->setUser($this->getUser());
            $order->setCreatedAt(new \DateTime());
            $order->setState(1); //En attente de paiement
            $order->setCarrierName($form->get('carriers')->getData()->getName());
            $order->setCarrierPrice($form->get('carriers')->getData()->getPrice());
            $order->setDelivery($address);

            foreach ($products as $product) {
                $orderDetails = new OrderDetails();
                $orderDetails->setProductName($product['object']->getName());
                $orderDetails->setProductIllustration($product['object']->getIllustration());
                $orderDetails->setProductPrice($product['object']->getPrice());
                $orderDetails->setProductTva($product['object']->getTva());
                $orderDetails->setProductQuantity($product['qty']);
                $order->addOrderDetail($orderDetails);
            }

            $entityManager->persist($order);
            $entityManager->flush();
        }

        return $this->render('order/summary.html.twig', [
            'choices' => $form->getData(),
            'cart' => $products,
            'order' => $order,
            'totalWt' => $cart->getTotalWt()
        ]);
    }

}

