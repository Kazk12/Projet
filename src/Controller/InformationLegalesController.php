<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class InformationLegalesController extends AbstractController
{
    #[Route('/politique-confidentialite', name: 'app_politique')]
    public function politique(): Response
    {
        return $this->render('informationLegales/politique.html.twig');
    }

    #[Route('/mentions-legales', name: 'app_mentions')]
    public function mentions(): Response
    {
        return $this->render('informationLegales/mentions.html.twig');
    }

    #[Route('/gestion-cookies', name: 'app_cookies')]
    public function gestionCookies(): Response
    {
        return $this->render('informationLegales/gestion.html.twig');
    }

    #[Route('/conditions-utilisation', name: 'app_conditions')]
    public function conditions(): Response
    {
        return $this->render('informationLegales/conditions.html.twig');
    }
}