<?php

namespace App\Controller;

use App\DTO\SearchCriteria;
use App\Entity\User;
use App\Interfaces\CommentFormServiceInterface;
use App\Interfaces\LikeServiceInterface;
use App\Interfaces\SearchServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class SearchController extends AbstractController
{
    #[Route('/search', name: 'app_search')]
    public function search(
        Request $request,
        PaginatorInterface $paginator,
        SearchServiceInterface $searchService,
        CommentFormServiceInterface $commentFormService,
        EntityManagerInterface $entityManager,
        LikeServiceInterface $likeService
    ): Response {
        /** @var User $user */
        $user = $this->getUser();

        // Création du DTO pour les critères de recherche
        $searchCriteria = new SearchCriteria(
            $request->query->get('query'),
            $request->query->get('genre'),
            $user ? $user->getId() : null
        );

        // Utilisation du service pour obtenir le queryBuilder
        $queryBuilder = $searchService->getSearchQueryBuilder($searchCriteria);

        // Pagination des résultats
        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            6
        );

        // Génération des formulaires de commentaire
        $commentForms = [];
        foreach ($pagination as $announce) {
            $commentForms[$announce->getId()] = $commentFormService->createCommentForm($announce)->createView();
        }
        
        // Récupération des informations de like en une seule requête
        $likeInfo = $likeService->getLikeInfoForAnnounces($pagination->getItems(), $user);
        
        // Récupération des statuts de relation entre l'utilisateur connecté et les auteurs des annonces
        $userStatuses = [];
        if ($user) {
            $announceAuthors = [];
            foreach ($pagination as $announce) {
                $announceAuthors[] = $announce->getUser()->getId();
            }
            
            if (!empty($announceAuthors)) {
                $statuts = $entityManager->getRepository(\App\Entity\Statut::class)->findBy([
                    'user' => $user,
                    'otherUser' => $announceAuthors
                ]);
                
                foreach ($statuts as $statut) {
                    $userStatuses[$statut->getOtherUser()->getId()] = $statut->getStatut();
                }
            }
        }

        // Récupération des genres pour le filtre
        $genres = $searchService->getAllGenres();

        return $this->render('home/search.html.twig', [
            'pagination' => $pagination,
            'comment_forms' => $commentForms,
            'query' => $searchCriteria->getQuery(),
            'selected_genre' => $searchCriteria->getGenre(),
            'genres' => $genres,
            'like_info' => $likeInfo,
            'user_statuses' => $userStatuses,
        ]);
    }
}