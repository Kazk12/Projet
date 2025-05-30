<?php

namespace App\Services;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;

class RefererService
{
    private RouterInterface $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * Redirige vers le referer ou une route de secours.
     */
    public function referer(Request $request, ?string $fallbackRoute = null): RedirectResponse
    {
        return new RedirectResponse($this->resolveRefererUrl($request, $fallbackRoute));
    }

    /**
     * Retourne l'URL du referer ou une route de secours.
     */
    public function getRefererUrl(Request $request, ?string $fallbackRoute = null): string
    {
        return $this->resolveRefererUrl($request, $fallbackRoute);
    }

    /**
     * Méthode interne : calcule l'URL finale à utiliser comme referer.
     */
    private function resolveRefererUrl(Request $request, ?string $fallbackRoute): string
    {
        $referer = $request->headers->get('referer');

        if ($referer) {
            return $referer;
        }

        $route = $fallbackRoute ?? 'app_home';
        return $this->router->generate($route);
    }
}
