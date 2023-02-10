<?php
namespace Core\Framework\Renderer;

    interface RendererInterface
    {

        public function addPath(string $namespace, ?string $path = null): void;

        public function render(string $view, array $params = []): string;

        public function addGlobal(string $key, $value): void;

    }
    //Ceci est l'interface RendererInterface pour un moteur de rendu dans une application PHP. Les méthodes définies dans cette interface incluent :

// addPath: cette méthode prend en entrée un namespace et un chemin de vue facultatif, et enregistre le chemin de vue associé au namespace donné.

// render: cette méthode prend en entrée le nom d'une vue et un tableau de paramètres facultatif, et renvoie le contenu de la vue rendue en tant que chaîne de caractères.

// addGlobal: cette méthode prend en entrée une clé et une valeur, et enregistre la valeur donnée en tant que variable globale accessible par toutes les vues rendues par ce moteur de rendu.

?>
