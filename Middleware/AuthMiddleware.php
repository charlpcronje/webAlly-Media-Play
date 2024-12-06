<?php
// File: app/Middleware/AuthMiddleware.php
namespace html\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Server\MiddlewareInterface;
use Slim\Psr7\Response as SlimResponse;

class AuthMiddleware implements MiddlewareInterface
{
    public function process(Request $request, RequestHandlerInterface $handler): Response
    {
        $route = $request->getAttribute('route');

        if ($route) {
            $routeName = $route->getName();
            // Public routes that don't require authentication
            $publicRoutes = ['admin.login', 'tracking_pixel', 'media.proxy'];
            if (in_array($routeName, $publicRoutes)) {
                return $handler->handle($request);
            }
        }

        // Check if user is logged in
        if (empty($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
            // Not logged in, redirect to login
            $response = new SlimResponse();
            return $response
                ->withHeader('Location', '/admin/login')
                ->withStatus(302);
        }

        // Proceed to the next middleware/route
        return $handler->handle($request);
    }
}
