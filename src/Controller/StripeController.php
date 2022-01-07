<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Classe\Cart;
use App\Entity\Order;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StripeController extends AbstractController
{
    /**
     * @Route("/commande/create-session/{reference}", name="stripe_create_session")
     */
    public function index(EntityManagerInterface $entityManager, Cart $cart, $reference)
    {
        $products_for_stripe = [];
        $YOUR_DOMAIN = 'http://127.0.0.1:8000';

        $order = $entityManager->getRepository(Order::class)->findOneBy(['reference' => $reference]);

        if(!$order){
            new JsonResponse(['error' => 'order']);
        }

        foreach($order->getOrderDetails()->getValues() as $product){
            $product_object = $entityManager->getRepository(Product::class)->findOneBy(['name' => $product->getProduct()]);
            //liaison produit bdd vers stripe
            $products_for_stripe[] = [
                //'price' => '{{PRICE_ID}}', # Provide the exact Price ID (e.g. pr_1234) of the product you want to sell
                'price_data' => [
                    'currency' => 'eur',
                    'unit_amount' => $product->getPrice(),
                    'product_data' => [
                        'name' => $product->getProduct(),
                        'images' => [$YOUR_DOMAIN."/uploads/".$product_object->getIllustration()],
                    ],
                ],
                'quantity' => $product->getQuantity(),
            ];
        }

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

        // This is your test secret API key.           
        Stripe::setApiKey('sk_test_51K9809Jjsl5lsnokiSFM2xccHNqOlpo092kYMItW1MI7xnThAoutwZgUQYyE4mCQY4u01JhQdz7nJFSEcGVSvvqW00J06woVOu');

        $checkout_session = Session::create([
            'customer_email'=> $this->getUser()->getEmail(),
            'payment_method_types' => ['card'],
            'line_items' => [
                //'price' => '{{PRICE_ID}}', # Provide the exact Price ID (e.g. pr_1234) of the product you want to sell
                $products_for_stripe
          ],
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
