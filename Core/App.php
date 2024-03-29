<?php

namespace Core;

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

        public function run(ServerRequestInterface $request): ResponseInterface{
            $uri = $request->getUri()->getPath();
            if(!empty($uri) && $uri[-1] === '/' && $uri != '/'){
                return (new Response())
                ->withStatus(301)
                ->withHeader('Location', substr($uri,0,-1));
            }

            $route = $this->router->match($request);

            if (is_null($route)) {
                return new Response(404, [],"<h2>Cette page n'existe pas</h2>");
            }

            $params = $route->getParams();

            $request = array_reduce(array_keys($params), function($request, $key) use ($params)
            {
                return $request->withAttribute($key, $params[$key]);
            },$request);

            $response = call_user_func_array($route->getCallback(), [$request]);
            
            if ($response instanceof ResponseInterface) {
                return $response;
            } elseif (is_string($response)) {
                return new Response(200,[],$response);
            } else {
                throw new \Exception("Reponse du serveur invalide");
            }

        }

        public function getContainer(): ContainerInterface
        {
            return $this->container;
        }

    }
    
?>
