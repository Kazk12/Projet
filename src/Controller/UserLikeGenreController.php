<?php

namespace App\Controller;

use App\Entity\UserLikeGenre;
use App\Form\UserLikeGenreType;
use App\Repository\UserLikeGenreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


final class UserLikeGenreController extends AbstractController
{
    #[Route('/profile/my-genres', name: 'app_profile_my_genres')]
    public function myGenres(UserLikeGenreRepository $userLikeGenreRepository): Response
    {
        /** 
         * @var User $user
         */
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $myGenres = $userLikeGenreRepository->findBy(['user' => $user]);

        return $this->render('profil/myGenres.html.twig', [
            'myGenres' => $myGenres
        ]);
    }
}
