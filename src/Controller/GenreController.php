<?php

namespace App\Controller;

use App\Entity\Genre;
use App\Entity\UserLikeGenre;
use App\Repository\GenreRepository;
use App\Repository\UserLikeGenreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class GenreController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private GenreRepository $genreRepository;
    private UserLikeGenreRepository $userLikeGenreRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        GenreRepository $genreRepository,
        UserLikeGenreRepository $userLikeGenreRepository
    ) {
        $this->entityManager = $entityManager;
        $this->genreRepository = $genreRepository;
        $this->userLikeGenreRepository = $userLikeGenreRepository;
    }

    #[Route('/genres', name: 'app_genres_list')]
    public function index(): Response
    {
        $genres = $this->genreRepository->findAll();
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
    public function toggleGenreLike(Genre $genre, Request $request): Response
    {
        $user = $this->getUser();
        $existingLike = $this->userLikeGenreRepository->findOneBy([
            'user' => $user,
            'genre' => $genre
        ]);

        if ($existingLike) {
            // Si l'utilisateur a déjà liké ce genre, on supprime le like
            $this->entityManager->remove($existingLike);
            $this->entityManager->flush();
            
            $this->addFlash('success', 'Genre retiré de vos favoris !');
        } else {
            // Sinon, on ajoute le like
            $userLikeGenre = new UserLikeGenre();
            $userLikeGenre->setUser($user);
            $userLikeGenre->setGenre($genre);
            
            $this->entityManager->persist($userLikeGenre);
            $this->entityManager->flush();
            
            $this->addFlash('success', 'Genre ajouté à vos favoris !');
        }

        $referer = $request->headers->get('referer');
        return $this->redirect($referer ?: $this->generateUrl('app_genres_list'));
    }
}
