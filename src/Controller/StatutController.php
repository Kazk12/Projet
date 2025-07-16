<?php

namespace App\Controller;

use App\Entity\Statut;
use App\Entity\User;
use App\Repository\StatutRepository;
use App\Services\RefererService;
use App\Services\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class StatutController extends AbstractController
{
    public function __construct(
        private RefererService $refererService,
        private UserService $userService,
    ) {}

    #[Route('/statut/{id}/{statut}', name: 'app_statut', methods: ['GET'])]
    public function index(int $id, string $statut, EntityManagerInterface $entityManager): Response
    {
        $user = $this->userService->getCurrentUser();

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

        if ($statut === 'Unblocked') {
            if ($existingStatut) {
                $entityManager->remove($existingStatut);
                $entityManager->flush();
                $this->addFlash('success', 'Utilisateur débloqué avec succès.');
            }
            return $this->refererService->referer();
        }

        if ($statut === 'Unfriend') {
            if ($existingStatut) {
                $entityManager->remove($existingStatut);
                $entityManager->flush();
                $this->addFlash('success', 'Utilisateur retiré de vos amis.');
            }
            return $this->refererService->referer();
        }

        // Pour Friend ou Blocked
        if ($existingStatut) {
            $existingStatut->setStatut($statut);
        } else {
            $statutEntity = new Statut();
            $statutEntity->setOtherUser($otherUser);
            $statutEntity->setStatut($statut);
            $statutEntity->setUser($user);
            $entityManager->persist($statutEntity);
        }
        $entityManager->flush();

        if ($statut === 'Friend') {
            $this->addFlash('success', 'Utilisateur ajouté à vos amis.');
        } elseif ($statut === 'Blocked') {
            $this->addFlash('success', 'Utilisateur bloqué avec succès.');
        }

        return $this->refererService->referer();
    }

    #[Route('/friends', name: 'friends', methods: ['GET'])]
    public function friends(StatutRepository $statutRepository): Response
    {
        $friends = $statutRepository->findFriendsByUser();

        return $this->render('profil/friends.html.twig', [
            'friends' => $friends,
        ]);
    }

    #[Route('/blocked', name: 'blocked', methods: ['GET'])]
    public function blocked(StatutRepository $statutRepository): Response
    {
        $blocked = $statutRepository->findBlockedByUser();

        return $this->render('profil/blocked.html.twig', [
            'blocked' => $blocked,
        ]);
    }
}