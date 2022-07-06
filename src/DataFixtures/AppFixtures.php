<?php

namespace App\DataFixtures;

use App\Entity\Produit;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {

        for($i = 1; $i <= 15; $i++)
        {
            //Les fixtures permettent de simuler un jeux de fausses données

            //Ici on simule un jeu de 15 produits
            //A chaque tour de boucle un instancoe (on créer) un nouveau produit, que l'on remplira avec les setters
            $produit = new Produit();

            $produit->setNom("Produit n° $i")
                    ->setDescription("Voici la description du produit $i")
                    ->setPrix(mt_rand(15, 89))
                    ->setImage("https://picsum.photos/id/" . mt_rand(12, 250) . "/300/160")
                    ->setStock(mt_rand(10, 100));

            $manager->persist($produit); //Ici on demande à manager de sauvegarder en cache, les produits que l'on créer à chaque tour
        }
       

        $manager->flush(); //Ici on demande à manager de balancer toutes ces données (produits précédement créés) en base de données.

        //Pour lancer remplir la bdd avec les fixtures => doctrine:fixtures:load
    }
}
