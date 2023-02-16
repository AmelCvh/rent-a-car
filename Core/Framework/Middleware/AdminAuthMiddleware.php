<?php

namespace Core\Framework\Middleware;


use Core\Toaster\Toaster;
use GuzzleHttp\Psr7\Response;
use Core\Framework\Auth\AdminAuth;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Core\Framework\Middleware\AbstractMiddleware;

class AdminAuthMiddleware extends AbstractMiddleware
{
    private ContainerInterface $container;
    private  Toaster $toaster;

    public function __construct(ContainerInterface $container)
    {
        $this->container =$container;
        $this->toaster = $container->get(Toaster::class);
    }

    public function process(ServerRequestInterface $request)
    {
        $uri = $request->getUri()->getPath();
        if (str_starts_with($uri, '/admin') && $uri !== '/admin/login') 
        {
            $auth = $this->container->get(AdminAuth::class);
            if (!$auth->isLogged()) {
                if (!$auth->isAdmin()) {
                    $this->toaster->makeToast('Vous ne passerez paaaas!', Toaster::ERROR);
                } elseif (!$auth->isLogged()) {
                    $this->toaster->makeToast('Vous devez être connecté pour acceder à cette page', Toaster::ERROR);
                }
                return (new Response())
                    ->withHeader('Location', '/');
            }
        }
        return parent::process($request);
    }
}
