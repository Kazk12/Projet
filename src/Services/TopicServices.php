<?php 

namespace App\Services;

use App\Entity\Conversation;
use Symfony\Component\HttpFoundation\RequestStack;

class TopicServices
{
    public function __construct(
        private readonly RequestStack $requestStack
    )
    {
        
    }

    public function getTopicUrl(Conversation $conversation) : string 
    {
        return "{$this->getServeurUrl()}/conversations/{$conversation->getId()}";
    }

    public function getServeurUrl(): string
    {
        $request = $this->requestStack->getMainRequest();

        $scheme = $request->getScheme();
        $host = $request->getHost();
        $port = $request->getPort();

        $portUrl = $port ? ":$port" : '';

        return $scheme . '://' . $host . $portUrl;

    }
        
}