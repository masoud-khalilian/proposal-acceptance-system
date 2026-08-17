<?php

declare(strict_types=1);

use App\Config\Database;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ReviewerController;
use App\Http\Controllers\SubmitterController;
use App\Http\Middleware\AuthMiddleware;
use App\Http\Middleware\LocaleMiddleware;
use App\Http\Middleware\RoleMiddleware;
use App\Repository\ActorRepository;
use App\Repository\RoleRepository;
use App\Repository\SubmissionRepository;
use App\Repository\SubmissionReviewerRepository;
use App\Repository\WorkflowTypeRepository;
use App\Service\AuthService;
use App\Service\WorkflowService;
use App\Support\Translator;
use Slim\Csrf\Guard;
use Slim\Factory\AppFactory;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;

require __DIR__ . '/../vendor/autoload.php';

Dotenv\Dotenv::createImmutable(__DIR__ . '/..')->safeLoad();

session_set_cookie_params(['httponly' => true, 'samesite' => 'Lax']);
session_start();

$db = Database::connection();
$translator = new Translator(__DIR__ . '/../translations');

$view = Twig::create(__DIR__ . '/../templates', [
    'cache' => (($_ENV['APP_ENV'] ?? 'local') === 'production') ? __DIR__ . '/../var/cache/twig' : false,
]);
$view->getEnvironment()->addFunction(new \Twig\TwigFunction(
    't',
    fn (string $key, array $params = []) => $translator->translate($key, $params)
));
$view->getEnvironment()->addGlobal('app_version', trim((string) file_get_contents(__DIR__ . '/../VERSION')));

$actorRepository = new ActorRepository($db);
$roleRepository = new RoleRepository($db);
$workflowTypeRepository = new WorkflowTypeRepository($db);
$submissionRepository = new SubmissionRepository($db);
$submissionReviewerRepository = new SubmissionReviewerRepository($db);

$authService = new AuthService($actorRepository, $roleRepository);
$workflowService = new WorkflowService(
    $submissionRepository,
    $submissionReviewerRepository,
    $workflowTypeRepository,
    $actorRepository
);

$app = AppFactory::create();

$authController = new AuthController($view, $translator, $authService);
$submitterController = new SubmitterController(
    $view,
    $translator,
    $submissionRepository,
    $actorRepository,
    $workflowTypeRepository,
    $workflowService
);
$reviewerController = new ReviewerController(
    $view,
    $translator,
    $submissionReviewerRepository,
    $submissionRepository,
    $workflowService
);
$adminController = new AdminController($view, $translator, $actorRepository, $submissionRepository);

$app->get('/', [$authController, 'dashboard']);
$app->get('/login', [$authController, 'showLogin']);
$app->post('/login', [$authController, 'login']);
$app->get('/register', [$authController, 'showRegister']);
$app->post('/register', [$authController, 'register']);
$app->post('/logout', [$authController, 'logout']);
$app->get('/dashboard', [$authController, 'dashboard']);

$app->group('', function ($group) use ($submitterController) {
    $group->get('/submitter', [$submitterController, 'dashboard']);
    $group->post('/submitter/submissions', [$submitterController, 'store']);
})->add(new RoleMiddleware(['submitter']))->add(new AuthMiddleware());

$app->group('', function ($group) use ($reviewerController) {
    $group->get('/reviewer', [$reviewerController, 'dashboard']);
    $group->get('/reviewer/submissions/{id}', [$reviewerController, 'show']);
    $group->post('/reviewer/submissions/{id}/decision', [$reviewerController, 'decide']);
})->add(new RoleMiddleware(['reviewer']))->add(new AuthMiddleware());

$app->group('', function ($group) use ($adminController) {
    $group->get('/admin', [$adminController, 'dashboard']);
})->add(new RoleMiddleware(['admin']))->add(new AuthMiddleware());

$app->get('/health', function ($request, $response) {
    $response->getBody()->write('ok');
    return $response;
});

// Slim executes middleware last-added-first (LIFO/onion), so this order
// resolves as: error (outermost/catches everything) -> routing (route must
// be known before anything else runs) -> body parsing (form fields must be
// parsed before CSRF can check them) -> CSRF guard -> locale -> twig request
// binding -> route handler (innermost).
$app->add(TwigMiddleware::create($app, $view));
$app->add(new LocaleMiddleware($translator, $view));
$app->add(new Guard($app->getResponseFactory()));
$app->addBodyParsingMiddleware();
$app->addRoutingMiddleware();
$app->addErrorMiddleware((bool) ($_ENV['APP_DEBUG'] ?? false), true, true);

$app->run();
