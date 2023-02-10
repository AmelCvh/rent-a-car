<?php

use Core\App;
use App\Car\CarModule;
use App\Home\HomeModule;
use function Http\Response\send;
use DI\ContainerBuilder;


use GuzzleHttp\Psr7\ServerRequest;
use Core\Framework\Renderer\PHPRenderer;


require dirname(__DIR__).'/vendor/autoload.php';

    $modules = [
        HomeModule::class,
        CarModule::class
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
    
    if (php_sapi_name() !== 'cli') {
        $response = $app->run(ServerRequest::fromGlobals());
        send($response);
    }
?>
