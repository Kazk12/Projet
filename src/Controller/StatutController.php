<?php

namespace App\Controller;

use App\Entity\Statut;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class StatutController extends AbstractController
{
    #[Route('/statut/{id}/{statut}', name: 'app_statut', methods: ['GET'])]
    public function index(int $id, string $statut, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $otherUser = $entityManager->getRepository(User::class)->find($id);

        if (!$otherUser) {
            throw $this->createNotFoundException('User not found');
        }

        
        $statutRepository = $entityManager->getRepository(Statut::class);
        $existingStatut = $statutRepository->findOneBy([
            'user' => $user,
            'otherUser' => $otherUser
        ]);

        if ($existingStatut) {
            // Mettre à jour le statut existant
            $existingStatut->setStatut($statut);
        } else {
            // Créer une nouvelle entité Statut
            $statutEntity = new Statut();
            $statutEntity->setOtherUser($otherUser);
            $statutEntity->setStatut($statut);
            $statutEntity->setUser($user);

            $entityManager->persist($statutEntity);
        }

        $entityManager->flush();

        return $this->redirectToRoute('app_home');
    }
}