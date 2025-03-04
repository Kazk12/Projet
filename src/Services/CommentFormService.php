<?php

namespace App\Services;

use App\Entity\Announce;
use App\Entity\Comment;
use App\Form\CommentType;
use App\Interfaces\CommentFormServiceInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

class CommentFormService implements CommentFormServiceInterface
{
    private FormFactoryInterface $formFactory;

    public function __construct(FormFactoryInterface $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    public function createCommentForm(Announce $announce): FormInterface
    {
        $comment = new Comment();
        $comment->setAnnounce($announce);

        return $this->formFactory->create(CommentType::class, $comment, [
            'announce_id' => $announce->getId()
        ]);
    }
}