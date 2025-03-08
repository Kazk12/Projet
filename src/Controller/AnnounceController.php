<?php

namespace App\Controller;

use App\Entity\Announce;
use App\Entity\User;
use App\Form\AnnounceType;
use App\Repository\AnnounceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/announce')]
final class AnnounceController extends AbstractController
{
    #[Route('/mine',name: 'app_announce_mine', methods: ['GET'])]
    public function index(AnnounceRepository $announceRepository): Response
    {
         /**
         *  @var User $user
         */
        $user = $this->getUser();
        return $this->render('announce/index.html.twig', [
            'announces' => $announceRepository->findByUserId($user->getId()),
        ]);
    }

    #[Route('/new', name: 'app_announce_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {



        /**
         *  @var User $user
         */
        $user = $this->getUser();
        if(!$user){
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
    public function show(Request $request, Announce $announce): Response
    {
        $referer = $request->headers->get('referer') ?? $this->generateUrl('app_home');
        
        return $this->render('announce/show.html.twig', [
            'announce' => $announce,
            'referer' => $referer,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_announce_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Announce $announce, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AnnounceType::class, $announce);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_announce_mine', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('announce/edit.html.twig', [
            'announce' => $announce,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_announce_delete', methods: ['POST'])]
    public function delete(Request $request, Announce $announce, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$announce->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($announce);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_announce_mine', [], Response::HTTP_SEE_OTHER);
    }
}
