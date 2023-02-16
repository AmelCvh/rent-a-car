<?php

namespace Core\Framework\Middleware;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface;
use Core\Framework\Middleware\AbstractMiddleware;

class NotFoundMiddleware extends AbstractMiddleware
{
    public function process(ServerRequestInterface $request) 
    {
        return new Response(404, [], "Erreur 404");
    }
}