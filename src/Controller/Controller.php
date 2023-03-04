<?php

namespace App\Controller;

use App\Service\RateLimiter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class Controller extends AbstractController
{
    public function __construct(
        private readonly RateLimiter $limiter
    ) {
    }

    public function number(): Response
    {
        if ($this->limiter->isOk($_SERVER['REQUEST_URI'])) {
            return new Response(
                '<html><body>Works</body></html>'
            );
        }

        return new Response(
            '<html><body>Limits</body></html>'
        );
    }
}
