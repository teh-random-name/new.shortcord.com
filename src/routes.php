<?php
require_once 'SystemInfo.php';
require_once 'Helpers.php';

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

// Routes

$app->get('/', function (Request $request, Response $response) {
    // Render index view
    return $this->view->render($response, 'index.twig');
});

$app->get('/netload.png', function(Request $request, Response $response) {
    $file = '/tmp/vnstat_m.png';
    $handle = fopen($file, 'r');
    $rawImage = fread($handle, filesize($file));
    fclose($handle);
    
    // to always make sure its a png
    $im = new Imagick();
    $im->readImageBlob($rawImage);
    $im->setImageFormat('png');

    $nResponse = $response->withHeader('Content-Type', 'image/png');
    $nResponse->write($im);

    //so i can free memeory afterwards
    $im->destroy();
    
    return $nResponse;
});

$app->get('/info.json', function(Request $request, Response $response) {
	    $sysinfo = new \ShortCord\SystemInfo();
        $userHost = \ShortCord\Helpers\getFromServer('HTTP_ORIGIN');

        // to have the SC-Requested-Host header show up
        if ($userHost == null) {
            $userHost = "NULL";
        }

        $hostMatchs = [];
        $nResponse = null;
        
        preg_match('/[^.]+\.[^.]+$/', $userHost, $hostMatchs);
        
        $nResponse = $response->withHeader('SC-Requested-Host', $userHost);

        // we check if there is anything in the regex match and if its on my domain
        if (count($hostMatchs) != 0 and $hostMatchs[0] === "shortcord.com") { 
            // if matched then we set the CORS header properly
            $nResponse = $nResponse->withHeader('Access-Control-Allow-Origin', $userHost);
        } else { 
            // hardcode it to domain.tld so it fails CORS
            $nResponse = $nResponse->withHeader('Access-Control-Allow-Origin', 'https://shortcord.com');            
        }

	return $nResponse->withJson($sysinfo->Report());
});

