<?php
use Main\Core\Router;
use Main\Core\Request;
use Main\Utils\DependencyInjector;

require_once __DIR__ . "/vendor/autoload.php";

$db = new PDO(
    'mysql:host=localhost' .
    ';dbname=car_rental_db',
    'root',
    'secret'
);

$loader = new Twig_Loader_Filesystem(__DIR__ . "/src/Views");
$twig = new Twig_Environment($loader);

$di = new DependencyInjector();
$di->set('PDO', $db);
$di->set('Twig_Environment', $twig);

$request = new Request();
$router = new Router($di);
$htmlCode = $router->route($request);

echo "<!DOCTYPE html>";
echo $htmlCode;