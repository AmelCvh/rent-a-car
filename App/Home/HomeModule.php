<?php
namespace App\Home;

use Core\Framework\Router\Router;
use Core\Framework\Renderer\RendererInterface;
use Core\Framework\AbstractClass\AbstractModule;


class HomeModule extends AbstractModule
{

    public const DEFINITION = __DIR__.DIRECTORY_SEPARATOR.'config' .DIRECTORY_SEPARATOR. 'config.php';

    private Router  $router;

    private RendererInterface  $renderer;


    public function __construct(Router $router,RendererInterface $renderer)
    {
        $this->router = $router;
        $this->renderer = $renderer;

        $this->renderer->addPath('Home', __DIR__.DIRECTORY_SEPARATOR.'view');
        $this->router->get('/', [$this,'index'], 'accueil');
    }
    
    public function index()
    {
        return $this->renderer->render('@Home/index', ['siteName' => 'rent-car']);
    }
}
?>