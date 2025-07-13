<?php

namespace App\Controller;

use App\Repository\BookRepository;
use App\Services\RefererService;
use App\Services\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;

final class TestController extends AbstractController{
    public function __construct(
        private RefererService $refererService,
        private UserService $userService,
    ) {}

    #[Route('/test', name: 'app_test')]
    public function index(BookRepository $bookRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $query = $bookRepository->createQueryBuilder('b')->orderBy('b.createdAt', 'DESC');
        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            9
        );

        return $this->render('test/index.html.twig', [
            'controller_name' => 'TestController',
            'pagination' => $pagination,
        ]);
    }
}
