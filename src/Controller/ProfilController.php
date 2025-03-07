<?php

namespace App\Controller;

use App\Entity\Announce;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;



final class ProfilController extends AbstractController
{

    #[Route('/profil', name: 'app_profil')]
    public function profil(EntityManagerInterface $em): Response
    {

        $myAnnounce = $em->getRepository(Announce::class)->countByUserNumberOfAnnounces($this->getUser()->getId());

        return $this->render('profil/profil.html.twig', [
            'myAnnounce' => $myAnnounce
        ]);
    }

    



}