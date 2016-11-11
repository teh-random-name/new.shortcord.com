<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app->get('/dcare/{task}', function(Request $request, Response $response, $args){
	return $this->view->render($response, 'dcare.twig', [
		'title' => ucwords($args['task']),
		'image' => ($args['task'] == 'finished' ? '/dcare/finished.png' : '/dcare/willContinue.png')
	]);
})
->setName('dcare');