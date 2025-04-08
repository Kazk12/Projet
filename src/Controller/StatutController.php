<?php

namespace App\Controller;

use App\DTO\AnnounceFilter;
use App\Entity\Statut;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class StatutController extends AbstractController
{
    #[Route('/statut/{id}/{statut}', name: 'app_statut', methods: ['GET'])]
    public function index(int $id, string $statut, EntityManagerInterface $entityManager, request $request): Response
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

        if($statut === 'Débloquer') {
            if($existingStatut){
                $entityManager->remove($existingStatut);
                $entityManager->flush();
                $this->addFlash('success', 'Utilisateur débloqué avec succès.');
            }
        }

        if ($statut === 'Unfriend') {
            if ($existingStatut) {
                $entityManager->remove($existingStatut);
                $entityManager->flush();
                $this->addFlash('success', 'Utilisateur retiré de vos amis.');
            }
        } else {
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
            
            if ($statut === 'Friend') {
                $this->addFlash('success', 'Utilisateur ajouté à vos amis.');
            } elseif ($statut === 'Blocked') {
                $this->addFlash('success', 'Utilisateur bloqué avec succès.');
            } elseif ($statut === 'Unblocked') {
                $this->addFlash('success', 'Utilisateur débloqué avec succès.');
            }
        }

        return $this->redirectToRefererOrHome($request);
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

        // dd($blocked);

        return $this->render('statut/blocked.html.twig', [
            'blocked' => $blocked,
        ]);
    }


    private function redirectToRefererOrHome(Request $request): Response
    {
        $referer = $request->headers->get('referer');

        if ($referer) {
            return $this->redirect($referer);
        }

        return $this->redirectToRoute('app_home');
    }
}