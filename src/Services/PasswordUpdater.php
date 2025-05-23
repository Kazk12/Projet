<?php

namespace App\Services;

use App\Entity\User;
use App\Interfaces\PasswordUpdaterInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;
use Symfony\Component\HttpFoundation\Session\Session;

class PasswordUpdater implements PasswordUpdaterInterface
{
    private UserPasswordHasherInterface $passwordHasher;
    private MailerInterface $mailer;
    private RequestStack $requestStack;

    public function __construct(UserPasswordHasherInterface $passwordHasher, MailerInterface $mailer, RequestStack $requestStack)
    {
        $this->passwordHasher = $passwordHasher;
        $this->mailer = $mailer;
        $this->requestStack = $requestStack;
    }

    /**
     * Updates the user's password and sends a confirmation email.
     *
     * @param User   $user        The user entity
     * @param string $email       The user's email
     * @param string $newPassword The new password
     */
    public function updatePassword(User $user, string $email, string $newPassword): void
    {
        /** @var Session $session */
        $session = $this->requestStack->getSession();

        /** @var FlashBag $flashBag */
        $flashBag = $session->getFlashBag();

        if ($user->getEmail() !== $email) {
            $flashBag->add('danger', 'L\'adresse e-mail saisie ne correspond pas à celle associée à votre compte.');
            return;
        }

        $hashedPassword = $this->passwordHasher->hashPassword($user, $newPassword);
        $user->setPassword($hashedPassword);

        try {
            
            $mail = (new TemplatedEmail())
                ->from('support@luxury-services.com')
                ->to($user->getEmail())
                ->subject('Changement de mot de passe')
                ->htmlTemplate('partials/motPasse.html.twig')
                ->context([
                    'user' => $user,
                    'date' => new \DateTime()
                ]);


            $this->mailer->send($mail);
            $flashBag->add('success', 'Votre mot de passe a été modifié avec succès !');
        } catch (\Exception $e) {
            $flashBag->add('danger', 'Une erreur est survenue lors de l\'envoi du message : ' . $e->getMessage());
        }
    }
}