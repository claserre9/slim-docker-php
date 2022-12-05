<?php

//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

use App\middlewares\CorsMiddleware;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\controllers\CategoryController;
use App\controllers\ProductController;
use App\controllers\UserController;
use App\middlewares\AuthTokenMiddleware;
use App\middlewares\JsonBodyParserMiddleware;
use DI\Container;
use Slim\Factory\AppFactory;


require __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createUnsafeImmutable(__DIR__."/../");
$dotenv->load();


$container = new Container();

$builder = new DI\ContainerBuilder();
$builder->addDefinitions(__DIR__ . "/../DIConfig.php");
$builder->useAutowiring(true);
$builder->useAnnotations(true);
try {
    $container = $builder->build();
} catch (Exception $e) {
    exit($e->getMessage());
}


AppFactory::setContainer($container);
$app = AppFactory::create();


$app->add(new CorsMiddleware());

$app->get('/', function (Request $request, Response $response, array $args) {
    /** @var \Twig\Environment $twig */
    $twig = $this->get("twig");
    $content = $twig->render('swagger.html.twig', ['domain' => getenv('APP_BE_DOMAIN')]);
    $response->getBody()->write($content);
    return $response;
});

$app->post('/products',[ProductController::class, 'create']);
$app->get('/products', [ProductController::class, 'getAll'])->add(new AuthTokenMiddleware());
$app->get('/products/{id}', [ProductController::class, 'get']);
$app->delete('/products/{id}', [ProductController::class, 'delete']);
$app->map(['PUT', 'PATCH'], '/products/{id}', [ProductController::class, 'update']);

$app->post('/categories',[CategoryController::class, 'create']);
$app->get('/categories', [CategoryController::class, 'getAll']);
$app->get('/categories/{id}', [CategoryController::class, 'get']);
$app->delete('/categories/{id}', [CategoryController::class, 'delete']);
$app->map(['PUT', 'PATCH'], '/categories/{id}', [CategoryController::class, 'update']);


$app->post('/users',[UserController::class, 'create']);
$app->get('/users', [UserController::class, 'getAll']);
$app->get('/users/{id}', [UserController::class, 'get']);
$app->delete('/users/{id}', [UserController::class, 'delete']);
$app->map(['PUT', 'PATCH'], '/users/{id}', [UserController::class, 'update']);


$app->post('/login', [UserController::class, 'login']);
$app->get('/logout', [UserController::class, 'logout']);



$app->add(new JsonBodyParserMiddleware());
$app->addRoutingMiddleware();
$errorMiddleware = $app->addErrorMiddleware(true, false, false);
$errorHandler = $errorMiddleware->getDefaultErrorHandler();
$errorHandler->forceContentType('application/json');

//$errorHandler = $errorMiddleware->getDefaultErrorHandler();
//$errorHandler->forceContentType('application/json');
//$errorHandler->registerErrorRenderer('application/json', AppErrorRenderer::class);



$app->run();
