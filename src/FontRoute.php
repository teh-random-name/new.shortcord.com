<?php
namespace ShortCord;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class FontRoute {

    private $app;

    public __construct(Request $request, Response $response) {
        $this->app = $app;
    }
}