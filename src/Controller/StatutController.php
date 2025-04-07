<?php

namespace App\Controller;

use App\DTO\AnnounceFilter;
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


        
        if (!$user) {
            $this->addFlash('error', 'Vous devez être connecté pour accéder à cette page.');
            return $this->redirectToRoute('app_login');
        }
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

    #[Route('/friends', name: 'friends', methods: ['GET'])]
    public function friends(EntityManagerInterface $entityManager): Response
    {
        /**
         * @var User $user
         */
        $user = $this->getUser();
        $friends = $entityManager->getRepository(Statut::class)->findFriendsByUser();
        

        return $this->render('statut/friends.html.twig', [
            'friends' => $friends,
        ]);
    }

    #[Route('/blocked', name: 'blocked', methods: ['GET'])]
    public function blocked(EntityManagerInterface $entityManager): Response
    {
        /**
         * @var User $user
         */
        $user = $this->getUser();
       
        $blocked = $entityManager->getRepository(Statut::class)->findBlockedByUser();

        return $this->render('statut/blocked.html.twig', [
            'blocked' => $blocked,
        ]);
    }
    


    
}