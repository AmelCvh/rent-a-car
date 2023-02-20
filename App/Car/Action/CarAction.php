<?php

namespace App\Car\Action;

use Model\Entity\Marque;
use Core\Toaster\Toaster;
use Model\Entity\Vehicule;
use GuzzleHttp\Psr7\Response;
use Doctrine\ORM\EntityManager;
use Core\Framework\Router\Router;
use GuzzleHttp\Psr7\UploadedFile;
use Psr\Container\ContainerInterface;
use Core\Framework\Validator\Validator;
use Core\Framework\Router\RedirectTrait;
use Psr\Http\Message\ServerRequestInterface;
use Core\Framework\Renderer\RendererInterface;

class CarAction
    {

        use RedirectTrait;

        private Router $router;
        private ContainerInterface $container;
        private RendererInterface $renderer;
        private EntityManager $manager;
        private Toaster $toaster;
        private $marqueRepository;
        private $repository;

        public function __construct(RendererInterface $renderer, EntityManager $manager, Toaster $toaster, ContainerInterface $container, Router $router)
        {
            $this->container = $container;
            $this->renderer = $renderer;
            $this->manager = $manager;
            $this->toaster = $toaster;
            $this->router = $router;
            $this->marqueRepository = $manager->getRepository(Marque::class);
            $this->repository = $manager->getRepository(Vehicule::class);
        }

        /**
         * Methode ajoutant un véhicule en bdd
         * @param ServerRequestInterface $request
         * @return MessageInterface|string
         */
        public function addCar(ServerRequestInterface $request)
        {
            $method = $request->getMethod();

            if ($method === 'POST') {
                $data = $request->getParsedBody();
                $file = $request->getUploadedFiles()["img"];
    
                $validator = new Validator($data);
                $errors = $validator
                    ->required('modele', 'color', 'marque')
                    ->getErrors();
                if($errors) {
                    foreach($errors as $error) {
                        $this->toaster->makeToast($error->toString(), Toaster::ERROR);
                    }
                return $this->redirect('car.add');
                }
                $this->fileGuards($file);
                $fileName = $file->getClientFileName();
                $imgPath = $this->container->get('img.basePath') . $fileName;
                $file->moveTo($imgPath);
                if (!$file->isMoved()) {
                    $this->toaster->makeToast("Une erreur s'est produite durant l'enregistrement de votre image, merci de réessayer.", Toaster::ERROR);
                    return $this->redirect('car.list');
                }
                $new = new Vehicule();
                $marque = $this->marqueRepository->find($data['marque']);
                if ($marque) {
                    $new->setModel($data['modele'])
                        ->setMarque($marque)
                        ->setCouleur($data['color'])
                        ->setImgPath($fileName);
    
                    $this->manager->persist($new);
                    $this->manager->flush();
                    $this->toaster->makeToast('Véhicule ajoutée avec success', Toaster::SUCCESS);
                }
    
                return $this->redirect('car.add');
            }
    
            $marques = $this->marqueRepository->findAll();
    
            return $this->renderer->render('@car/addCar', [
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
         * @return \Psr\Http\Message\MessageInterface|string
         * @throws \Doctrine\ORM\Exception\ORMException
         * @throws \Doctrine\ORM\OptimisticLockException
         */
        public function update(ServerRequestInterface $request)
        {
        $id = $request->getAttribute('id');
        $voiture = $this->repository->find($id);

        $method = $request->getMethod();

        if ($method === 'POST') {
            $data = $request->getParsedBody();
            $files = $request->getUploadedFiles();
            if (sizeof($files) > 0 && $files['img']->getError() !== 4) {
                $oldImg = $voiture->getImgPath();
                $newImg = $files['img'];
                $imgName = $newImg->getClientFileName();
                $imgPath = $this->container->get('img.basePath') . $imgName;
                $this->fileGuards($newImg);
                $newImg->moveTo($imgPath);
                if ($newImg->isMoved()) {
                    $voiture->setImgPath($imgName);
                    $oldPath = $this->container->get('img.basePath') . $oldImg;
                    unlink($oldPath);
                }
            }
            $marque = $this->marqueRepository->find($data['marque']);
            $voiture->setModel($data['modele'])
                ->setMarque($marque)
                ->setCouleur($data['color']);

            $this->manager->flush();
            $this->toaster->makeToast('Véhicule ajoutée avec success', Toaster::SUCCESS);
            return $this->redirect('car.list');
        }

        $marques = $this->marqueRepository->findAll();

        return $this->renderer->render('@car/update', [
            'voiture' => $voiture,
            'marques' => $marques
        ]);
        }

        /**
         * Methode qui supprime un véhicule en bdd
         *
         * @param ServerRequestInterface $request
         * @return \Psr\Http\Message\MessageInterface
         * @throws \Doctrine\ORM\Exception\ORMException
         * @throws \Doctrine\ORM\OptimisticLockException
         */
        public function delete(ServerRequestInterface $request): Response
        {
            $id = $request->getAttribute('id');
            $voiture = $this->repository->find($id);

            $this->manager->remove($voiture);
            $this->manager->flush();

            return $this->redirect('car.list');
        }

        private function fileGuards(UploadedFile $file)
        {
            // Handle server error
            if ($file->getError() === 4) {
                $this->toaster->makeToast("Une erreur est survenue lors du chargement du fichier", Toaster::ERROR);
                return $this->redirect('car.add');
            }

            list($type, $format)= explode('/', $file->getClientMediaType());

            //Handle foarmat server
            if(!in_array($type, ['img']) or !in_array($format, ['jpg', 'jpeg', 'png']))
            {
                $this->toaster->makeToast("ERREUR : Le format du fichier n'est pas valide, merci de charger un .png, .jpeg ou .jpg", Toaster::ERROR);
                return $this->redirect('car.add');
            }

            //Handle excessive size
            if ($file->getSize() > 2047674) {
                $this->toaster->makeToast("Merci de choisir un fichier n'excedant pas 2Mo", Toaster::ERROR);
                return $this->redirect('car.add');
            }
            return true;
        }
    }
?>