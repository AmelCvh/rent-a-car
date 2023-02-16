<?php

namespace App\Car\Action;

use Model\Entity\Marque;
use Core\Toaster\Toaster;
use Model\Entity\Vehicule;
use GuzzleHttp\Psr7\Response;
use Doctrine\ORM\EntityManager;
use Psr\Http\Message\ServerRequestInterface;
use Core\Framework\Renderer\RendererInterface;
use Core\Framework\Validator\Validator;
use GuzzleHttp\Psr7\UploadedFile;

class CarAction
    {
        private RendererInterface $renderer;
        private EntityManager $manager;
        private Toaster $toaster;
        private $marqueRepository;
        private $repository;

        public function __construct(RendererInterface $renderer, EntityManager $manager, Toaster $toaster)
        {
            $this->renderer = $renderer;
            $this->manager = $manager;
            $this->toaster = $toaster;
            $this->marqueRepository = $manager->getRepository(Marque::class);
            $this->repository = $manager->getRepository(Vehicule::class);
        }

        /**
         * Methode ajoutant un véhicule en bdd
         *
         * @param ServerRequestInterface $request
         * @return void
         */
        public function addCar(ServerRequestInterface $request)
        {
            $method = $request->getMethod();

            if ($method ==='POST') {
                $data = $request->getParsedBody(); 
                $file = $request->getUploadedFiles()['image'];

                $validator = new Validator($data);
                $errors = $validator
                        ->required('modele', 'couleur', 'marque')
                        ->getErrors(); 
                        if ($errors) {
                            foreach ($errors as $error) {
                                $this->toaster->makeToast($error->toString(),Toaster::ERROR);
                            }
                            return (new Response())
                            ->withHeader('Location', 'admin/addCar');
                        }
                $this->fileGuards($file);
                $fileName = $file->getClientFileName();
                $imgPath = dirname(__DIR__, 3). DIRECTORY_SEPARATOR . 'public'. DIRECTORY_SEPARATOR . 'assets'. DIRECTORY_SEPARATOR. 'image' . DIRECTORY_SEPARATOR. $fileName;
                $file->moveTo($imgPath);
                        if (!$file->isMoved()) {
                            $this->toaster->makeToast("Une erreur s'est produite durant l'enregistrement de votre image, merci de réessayer", Toaster::ERROR);
                            return (new Response())
                            ->withHeader('Location', 'admin/addCar');
                        }

                $new = new Vehicule();
                $marque = $this->marqueRepository->find($data['marque']);
                $new->setModel($data['modele'])
                    ->setMarque($marque)
                    ->setCouleur($data['color']);

                $this->manager->persist($new);
                $this->manager->flush();
                $this->toaster->makeToast('Vehicule créer avec succès', Toaster::SUCCESS);

                return (new Response())
                    ->withHeader('Location','admin/listCar');
                    
            }

            $marques = $this->marqueRepository->findAll();

            return $this->renderer->render('@car/addCar',[
                'marques' => $marques
            ]);
        }

        /**
         * Methode qui retourne une liste de vehicule de la bdd
         *
         * @param ServerRequestInterface $request
         * @return string
         */
        public function listCar(ServerRequestInterface $request): string
        {
            $voitures = $this->repository->findAll();

            return $this->renderer->render('@car/listCar', ["voitures" => $voitures]);

        }


        /**
         * Methode qui affiche un vehicule de la bdd
         *
         * @param ServerRequestInterface $request
         * @return string
         */
        public function show(ServerRequestInterface $request):string
        {
            $id = $request->getAttribute('id');

            $voiture = $this->repository->find($id);

            // $voiture = [
            //         "model" => "Challenger",
            //         "marque" => "Dodge",
            //         "couleur" => "Rouge sang"
            //     ];

            return $this->renderer->render('@car/show', ["voiture" => $voiture]);
        }


        /**
         * Methode qui modifie un vehicule en bdd
         *
         * @param ServerRequestInterface $request
         * @return void
         */
        public function update(ServerRequestInterface $request)
        {
            $id = $request->getAttribute('id');
            $voiture = $this->repository->find($id);


            $method = $request->getMethod();

            if($method === 'POST') {
                $data = $request->getParsedBody();
                $voiture->setModel($data['modele']) 
                        ->setMarque($data['marque'])
                        ->setCouleur($data['color']);

                        $this->manager->flush();
                        $this->toaster->makeToast('Véhicule créer avec succès', Toaster::SUCCESS);

                        
                return (new Response())
                ->withHeader('Location','/listCar');
            }
            return $this->renderer->render('@car/update', ["voiture" => $voiture]);
        }

        /**
         * Methode qui supprime un véhicule en bdd
         *
         * @param ServerRequestInterface $request
         * @return Response
         */
        public function delete(ServerRequestInterface $request): Response
        {
            $id = $request->getAttribute('id');
            $voiture = $this->repository->find($id);

            $this->manager->remove($voiture);
            $this->manager->flush();

            return (new Response())
            ->withHeader('Location','/listCar');
        }

        private function fileGuards(UploadedFile $file)
        {
            // Handle server error
            if ($file->getError() === 4) {
                $this->toaster->makeToast("Une erreur est survenue lors du chargement du fichier", Toaster::ERROR);
                return (new Response())
                    ->withHeader('Location', '/admin/addCar');
            }

            list($type, $format)= explode('/', $file->getClientMediaType());

            //Handle foarmat server
            if(!in_array($type, ['image']) or !in_array($format, ['jpg', 'jpeg', 'png']))
            {
                $this->toaster->makeToast("ERREUR : Le format du fichier n'est pas valide, merci de charger un .png, .jpeg ou .jpg", Toaster::ERROR);
                return (new Response())
                    ->withHeader('Location', '/admin/addCar');
            }

            //Handle excessive size
            if ($file->getSize() > 2047674) {
                $this->toaster->makeToast("Merci de choisir un fichier n'excedant pas 2Mo", Toaster::ERROR);
                return (new Response())
                    ->withHeader('Location', '/admin/addCar');
            }
            return true;
        }
    }
?>