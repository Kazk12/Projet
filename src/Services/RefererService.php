<?php

namespace App\Services;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;

class RefererService
{
    private RequestStack $requestStack;
    private RouterInterface $router;

    public function __construct(RequestStack $requestStack, RouterInterface $router)
    {
        $this->requestStack = $requestStack;
        $this->router = $router;
    }

    /**
     * Redirige vers le referer ou une route de secours.
     */
    public function referer(?string $fallbackRoute = null): RedirectResponse
    {
        return new RedirectResponse($this->getRefererUrl($fallbackRoute));
    }

    /**
     * Retourne l'URL du referer ou une route de secours.
     */
    public function getRefererUrl(?string $fallbackRoute = null): string
    {
        $request = $this->requestStack->getCurrentRequest();
        if ($request) {
            $referer = $request->headers->get('referer');
            if ($referer) {
                return $referer;
            }
        }
        $route = $fallbackRoute ?? 'app_home';
        return $this->router->generate($route);
    }
}