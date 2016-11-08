<?php
// DIC configuration
require_once 'TrackingMiddleware.php';
require_once __DIR__.'/../vendor/autoload.php';

$container = $app->getContainer();

// view renderer
$container['view'] = function($c) {
    $settings = $c->get('settings')['renderer'];
    $view = new \Slim\Views\Twig($settings, [
            'cache' => false
    ]);
    $view->addExtension(new \Slim\Views\TwigExtension(
            $c['router'],
            $c['request']->getUri()
    ));
    
    return $view;
};

// monolog
$container['logger'] = function ($c) {
    $settings = $c->get('settings')['logger'];
    $logger = new Monolog\Logger($settings['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], $settings['level']));
    return $logger;
};

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$container['notFoundHandler'] = function($c) {
    return function(Request $request, Response $response) use ($c) {
        return $c['response']
                ->withStatus(404)
                ->withHEader('Content-Type', 'text/plain')
                ->write('Something Broke (404 Not Found)');
    };
};

new \ShortCord\TrackingMiddleware($container, new PiwikTracker(2, 'https://owa.shortcord.com'));