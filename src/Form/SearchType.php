<?php

namespace App\Form;

use App\Classe\Search;
use App\Entity\Category;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormBuilderInterface;

class SearchType extends AbstractType
{

    //	Appeler la fonction Symfony buildForm () pour construire le formulaire et le remplir selon besoin : Ajouter la propriété string qui va représenter la recherche texte des utilisateurs (saisie dans l’input), son type et un tableau d’option

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('string', TextType::class, [
                'label'=> false,
                'required' => false,
                'attr'=>  [
                    'placeholder' => 'Votre recherche...',
                    'class' => 'form-control-sm'
                ]
            ])
            ->add('categories', EntityType::class, [
                'label'=> false,
                'required' => false,
                'class' => Category::class,
                'multiple' => true,
                'expanded' => true
            ])
            ->add('submit', SubmitType::class, [
                'label'=> 'Filtrer',
                'attr'=>  [
                    'class' => 'btn-block btn-info'
                ]
            ])
        ;
    }

    // Appeler  une fonction permettant de configurer des options : copier la fonction configureOptions() générée par Symfony dans RegisterType et la coller en remplaçant la classe user par la search
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Search::class,
            // spécifier la méthode GET pour que les données du formulaire transitent par l’URL ce qui permet de copier-coller et partager l’URL cliquable, configurée avec les bons filtres. 
            'method' => 'GET',
            // Désactiver le cripting de Symfony car non nécessaire sur un formulaire de recherche (pas de données sensibles)
            'crsf_protection' => false,
        ]);
    }

    // Appeler la fonction Symfony getBlockPrefix() qui retourne un tableau de valeur préfixé du nom de la classe SearchType et l’initialiser à vide pour ne pas intégrer ces valeurs dans l’URL
    public function getBlockPrefix()
    {
        return '';
    }

}
