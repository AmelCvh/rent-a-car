<?php

namespace App\Car\Action;

use Model\Entity\Marque;
use Core\Toaster\Toaster;
use Model\Entity\Vehicule;
use GuzzleHttp\Psr7\Response;
use Doctrine\ORM\EntityManager;
use Psr\Http\Message\ServerRequestInterface;
use Core\Framework\Renderer\RendererInterface;

class MarqueAction
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
         * Methode qui ajoute une marque dans la bdd
         *
         * @param ServerRequestInterface $request
         * @return void
         */
        public function addMarque(ServerRequestInterface $request) {

            $method = $request->getMethod();

            if ($method ==='POST') {
                $data = $request->getParsedBody();  
                $marque =$this->marqueRepository->findAll();

                foreach ($marque as $marques) {
                    if($marques->getName() === $data['name']) {
                        $this->toaster->makeToast('Cette marque existe déjà', Toaster::ERROR);
                        return $this->renderer->render('@car/addMarque');
                    }
                }
                $new = new Marque();
                $new->setName($data['name']);
                $this->manager->persist($new);
                $this->manager->flush();
                $this->toaster->makeToast('Marque créer avec succès', Toaster::SUCCESS);

                return (new Response())
                ->withHeader('Location','/listCar');
            }
            return $this->renderer->render('@car/addMarque');
        }

        public function marqueList(ServerRequestInterface $request) {
            
            $marques = $this->marqueRepository->findAll();
            
            return $this->renderer->render('@car/listMarque', ["marques" => $marques]);
        }
    }
?>