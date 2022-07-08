<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\InscriptionFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
         if ($this->getUser()) {
             return $this->redirectToRoute('app_home');
         }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }


    /**
     * @Route("/inscription", name="app_inscription")
     */
    public function inscription(Request $request, UserPasswordHasherInterface $encoder, EntityManagerInterface $manager): Response
    {
        //On va créer un nouvel exemplaire de l'entité User afin de pouvoir remplir l'objet via le formulaire, puis l'insérer en BDD
        $user = new User;

        //On execute la méthode createForm() fournit par la classe AbstractController

        // dd($request);

        $formInscription = $this->createForm(InscriptionFormType::class, $user);

        /**
         * handleRequest() : méthode du formulaire (createForm()), qui permet au formulaire de se gorger des informations qui ont été transmises via la request
         */
        $formInscription->handleRequest($request);

        if($formInscription->isSubmitted() && $formInscription->isValid())
        {
            //Si le formulaire a été correctement rempli, bien validé (est-ce que chaque donnée saisie a bien été transmis au bon setter), alors on peut faire faire le traitement

            //Ici on hash le mdp saisie par l'utilisateur
            $passwordHash = $encoder->hashPassword($user, $user->getPassword());

            dump($passwordHash);

            //On écrase son mdp saisie par le mdp hashé (crypté)
            $user->setPassword($passwordHash);

            $user->setRoles(["ROLE_USER"]);

            dump($user);

            //Sauvegarger en cache les données
            $manager->persist($user);

            //On insert en BDD
            $manager->flush();

            //Fonction addFlash() permet d'enregistré un message en session
            $this->addFlash("success", "Félicitation, votre comptre est créer vous pouvez dès à présent vous connecter");

            //Une fois inscrit, on redirige l'utilisateur vers la page de connexion

            return $this->redirectToRoute("app_login");

        }

        // dd($user);


        return $this->render("security/inscription.html.twig", [
            'form' => $formInscription->createView()
        ]);
    }


    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
