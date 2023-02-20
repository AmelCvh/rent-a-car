<?php

namespace App\Car;

use App\Car\Action\CarAction;
use App\Car\Action\MarqueAction;
use Core\Framework\Router\Router;
use Psr\Container\ContainerInterface;
use Core\Framework\Renderer\RendererInterface;
use Core\Framework\AbstractClass\AbstractModule;

    class CarModule extends AbstractModule
    {
        private Router  $router;
        private RendererInterface $renderer;

        public const DEFINITION = __DIR__.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'config.php';

        public function __construct(ContainerInterface $container)
        {
            $this->router = $container->get(Router::class);
            $this->renderer = $container->get(RendererInterface::class);
            $carAction = $container->get(CarAction::class);
            $marqueAction = $container->get(MarqueAction::class);


            $this->renderer->addPath('car',__DIR__.DIRECTORY_SEPARATOR.'view');
            $this->router->get('/admin/addCar',[$carAction,'addCar'], 'car.add');
            $this->router->get('/admin/listCar', [$carAction, 'listCar'], 'car.list');
            $this->router->get('/show/{id:[\d]+}', [$carAction, 'show'], 'car.show');
            $this->router->get('/admin/update/{id:[\d]+}', [$carAction, 'update'], 'car.update');
            $this->router->get('/admin/delete/{id:[\d]+}', [$carAction, 'delete'], 'car.delete');
            $this->router->post('/admin/update/{id:[\d]+}', [$carAction, 'update']);
            $this->router->post('/admin/addCar', [$carAction, 'addCar']);
            $this->router->get('/admin/addMarque',[$marqueAction, 'addMarque'], 'marque.add');
            $this->router->get('/admin/marqueList', [$marqueAction, 'marqueList'], 'marque.list');
            $this->router->post('/admin/addMarque',[$marqueAction, 'addMarque']);
            $this->router->get('/admin/delete/marque/{id:[\d]+}', [$marqueAction, 'delete'], 'marque.delete');
            $this->router->get('/admin/updateMarque/{id:[\d]+}', [$marqueAction, 'update'], 'marque.update');
            $this->router->post('/admin/updateMarque', [$marqueAction, 'update']);
        }
    }
?>
