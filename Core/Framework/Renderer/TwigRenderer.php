<?php

namespace Core\Framework\Renderer;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

    class TwigRenderer implements RendererInterface{
        private $twig;
        private $loader;

        public function __construct(FilesystemLoader $loader, Environment $twig)
        {
            $this->loader = $loader;
            $this->twig = $twig;
        }
        
        public function addPath(string $namespace, ?string $path = null ):void
        {
            $this->loader->addPath($path, $namespace);
        }

        // ?string $path = null // Il s'attend a un path sinon = null 

        public function render(string $view, array $params = []): string
        {
            return $this->twig->render($view.'.html.twig', $params);
        }
        
        public function addGlobal(string $key, $value): void
        {
            $this->twig->addGlobal($key, $value);
        }
    }
?>
