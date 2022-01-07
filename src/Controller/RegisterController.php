<?php

namespace App\Controller;

use App\Classe\Mail;
use App\Entity\Header;
use App\Entity\User;
use App\Form\RegisterType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegisterController extends AbstractController
{
    //on crée la var privée $entityManager (convention de nommage pour désigner doctrine) qui va permettre d'utiliser doctrine pour enregistrer les données en bdd
    private $entityManager;

    //on crée la fonction publique du constructeur pour récupérer la valeur de la var $entityManager à l'extérieur de la fonction : on injecte l'EntityManagerInterface qui prend la valeur de la var $entityManager
    public function __construct(EntityManagerInterface $entityManager)
    {
        //dans cette var entityManager (celle de création de l'objet private $entityManager; qu'on appelle sans $), on met la valeur de la var $entityManager (celle de de public function __construct(EntityManagerInterface $entityManager)) qu'on vient d'instancier grâce à EntityManagerInterface
        $this->entityManager = $entityManager;        
    }

    /**
     * @Route("/inscription", name="register")
     */
    //on demande à la fonction index d'embarquer l'objet Request qui sera inclu dans la variable $request (= injection de dépendance)+ on injecte l'objet UserPasswordHasherInterface dépendance dans la variable $encoder pour que la fonction utilise ces objets en construisant son formulaire
    public function index(Request $request, UserPasswordHasherInterface $encoder): Response
    {
        $headers = $this->entityManager->getRepository(Header::class)->findAll();
        $notification = null;

        //on appelle la création du formulaire pour le nouvel user
        $user = new User();
        $form = $this ->createForm(RegisterType::class, $user);

        /*on demande à la fonction : 
        1. dès que le formulaire est soumis
        2. traite l'information
        3. vérifie la validité des données
        4. enregistre en bdd
        */
 
        //1. on passe la $request (objet cf + haut) à la méthode puis on écoute la requête pour savoir si c'est un post
        $form->handleRequest($request);

        //2. et 3. si mon form a été soumis et qu'il est valide (si les types dans le register type sont ok)
        if($form->isSubmitted() && $form->isValid()){
            //on injecte dans l'objet $user toutes les données récupérées du formulaire
            $user = $form->getData();

            $search_email = $this->entityManager->getRepository(User::class)->findOneByEmail($user->getEmail());

            if (!$search_email) {
                //on place la valeur du pwd (récupérée grâce au getter de l'entité User) dans la var $password en passant par le hashage du pwd
                $password = $encoder->hashPassword($user, $user->getPassword());
                //on réinjecte le pwd hashé dans l'objet user
                $user -> setPassword($password);

                //4. on appelle la var $this->entityManager de la fonction publique constructeur pour enregistrer les données dans la bdd (plusieurs manières sont possibles) et on lui applique la méthode persist avec l'entité $user en paramètre qui va figer les données (la data) pour la préparer à être créée en bdd (mais pas mise à jour)
                $this->entityManager->persist($user);
                //après avoir figé, on flush l'info (on exécute la persistance = on prend la data figée et on l'enregistre en bdd)
                $this->entityManager->flush();

                $mail = new Mail();
                    $content = "Bonjour ".$user->getFirstname()."<br/>Bienvenue sur la notre boutique dédiée au made in France.<br><br/>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Aperiam expedita fugiat ipsa magnam mollitia optio voluptas! Alias, aliquid dicta ducimus exercitationem facilis, incidunt magni, minus natus nihil odio quos sunt?";
                    $mail->send($user->getEmail(), $user->getFirstname(), 'Bienvenue sur La Boutique Française', $content);

                    $notification = "Votre inscription s'est correctement déroulée. Vous pouvez dès à présent vous connecter à votre compte.";
            } else {
                $notification = "L'email que vous avez renseigné existe déjà.";
            }
        }

        // on retourne la vue "enregistrement"(register)
        return $this->render('register/index.html.twig', [
            'form' => $form->createView(),
			'notification' => $notification,
            'headers' => $headers										
        ]);
    }
}
