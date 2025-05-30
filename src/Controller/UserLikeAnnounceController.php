<?php

namespace App\Controller;

use App\Entity\Announce;
use App\Entity\UserLikeAnnounce;
use App\Entity\User;
use App\Repository\UserLikeAnnounceRepository;
use App\Services\RefererService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class UserLikeAnnounceController extends AbstractController
{

    public function __construct(private RefererService $refererService){}

    #[Route('/like/{id}', name: 'app_announce_like')]
    public function index(int $id, EntityManagerInterface $em, Request $request): Response
    {
        /** 
         * @var User $user
         */
        $user = $this->getUser();
    
        if ($user === null) {
            return $this->redirectToRoute('app_login');
        }
    
        try {
            $announce = $em->getRepository(Announce::class)->find($id);
    
            if ($announce === null) {
                return $this->redirectToRoute('app_home');
            }
    
            // Vérifier si l'utilisateur a déjà liké cette annonce
            $existingLike = $em->getRepository(UserLikeAnnounce::class)->findOneBy([
                'user' => $user,
                'announce' => $announce
            ]);
    
            if ($existingLike) {
                $em->remove($existingLike);
                $this->addFlash('success', 'Like retiré avec succès');
            } else {
                $userLike = new UserLikeAnnounce();
                $userLike->setUser($user);
                $userLike->setAnnounce($announce);
                $em->persist($userLike);
                $this->addFlash('success', 'Annonce likée avec succès');
            }
            
            $em->flush();
            
            return $this->refererService->referer($request);

        } catch (\Exception $e) {
            $this->addFlash('error', 'Une erreur est survenue');
            return $this->redirectToRoute('app_home');
        }
    }

    #[Route('/user/likes', name: 'user_likes')]
    public function show(UserLikeAnnounceRepository $userLikeAnnounceRepository): Response
    {
        /** 
         * @var User $user 
         */
        $user = $this->getUser();
        
        if (!$user) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour voir cette page.');
        }
            $likedAnnounces = $userLikeAnnounceRepository->findLikedAnnouncesByUser($user->getId());       
            return $this->render('profil/likes.html.twig', [
                'likes' => $likedAnnounces,
            ]);   
    }    
}
