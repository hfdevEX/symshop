<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Form\ProduitFormType;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="app_admin")
     */
    public function index(): Response
    {
        return $this->render('admin/index.html.twig');
    }

    /**
     * @Route("/admin/produits", name="app_admin_produits")
     */
    public function adminProduits(ProduitRepository $produitRepo, EntityManagerInterface $manager): Response
    {

        //Grace au manager on peut aller récupérer les champs de la table désiré ('Produit')
        $colonnes = $manager->getClassMetadata(Produit::class)->getFieldNames();
        // dd($colonnes);

        //Récupérer tous les produits présents en bdd avec le répository (ProduitRepository)

        $produits = $produitRepo->findAll();

        return $this->render("admin/produits.html.twig", [
            'colonnes' => $colonnes,
            'produits' => $produits
        ]);
    }


    /**
     * @Route("/admin/produits/ajouter", name="app_produit_ajouter")
     */
    public function ajouterProduit(Request $request, EntityManagerInterface $manager) : Response
    {

        $produit = new Produit;

        $formProduit = $this->createForm(ProduitFormType::class, $produit);

        $formProduit->handleRequest($request);

        if($formProduit->isSubmitted() && $formProduit->isValid())
        {

            $manager->persist($produit);

            $manager->flush();

            $this->addFlash('success', "Le produit n° " . $produit->getId() ." a bien été ajouté");

           return $this->redirectToRoute("app_admin_produits");
        }

        return $this->render("admin/produit_ajouter.html.twig", [
            'formProduit' => $formProduit->createView()
        ]);
    }


    /**
     * @Route("/admin/produits/editer/{id}", name="app_produit_editer")
     */
    public function editerProduit(Produit $produit, Request $request, EntityManagerInterface $manager) : Response
    {
            $id = $produit->getId();

            $form = $this->createForm(ProduitFormType::class, $produit);

            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid())
            {
                $manager->flush();

                $this->addFlash("info", "Le produit n° $id a bien été modifié");

                return $this->redirectToRoute("app_admin_produits");
            }

        return $this->render("admin/produit_editer.html.twig", [
            'form' => $form->createView(),
            'id' => $id
        ]);
    }


    /**
     * @Route("/admin/produits/supprimer/{id}", name="app_produit_supp")
     */
    public function suppProduit(Produit $produit, EntityManagerInterface $manager): Response
    {
        $manager->remove($produit);
        $manager->flush();

        $this->addFlash('danger', "Le produit " . $produit->getNom() . " a bien été supprimé");

        return $this->redirectToRoute('app_admin_produits');
    }

}
