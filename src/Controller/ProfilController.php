<?php

namespace App\Controller;

use App\Entity\Announce;
use App\Entity\User;
use App\Form\UpdateUserType;
use App\Interfaces\PasswordUpdaterInterface;
use App\Interfaces\UpdateProfilInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class ProfilController extends AbstractController
{
    #[Route('/profil', name: 'app_profil')]
    public function profil(EntityManagerInterface $em): Response
    {
        /** 
         * @var User $user
         */
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
        $myAnnounce = $em->getRepository(Announce::class)->countByUserNumberOfAnnounces($user->getId());

        return $this->render('profil/profil.html.twig', [
            'myAnnounce' => $myAnnounce
        ]);
    }

    #[Route('/profil/update', name: 'app_profil_update')]
    public function update(
        EntityManagerInterface $entityManager,
        PasswordUpdaterInterface $passwordUpdater,
        UpdateProfilInterface $updateProfilService,
        Request $request
    ): Response {
        /** 
         * @var User $user
         */
        $user = $this->getUser();

        $form = $this->createForm(UpdateUserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $pseudo = $form->get('pseudo')->getData();
            $email = $form->get('email')->getData();
            $photoFile = $form->get('photoFile')->getData();
            $emailPassword = $form->get('emailPassword')->getData();
            $newPassword = $form->get('newPassword')->getData();

            if ($emailPassword && $newPassword) {
                $passwordUpdater->updatePassword($user, $emailPassword, $newPassword);
            } elseif ($emailPassword || $newPassword) {
                $this->addFlash('danger', 'L\'email et le mot de passe doivent être remplis ensemble pour changer le mot de passe.');
            }

            $updateProfilService->updateProfil($user, $pseudo, $email, $photoFile);
           
            $this->addFlash('success', 'Vos informations ont bien été mises à jour.');

            return $this->redirectToRoute('app_profil_update');
        }

        return $this->render('profil/update.html.twig', [
            'updateForm' => $form->createView(),
        ]);
    }

    
}