<?php

namespace App\Controller;

use App\Entity\Genre;
use App\Entity\User;
use App\Entity\UserLikeGenre;
use App\Repository\GenreRepository;
use App\Repository\UserLikeGenreRepository;
use App\Services\RefererService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class GenreController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private GenreRepository $genreRepository,
        private UserLikeGenreRepository $userLikeGenreRepository,
        private RefererService $refererService,
    ) {}

    #[Route('/genres', name: 'app_genres_list')]
    public function index(): Response
    {
        $genres = $this->genreRepository->findAll();

        /** @var User $user */
        $user = $this->getUser();

        $userGenreLikes = [];
        if ($user) {
            $userLikeGenres = $this->userLikeGenreRepository->findBy(['user' => $user]);
            foreach ($userLikeGenres as $userLikeGenre) {
                $userGenreLikes[$userLikeGenre->getGenre()->getId()] = true;
            }
        }

        return $this->render('genre/index.html.twig', [
            'genres' => $genres,
            'userGenreLikes' => $userGenreLikes
        ]);
    }

    #[Route('/genre/{id}/toggle-like', name: 'app_genre_toggle_like', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function toggleGenreLike(Genre $genre): Response
    {
        $user = $this->getUser();
        $existingLike = $this->userLikeGenreRepository->findOneBy([
            'user' => $user,
            'genre' => $genre
        ]);

        if ($existingLike) {
            $this->entityManager->remove($existingLike);
            $this->addFlash('success', 'Genre retiré de vos favoris !');
        } else {
            $userLikeGenre = new UserLikeGenre();
            $userLikeGenre->setUser($user);
            $userLikeGenre->setGenre($genre);

            $this->entityManager->persist($userLikeGenre);
            $this->addFlash('success', 'Genre ajouté à vos favoris !');
        }

        $this->entityManager->flush();

        return $this->refererService->referer();
    }
}