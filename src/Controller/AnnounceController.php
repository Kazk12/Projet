<?php

namespace App\Controller;

use App\Entity\Announce;
use App\Form\AnnounceType;
use App\Repository\AnnounceRepository;
use App\Services\RefererService;
use App\Services\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/announce')]
final class AnnounceController extends AbstractController
{
    public function __construct(
        private RefererService $refererService,
        private UserService $userService,
    ) {}

    #[Route('/mine', name: 'app_announce_mine', methods: ['GET'])]
    public function index(AnnounceRepository $announceRepository): Response
    {
        $user = $this->userService->getCurrentUser();
        return $this->render('announce/myAnnounces.html.twig', [
            'announces' => $announceRepository->findByUserId($user->getId()),
        ]);
    }

    #[Route('/new', name: 'app_announce_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->userService->getCurrentUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $announce = new Announce();
        $form = $this->createForm(AnnounceType::class, $announce);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $announce->setUser($user);

            $entityManager->persist($announce);
            $entityManager->flush();

            return $this->redirectToRoute('app_announce_mine', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('announce/new.html.twig', [
            'announce' => $announce,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_announce_show', methods: ['GET'])]
    public function show(Announce $announce): Response
    {
        $referer = $this->refererService->getRefererUrl('app_announce_mine');

        return $this->render('announce/show.html.twig', [
            'announce' => $announce,
            'referer' => $referer,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_announce_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Announce $announce, EntityManagerInterface $entityManager): Response
    {
        $user = $this->userService->getCurrentUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        if ($announce->getUser() !== $user) {
            $this->addFlash('warning', 'Vous n\'êtes pas autorisé à modifier cette annonce.');
            return $this->refererService->referer('app_announce_mine');
        }
        $form = $this->createForm(AnnounceType::class, $announce);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $announce->setUpdatedAt(new \DateTimeImmutable());

            $entityManager->flush();
            $this->addFlash('success', 'L\'annonce a été mise à jour avec succès.');

            return $this->refererService->referer();
        }

        return $this->render('announce/edit.html.twig', [
            'announce' => $announce,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_announce_delete', methods: ['POST'])]
    public function delete(Request $request, Announce $announce, EntityManagerInterface $entityManager): Response
    {
        $user = $this->userService->getCurrentUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        if ($announce->getUser() !== $user) {
            $this->addFlash('error', 'Vous n\'êtes pas autorisé à supprimer cette annonce.');
            return $this->refererService->referer('app_home');
        }

        if ($this->isCsrfTokenValid('delete' . $announce->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($announce);
            $entityManager->flush();
            $this->addFlash('success', 'L\'annonce a été supprimée avec succès.');
        }

        return $this->redirectToRoute('app_announce_mine');
    }
}