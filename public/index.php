<?php

use Core\App;
use App\Car\CarModule;
use App\Home\HomeModule;
use DI\ContainerBuilder;
use App\Admin\AdminModule;
use function Http\Response\send;
use GuzzleHttp\Psr7\ServerRequest;
use Core\Framework\Middleware\RouterMiddleware;
use Core\Framework\Middleware\NotFoundMiddleware;
use Core\Framework\Middleware\AdminAuthMiddleware;
use Core\Framework\Middleware\TraillingSlashMiddleware;
use Core\Framework\Middleware\RouterDispatcherMiddleware;


require dirname(__DIR__).'/vendor/autoload.php';

    $modules = [
        HomeModule::class,
        CarModule::class,
        AdminModule::class
    ];

    $builder = new ContainerBuilder();
    $builder->addDefinitions(dirname(__DIR__).DIRECTORY_SEPARATOR. 'config'. DIRECTORY_SEPARATOR.'config.php');

    foreach($modules as $module)
    {
        if(!is_null($module::DEFINITION))
        {
            $builder->addDefinitions($module::DEFINITION);
        }
    }

    $container = $builder->build();

    $app = new App($container, $modules);

    $app->linkFirst(new TraillingSlashMiddleware())
        ->linkWith(new RouterMiddleware($container))
        ->linkWith(new AdminAuthMiddleware($container))
        ->linkWith(new RouterDispatcherMiddleware())
        ->linkWith(new NotFoundMiddleware());
    
    if (php_sapi_name() !== 'cli') {
        $response = $app->run(ServerRequest::fromGlobals());
        send($response);
    }
?>
