<?php

namespace App\User\Action;

use Model\Entity\User;
use Core\Toaster\Toaster;
use Doctrine\ORM\EntityManager;
use Core\Framework\Auth\UserAuth;
use Core\Framework\Router\Router;
use Doctrine\ORM\EntityRepository;
use GuzzleHttp\Psr7\ServerRequest;
use Psr\Container\ContainerInterface;
use Core\Framework\Validator\Validator;
use Core\Framework\Router\RedirectTrait;
use Core\Framework\Renderer\RendererInterface;
use Core\Session\SessionInterface;
use Core\Toaster\Toast;

class UserAction
{
    use RedirectTrait;
    private ContainerInterface $container;
    private RendererInterface $renderer;
    private Toaster $toaster;
    private Router $router;
    private EntityRepository $repository;
    private SessionInterface $session;


    public function __construct(ContainerInterface $container)
    {

        $this->container = $container;
        $this->renderer = $container->get(RendererInterface::class);
        $this->toaster = $container->get(Toaster::class);
        $this->router = $container->get(Router::class);
        $this->repository = $container->get(EntityManager::class)->getRepository(User::class);
        $this->session = $container->get(SessionInterface::class);
    }

    public function logView(ServerRequest $request)
    {
        return $this->renderer->render('@user/forms');
    }

    public function signIn(ServerRequest $request)
    {   
        $auth = $this->container->get(UserAuth::class);
        $data = $request->getParsedBody();
        $validator = new Validator($data);
        $errors = $validator->required('nom', 'prenom', 'email','mdp','mdp_confirm')
                            ->email('email')
                            ->strSize('mdp', 12, 50)
                            ->confirm('mdp')
                            ->isUnique('email', $this->repository,'email')
                            ->getErrors();

        if ($errors) {
            foreach ($errors as $error) {
                $this->toaster->makeToast($error->toString(), Toaster::ERROR);
            }
            return $this->redirect('user.login');
        }

        $result = $auth->signIn($data);
        if($result !== true) {
            return $result;
        }

        $this->toaster->makeToast("Inscription reussie, vous pouvez vous connecter", Toaster::SUCCESS);
        return $this->redirect('user.login');
    }

    public function login(ServerRequest $request)
    {
        $data = $request->getParsedBody();
        $validator = new Validator($data);
        $errors = $validator
                ->required('email', 'mdp')
                ->email('email')
                ->getErrors();

        if ($errors) {
            foreach ($errors as $error) {
                $this->toaster->makeToast($error->toString(), Toaster::ERROR);
            }
            return $this->redirect('use.login');
        }

        $auth = $this->container->get(UserAuth::class);
        $res = $auth->login($data['email'], $data['mdp']);
        if ($res) {
            $this->toaster->makeToast('Connexion réussie', Toaster::SUCCESS);
            return $this->redirect('user.home');
        }
        $this->toaster->makeToast('Connexion échoué, merci de verifier mail et mot de passe', Toaster::ERROR);
        return $this->redirect('user.login');
    }

    public function home(ServerRequest $request)
    {
        $user = $this->session->get('auth');
        return $this->renderer->render('@user/home',['user' => $user]);
    }
}