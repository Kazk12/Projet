<?php

namespace App\Controller;

use App\Repository\BookRepository;
use App\Services\RefererService;
use App\Services\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class TestController extends AbstractController{
    public function __construct(
        private RefererService $refererService,
        private UserService $userService,
    ) {}
    #[Route('/test', name: 'app_test')]
    public function index(BookRepository $bookRepository): Response
    {
        $books = $bookRepository->findAll();

        return $this->render('test/index.html.twig', [
            'controller_name' => 'TestController',
            'books' => $books,
        ]);
    }
}
