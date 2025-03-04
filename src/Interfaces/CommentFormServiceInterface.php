<?php

namespace App\Interfaces;

use Symfony\Component\Form\FormInterface;
use App\Entity\Announce;

interface CommentFormServiceInterface
{
    public function createCommentForm(Announce $announce): FormInterface;
}