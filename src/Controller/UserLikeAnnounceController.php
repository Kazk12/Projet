<?php

namespace App\Controller;

use App\Entity\Announce;
use App\Entity\UserLikeAnnounce;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class UserLikeAnnounceController extends AbstractController
{
    #[Route('/like/{id}', name: 'app_announce_like')]
    public function index(int $id, EntityManagerInterface $em): Response
    {
        /** 
         * @var User $user
         */
        $user = $this->getUser();

        if ($user === null) {
            return $this->redirectToRoute('app_login');
        }

        $announce = $em->getRepository(Announce::class)->find($id);

        if ($announce === null) {
            return $this->redirectToRoute('app_home');
        }

        $userLike = new UserLikeAnnounce();
        $userLike->setUser($user);
        $userLike->setAnnounce($announce);

        $em->persist($userLike);
        $em->flush();

        return $this->redirectToRoute('app_home');
    }
}
