<?php
namespace ShortCord;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

/**
 * Custom Middleware for PiwikTracker
 * @author Tristan Smith
 */
class TrackingMiddleware {

        private $tracker;
	
        /**
         * @param array $container Slim 3 Container
         * @param class $tracker PiwikTracker
         */
	public function __construct($container, $tracker) {
                $this->tracker = $tracker;
                $container['tracker'] = $this;
	}
	
        /**
         * Internal function called by Slim Middleware controller
         * @param Request $request
         * @param Response $response
         * @param \callable $next
         * @return \callable
         */
	public function __invoke(Request $request, Response $response, $next) {
        $uriObject = $request->getUri();
		$response = $next($request, $response);
		$this->tracker->doTrackPageView($uriObject->getPath());
		return $response;
    }
}
