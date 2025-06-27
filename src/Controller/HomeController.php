<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Repository\AnnounceRepository;
use App\Form\CommentType;
use App\Interfaces\CommentFormServiceInterface;
use App\Interfaces\LikeServiceInterface;
use App\Repository\StatutRepository;
use App\Services\RefererService;
use App\Services\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class HomeController extends AbstractController
{
    public function __construct(
        private RefererService $refererService,
        private UserService $userService,
    ) {}

    #[Route('/', name: 'app_home')]
    public function index(
        AnnounceRepository $announceRepository,
        StatutRepository $statutRepository,
        PaginatorInterface $paginator,
        Request $request,
        EntityManagerInterface $entityManager,
        CommentFormServiceInterface $commentFormService,
        LikeServiceInterface $likeService
    ): Response {
        $user = $this->userService->getCurrentUser();

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

        $likeInfo = $likeService->getLikeInfoForAnnounces($pagination->getItems(), $user);

        $userStatuses = [];
        if ($user) {
            $announceAuthors = [];
            foreach ($pagination as $announce) {
                $announceAuthors[] = $announce->getUser()->getId();
            }

            if (!empty($announceAuthors)) {
                $statuts = $statutRepository->findBy([
                    'user' => $user,
                    'otherUser' => $announceAuthors
                ]);

                foreach ($statuts as $statut) {
                    $userStatuses[$statut->getOtherUser()->getId()] = $statut->getStatut();
                }
            }
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

                return $this->refererService->referer();
            }
        }

        return $this->render('home/index.html.twig', [
            'pagination' => $pagination,
            'comment_forms' => $commentForms,
            'like_info' => $likeInfo,
            'user_statuses' => $userStatuses,
        ]);
    }
}