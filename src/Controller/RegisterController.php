<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterUserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class RegisterController extends AbstractController
{
    #[Route('/inscription', name: 'app_register')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = new User();

        $form = $this->createForm( RegisterUserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addflash(
                'success',
                "Votre compte est correctement créé, veuillez vous connecter."
            );

            return $this->redirectToRoute('app_login');

        }
        
        // si le formulaire est soumis alors :
        // tu enregistrer les datas en bdd
        // tu envoies un message de confirmation du compte bien créé

        return $this->render('register/index.html.twig', [
            'registerForm' => $form->createView()
        ]);
    }
}




// USE
// Je l'appelle
// NAMESPACE
// Je définis un répertoire