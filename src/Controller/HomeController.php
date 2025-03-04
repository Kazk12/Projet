<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\UpdateUserType;
use App\Interfaces\PasswordUpdaterInterface;
use App\Repository\AnnounceRepository;
use App\Form\CommentType;
use App\Repository\UserRepository;
use App\Entity\User;
use App\Interfaces\CommentFormServiceInterface;
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

    ): Response
    {
        /** 
         * @var User $user
         */
        $user = $this->getUser();
        $queryBuilder = $announceRepository->createQueryBuilder('a');

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

                return $this->redirectToRoute('app_home');
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
        UpdateProfilInterface $updateProfilService,
        Request $request

    ): Response
    {
        /** 
         * @var User $user
         */
        $user = $this->getUser();

        $form = $this->createForm(UpdateUserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $pseudo = $form->get('pseudo')->getData();
            $plainPassword = $form->get('plainPassword')->getData(); // Ceci contient déjà la valeur validée
            $email = $form->get('email')->getData();
            
            $updateProfilService->updateProfil($user, $pseudo, $email, $plainPassword);
            $this->addFlash('success', 'User updated successfully.');
            
            return $this->redirectToRoute('app_user_update');
        }

        return $this->render('user/update.html.twig', [
            'updateForm' => $form->createView(),
        ]);
    }
}