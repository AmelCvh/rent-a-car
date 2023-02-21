<?php

namespace Core\Framework\Middleware;

use Core\Framework\Auth\UserAuth;
use Core\Framework\Router\Router;
use Psr\Container\ContainerInterface;
use Core\Framework\Middleware\AbstractMiddleware;
use Core\Framework\Router\RedirectTrait;
use Core\Toaster\Toaster;
use Psr\Http\Message\ServerRequestInterface;

class UserAuthMiddleware extends AbstractMiddleware
{ 
    use RedirectTrait;
    private ContainerInterface $container;
    private Router $router;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->router = $container->get(Router::class);
    }

    
    public function process(ServerRequestInterface $request)
    {
        $uri = $request->getUri()->getPath();
        if (str_starts_with($uri, '/user')) {
            $auth = $this->container->get(UserAuth::class);
            if (!$auth->isLogged() || !$auth->isUser()) {
                $toaster = $this->container->get(Toaster::class);
                $toaster->makeToast("Veuillez vous connecté pour continuer", Toaster::ERROR);
                return $this->redirect('user.login');
            }
        }
        return parent::process($request); //permet d'appeler le middleware suivant;
    }
}
