<?php

namespace App\Controller;

use App\Classe\Mail;
use App\Form\ContactType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    /**
     * @Route("/nous-contacter", name="contact")
     */
    public function index(Request $request): Response
    {
        $form = $this->createForm(ContactType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $this->addFlash('notice', 'Merci de nous avoir contacté. Notre équipe va vous répondre dans les meilleurs délais.');
        
            $mailUser = new Mail();
            $mailAdmin = new Mail();
            $content = "Bonjour ".$user['prenom'].' '.$user['nom']."<br/>Vous avez envoyé le message suivant à La Boutique Française:<br/><br/>" .$user['content']."<br/><br/>Nous vous répondrons dans les plus brefs délais<br/>à l'adresse mail suivante: ".$user['email'] ;
            $mailAdmin->send('contact.laboutiquefrancaise@immacora.com', 'La Boutique Française', "Vous avez reçu un message de La Boutique Française", "Vous avez reçu le message suivant de La Boutique Française:<br/><br/>". $content);
            $mailUser->send($user['email'], $user['prenom'], 'Votre message à La Boutique Française', $content);
        }

        return $this->render('contact/index.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
