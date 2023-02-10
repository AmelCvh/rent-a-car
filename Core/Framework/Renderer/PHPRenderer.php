<?php 
namespace Core\Framework\Renderer;


class PHPRenderer implements RendererInterface
{

    const DEFAULT_NAMESPACE = "__MAIN";

    private array $path = [];

    private array $globals = [];

    public function __construct(string $defaultPath = null)
    {
        if(!is_null($defaultPath)) {
            $this->addPath($defaultPath);
        }
    }

    // Ceci est un méthode, une "action" que l'objet pourra effectuer
    public function addPath(string $namespace, ?string $path = null): void
    {
        if (is_null($path)){
            $this->path[self::DEFAULT_NAMESPACE]= $namespace;
        } else {
            $this->path[$namespace] = $path;
        }
    }

    // $renderer->render('@blog\addVehicule') || @ permet de pointer le namespace
    // $renderer->render('header')
    // $renderer->render('test', [$name->'sososo'])

    // Ceci est une méthode, une "action" que l'objet pourra effectuer
    public function render(string $view, array $params = []): string
    {
        if ($this->hasNamespace($view))
        {
            $path = $this->replayNamespace($view).'.php';
        } else {
            $path = $this->path[self::DEFAULT_NAMESPACE].DIRECTORY_SEPARATOR.$view.'.php';
        }
        ob_start();
        $renderer = $this;
        extract($this->globals);
        extract($params);
        require($path);
        return ob_get_clean();
    }

        public function addGlobal(string $key, $value): void
        {
            $this->globals[$key] = $value;
        }
        
    
        private function hasNamespace(string $view): bool
        {
        return $view[0] === '@';
        }

        private function replayNamespace(string $view): string
        {
            $namespace = substr($view, 1,strpos($view, '/')-1);
            $str = str_replace('@'.$namespace,$this->path[$namespace],$view);
            return str_replace('/','\\',$str);
        }

    }
// Ce code définit la classe "PHPRenderer" dans l'espace de nom "Core\Framework\Renderer". Cette classe implémente l'interface "RendererInterface" qui est utilisée pour générer des vues.

// La classe déclare deux propriétés privées :

// $path est un tableau qui contient les chemins vers les dossiers où se trouvent les vues.
// La constante DEFAULT_NAMESPACE qui est utilisé pour pointer vers le namespace par défaut.
// La méthode "addPath" permet d'ajouter un chemin vers un dossier de vues. Elle prend en paramètre un namespace et un chemin. Si le chemin n'est pas spécifié, il sera ajouté au namespace par défaut.

// La méthode "render" prend en paramètre le nom d'une vue et un tableau de paramètres. Elle utilise la méthode "hasNamespace" pour vérifier si le nom de la vue contient un namespace, si c'est le cas, elle utilise la méthode "replayNamespace" pour remplacer le namespace par le chemin correspondant. Sinon, elle utilise le namespace par défaut et ajoute le nom de la vue.
// La méthode "render" utilise ensuite la fonction "require" pour inclure le fichier de vue correspondant et "extract" pour extraire les paramètres passés dans le tableau de paramètres pour être utilisé dans la vue. Enfin, elle renvoie le contenu de la vue générée.

// Les méthodes "hasNamespace" et "replayNamespace" sont des fonctions privées qui sont utilisées dans la méthode "render" pour gérer les namespaces. 

?>