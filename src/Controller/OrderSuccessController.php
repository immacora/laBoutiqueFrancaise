<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Classe\Mail;					
use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderSuccessController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/commande/merci/{stripeSessionId}", name="order_success")
     */
    public function index(Cart $cart, $stripeSessionId): Response
    {
        $order = $this->entityManager->getRepository(Order::class)->findOneBy(['stripeSessionId' => $stripeSessionId]);
        if (!$order || $order->getUser() != $this->getUser()){
            return $this->redirectToRoute('home');
        }

        if($order->getState() == 0){

            $cart->remove();
            $order->setState(1);
            $this->entityManager->flush();
			
            $mail = new Mail();
            $username = $order->getUser()->getFirstname().' '.$order->getUser()->getLastname();
            $content = "Merci pour votre commande n° ".$order->getReference(). ".<br><br/>Elle sera traitée dans les meilleurs délais";
            $mail->send($order->getUser()->getEmail(), $username, 'Votre commande sur La Boutique Française', $content, $username);            													
        }

        return $this->render('order_success/index.html.twig', [
            'order' => $order
        ]);
    }
}
