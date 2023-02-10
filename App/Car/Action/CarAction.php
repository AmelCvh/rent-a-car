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
                $validator = new Validator($data);
                $errors = $validator
                        ->required('modele', 'couleur', 'marque')
                        ->getErrors(); 
                        if ($errors) {
                            foreach ($errors as $error) {
                                $this->toaster->makeToast($error->toString(),Toaster::ERROR);
                            }
                            return (new Response())
                            ->withHeader('Location', '/addCar');
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
                    ->withHeader('Location','/listCar');
                    
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

            return $this->renderer->render('@car/list', ["voitures" => $voitures]);

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
    }
?>