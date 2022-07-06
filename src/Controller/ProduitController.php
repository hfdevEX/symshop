<?php

namespace App\Controller;

use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


/*On créer un controller pour traiter une partie de notre application, ici, en l'ocurence la partie qui va concerner les produits */

/*Tous les controllers heritent par défaut d'une classe contenant plusieurs méthodes pour nous faciliter le traitement, c'est la classe abstraite AbstractController */

class ProduitController extends AbstractController
{
    /**
     * Cette fonction conduit vers la page listant tous nos produits.
     * 
     * Le controleur utilise les Routes (URL) comme des écouteurs, et lorsqu'une route est appellée, une fonction corespondante à cette même route se déclenche
     * 
     * @Route("/produits", name="app_produits")
     */
    public function index(ProduitRepository $produitRepo): Response
    {
        /**
         * Pour sélectionner des données dans une table SQL, nous avons le Repository de l'entité corespondante (Produit => 'ProduitRepository). Le repository est une classe générée par doctrine, qui permet de faire des sélections en BDD (SELECT). Pour cela, elle dispose de différentes méthodes: find(), findAll(), findByOne(), findBy().
         */

        /*Pour pouvoir utiliser le ProduitRepository, on a la possibilité de l'injecter en dépendance, c'est à dire, de l'entré en argument de la fonction. l' ArgumentResolver de symfony se chargera de m'instancier */

        $produits = $produitRepo->findAll();

        return $this->render('produit/index.html.twig', [
            'produits' => $produits
        ]);
    }
}
