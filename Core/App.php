<?php

namespace Core;

use Core\Framework\Middleware\MiddlewareInterface;
use Core\Framework\Renderer\PHPRenderer;
use Core\Framework\Router\Router;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Psr7\Response;
use Psr\Container\ContainerInterface;

// objet général pour la gestion du site 
    class App
    {
        private Router $router;

        private array $modules;

        private ContainerInterface $container;

        private MiddlewareInterface $middleware;

        public const DEFINITION = __DIR__.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'config.php';

        public function __construct(ContainerInterface $container, array $modules = [])
        {
            $this->router = $container->get(Router::class);

            foreach($modules as $module)
            {
                $this->modules[] = $container->get($module);
            }

            $this->container = $container;
        }

        public function run(ServerRequestInterface $request): ResponseInterface {

            return $this->middleware->process($request);
        }

        public function linkFirst(MiddlewareInterface $middleware): MiddlewareInterface
        {
            $this->middleware = $middleware;
            return $middleware;
        }

        public function getContainer(): ContainerInterface
        {
            return $this->container;
        }

    }
