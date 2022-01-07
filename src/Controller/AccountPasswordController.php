<?php

namespace App\Controller;

use App\Form\ChangePasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AccountPasswordController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;        
    }

    /**
     * @Route("/compte/modifier-mon-mot-de-passe", name="account_password")
     */
    public function index(Request $request, UserPasswordHasherInterface $encoder): Response
    {
        // ajout d'un système de notification de mise à jour du pwd:
        $notification = null ;
        
        //on appelle l'objet utilisateur et on l'injecte dans la variable user
        //pour le passer au formulaire
        $user = $this->getUser();
        $form = $this->createForm(ChangePasswordType::class, $user);

        //traitement du formulaire de modif pwd:
            //méthode handleRequest($request) = demande au formulaire d'écouter la requête entrante (ATTENTION à ajouter l’injection de dépendance Request attribué à la $request dans la fonction index) idem RegisterController
        $form -> handleRequest($request);


        //si form a été soumis et qu'il est valide (si les types dans le register type sont ok)
        if($form->isSubmitted() && $form->isValid()){
        //ATTENTION : ajouter l’injection de dépendance UserPasswordHasherInterface attribué à la $encoder dans la fonction index pour pouvoir l'utiliser ici) idem RegisterController

            //on récupère la valeur (sous forme de data) de l'ancien pwd saisi dans le form
            $old_pwd = $form->get('old_password')->getData();

            //si encoder est true (si la méthode isPasswordValid a la correspondance entre les 2 var en param)
            if ($encoder->isPasswordValid($user, $old_pwd)) {
        
                //on récupère la valeur (sous forme de data) du nouveau pwd saisi dans le form
                $new_pwd = $form->get('new_password')->getData();

                //on place la valeur encryptée du nouveau pwd dans la var $password
                $password = $encoder->hashPassword($user, $new_pwd);

                //on sette le nouveau pwd hashé dans l'objet user
                $user -> setPassword($password);
            
                //on appelle l'entityManager pour mettre à jour la bdd et on flush (idem RegisterController mais pas besoin de figer la data avec persist() pour une mise à jour) ATTENTION à faire l'injection de dépendance (private $entityManager;) au début pour utiliser doctrine + créer le constructeur pour lier la var privée à une fonction publique 
                $this->entityManager->flush();
                $notification = "Votre mot de passe a bien été mis à jour.";
            } else{
                $notification = "Votre mot de passe actuel est erroné.";
            }
        }

        // idem registerController + notification de modif du pwd ok ou non
        return $this->render('account/password.html.twig', [
            'form' => $form->createView(),
            'notification' => $notification
        ]);
    }
}