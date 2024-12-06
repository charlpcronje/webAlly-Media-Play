<?php
// File: app/Controllers/AdminController.php
namespace html\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use html\Models\User;
use html\Models\Page;
use html\Models\Media;

class AdminController
{
    /**
     * Show login form or handle login submission
     */
    public function login(Request $request, Response $response, $args): Response
    {
        if ($request->getMethod() === 'GET') {
            // Display login form
            ob_start();
            include __DIR__ . '/../templates/login.php';
            $response->getBody()->write(ob_get_clean());
            return $response;
        }

        if ($request->getMethod() === 'POST') {
            // Handle login submission
            $params = (array)$request->getParsedBody();
            $email = isset($params['email']) ? trim($params['email']) : '';
            $password = $params['password'] ?? '';

            $user = User::findByEmail($email);

            if ($user && password_verify($password, $user->password_hash)) {
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_email'] = $user->email;

                // Redirect to dashboard
                return $response
                    ->withHeader('Location', '/admin/dashboard')
                    ->withStatus(302);
            } else {
                // Invalid credentials
                $_SESSION['error'] = "Invalid email or password.";
                // Redirect back to login
                return $response
                    ->withHeader('Location', '/admin/login')
                    ->withStatus(302);
            }
        }

        // If not GET or POST, return 405 Method Not Allowed
        return $response->withStatus(405);
    }

    /**
     * Logout admin
     */
    public function logout(Request $request, Response $response, $args): Response
    {
        session_destroy();

        // Redirect to login
        return $response
            ->withHeader('Location', '/admin/login')
            ->withStatus(302);
    }

    /**
     * Display dashboard
     */
    public function dashboard(Request $request, Response $response, $args): Response
    {
        ob_start();
        $pages = Page::all(true); // include archived
        $media = Media::all();
        include __DIR__ . '/../templates/dashboard.php';
        $response->getBody()->write(ob_get_clean());
        return $response;
    }
}
