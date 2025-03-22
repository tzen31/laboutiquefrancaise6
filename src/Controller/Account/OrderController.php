<?php

namespace App\Controller\Account;

use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\OrderRepository;

class OrderController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/compte/commande/{id_order}', name: 'app_account_order')]
    public function index($id_order, OrderRepository $orderRepository): Response
    {
        //$orders = $this->entityManager->getRepository(Order::class)->findSuccessOrders($this->getUser());
        $order = $orderRepository->findOneBy([
            'id' => $id_order,
            'user' => $this->getUser()
        ]);

        if (!$order) {
            $this->redirectToRoute('app_home');
        }

        return $this->render('account/order/index.html.twig', [
            'order' => $order
        ]);
    }

    #[Route('/compte/mes-commandes/{reference}', name: 'app_account_order_show')]
    public function show($reference): Response
    {
        $order = $this->entityManager->getRepository(Order::class)->findOneByReference($reference);

        if (!$order || $order->getUser() != $this->getUser() ) {
            $this->redirectToRoute('app_account_order');
        }

        return $this->render('account/order_show.html.twig', [
            'order' => $order
        ]);
    }
}
