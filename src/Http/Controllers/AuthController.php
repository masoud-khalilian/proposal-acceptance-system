<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Service\AuthService;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class AuthController extends Controller
{
    public function __construct(
        \Slim\Views\Twig $view,
        \App\Support\Translator $translator,
        private readonly AuthService $auth,
    ) {
        parent::__construct($view, $translator);
    }

    public function showLogin(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, $request, 'auth/login.twig');
    }

    public function login(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $body = (array) $request->getParsedBody();
        $username = trim((string) ($body['username'] ?? ''));
        $password = (string) ($body['password'] ?? '');
        $role = (string) ($body['role'] ?? '');

        $actor = $username !== '' && $password !== '' && $role !== ''
            ? $this->auth->attempt($username, $password, $role)
            : null;

        if ($actor === null) {
            $this->flash('error', $this->translator->translate('auth.invalid_credentials'));
            return $this->redirect($response, '/login');
        }

        session_regenerate_id(true);
        $_SESSION['actor_id'] = (int) $actor['id'];
        $_SESSION['actor_name'] = $actor['first_name'] . ' ' . $actor['last_name'];
        $_SESSION['role_key'] = $actor['role_key'];

        return $this->redirect($response, '/dashboard');
    }

    public function showRegister(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, $request, 'auth/register.twig');
    }

    public function register(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $body = (array) $request->getParsedBody();
        $username = trim((string) ($body['username'] ?? ''));
        $password = (string) ($body['password'] ?? '');
        $passwordConfirmation = (string) ($body['password_confirmation'] ?? '');
        $firstName = trim((string) ($body['first_name'] ?? ''));
        $lastName = trim((string) ($body['last_name'] ?? ''));
        $fieldLevel = (string) ($body['field_level'] ?? '');

        if ($username === '' || $firstName === '' || $lastName === '') {
            $this->flash('error', $this->translator->translate('auth.fill_all_fields'));
            return $this->redirect($response, '/register');
        }

        if ($password === '' || $password !== $passwordConfirmation) {
            $this->flash('error', $this->translator->translate('auth.password_mismatch'));
            return $this->redirect($response, '/register');
        }

        try {
            $this->auth->registerSubmitter($username, $password, $firstName, $lastName, [
                'field_level' => $fieldLevel,
            ]);
        } catch (InvalidArgumentException) {
            $this->flash('error', $this->translator->translate('auth.username_taken'));
            return $this->redirect($response, '/register');
        }

        $this->flash('success', $this->translator->translate('auth.registration_complete'));
        return $this->redirect($response, '/login');
    }

    public function logout(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $_SESSION = [];
        session_destroy();
        return $this->redirect($response, '/login');
    }

    public function dashboard(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return match ($_SESSION['role_key'] ?? null) {
            'submitter' => $this->redirect($response, '/submitter'),
            'reviewer' => $this->redirect($response, '/reviewer'),
            'admin' => $this->redirect($response, '/admin'),
            default => $this->redirect($response, '/login'),
        };
    }
}
