<?php

namespace App\Controller;

use App\Form\UpdateUserType;
use App\Interfaces\PasswordUpdaterInterface;
use App\Repository\AnnounceRepository;
use App\Repository\UserRepository;
use App\Entity\User;
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
        Request $request
    ): Response
    {
        /** 
         * @var User $user
         */
        $user = $this->getUser();
        $queryBuilder = $announceRepository->createQueryBuilder('a');

        $pagination = $paginator->paginate(
            $queryBuilder, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/
        );

        return $this->render('home/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    #[Route('/update', name: 'app_user_update')]
    public function update(
        EntityManagerInterface $entityManager,
        UserRepository $userRepository,
        PasswordUpdaterInterface $passwordUpdater,
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
            $email = $form->get('email')->getData();
            $newPassword = $form->get('plainPassword')->getData();

            if ($email && $newPassword) {
                $passwordUpdater->updatePassword($user, $email, $newPassword);
            } elseif ($email || $newPassword) {
                $this->addFlash('danger', 'Email and password must be filled together to change password.');
            }

            $entityManager->flush();
            $this->addFlash('success', 'User updated successfully.');

            return $this->redirectToRoute('app_user_update');
        }
        return $this->render('user/update.html.twig', [
            'updateForm' => $form->createView(),
        ]);
    }
}