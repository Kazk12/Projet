<?php

namespace App\Controller;

use App\DTO\AnnounceFilter;
use App\DTO\SearchCriteria;
use App\Entity\Announce;
use App\Entity\Comment;
use App\Form\UpdateUserType;
use App\Interfaces\PasswordUpdaterInterface;
use App\Repository\AnnounceRepository;
use App\Form\CommentType;
use App\Repository\UserRepository;
use App\Entity\User;
use App\Interfaces\CommentFormServiceInterface;
use App\Interfaces\SearchServiceInterface;
use App\Interfaces\UpdateProfilInterface;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(
        AnnounceRepository $announceRepository,
        PaginatorInterface $paginator,
        Request $request,
        EntityManagerInterface $entityManager,
        CommentFormServiceInterface $commentFormService

    ): Response {
        /** 
         * @var User $user
         */
        $user = $this->getUser();

        $queryBuilder = $announceRepository->findByUserStatus();

        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            6
        );

        $commentForms = [];
        foreach ($pagination as $announce) {
            $commentForms[$announce->getId()] = $commentFormService->createCommentForm($announce)->createView();
        }

        if ($request->isMethod('POST')) {
            $comment = new Comment();
            $form = $this->createForm(CommentType::class, $comment);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $announceId = $form->get('announceId')->getData();
                $announce = $announceRepository->find($announceId);

                $comment->setUser($user);
                $comment->setAnnounce($announce);
                $comment->setCreatedAt(new \DateTimeImmutable());

                $entityManager->persist($comment);
                $entityManager->flush();


                return $this->redirectToRefererOrHome($request);
            }
        }

        return $this->render('home/index.html.twig', [
            'pagination' => $pagination,
            'comment_forms' => $commentForms,
        ]);
    }

    #[Route('/update', name: 'app_user_update')]
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




            $emailPassword = $form->get('emailPassword')->getData();
            $newPassword = $form->get('newPassword')->getData();





            if ($emailPassword && $newPassword) {
                $passwordUpdater->updatePassword($user, $emailPassword, $newPassword);
            } elseif ($emailPassword || $newPassword) {
                $this->addFlash('danger', 'Email and password must be filled together to change password.');
            }


            $updateProfilService->updateProfil($user, $pseudo, $email);
            $this->addFlash('success', 'User updated successfully.');

            return $this->redirectToRoute('app_user_update');
        }

        return $this->render('user/update.html.twig', [
            'updateForm' => $form->createView(),
        ]);
    }








    #[Route('/search', name: 'app_search')]
    public function search(
        Request $request,
        PaginatorInterface $paginator,
        SearchServiceInterface $searchService,
        CommentFormServiceInterface $commentFormService
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

        // Récupération des genres pour le filtre
        $genres = $searchService->getAllGenres();

        return $this->render('home/search.html.twig', [
            'pagination' => $pagination,
            'comment_forms' => $commentForms,
            'query' => $searchCriteria->getQuery(),
            'selected_genre' => $searchCriteria->getGenre(),
            'genres' => $genres,
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
