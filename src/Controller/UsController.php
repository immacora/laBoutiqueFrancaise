<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UsController extends AbstractController
{
    /**
     * @Route("/qui-sommes-nous", name="us")
     */
    public function index(): Response
    {
        return $this->render('us/index.html.twig', [
            'controller_name' => 'UsController',
        ]);
    }
}
