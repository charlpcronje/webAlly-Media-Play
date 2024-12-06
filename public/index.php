<?php
// File: public/index.php
namespace  html\public;

error_reporting(E_ALL);
ini_set('display_errors', 1);
require __DIR__ . '/../vendor/autoload.php';



use Slim\Factory\AppFactory;
use Slim\Middleware\MethodOverrideMiddleware;
use Slim\Middleware\ErrorMiddleware;
use html\Middleware\AuthMiddleware;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use html\Controllers\AdminController;
use html\Controllers\MediaController;
use html\Controllers\PageController;
use html\Controllers\TrackingPixelController;

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// Create Slim App
$app = AppFactory::create();

// Add Routing Middleware
$app->addRoutingMiddleware();

// Add Method Override Middleware
$app->add(new MethodOverrideMiddleware());

// Add Error Middleware
$errorMiddleware = new ErrorMiddleware(
    $app->getCallableResolver(),
    $app->getResponseFactory(),
    true, // displayErrorDetails - set to false in production
    false, // logErrors
    false  // logErrorDetails
);
$app->add($errorMiddleware);

// Add Authentication Middleware
$app->add(new AuthMiddleware());

// Define Routes

// Admin Routes
$app->group('/admin', function ($group) {

    // Admin Login
    $group->map(['GET', 'POST'], '/login', AdminController::class . ':login')->setName('admin.login');

    // Admin Logout
    $group->get('/logout', AdminController::class . ':logout')->setName('admin.logout');

    // Dashboard
    $group->get('/dashboard', AdminController::class . ':dashboard')->setName('admin.dashboard');

    // Pages Management
    $group->get('/pages', PageController::class . ':index')->setName('admin.pages');
    $group->get('/pages/create', PageController::class . ':create')->setName('admin.pages.create');
    $group->post('/pages/create', PageController::class . ':store')->setName('admin.pages.store');
    $group->get('/pages/edit/{id}', PageController::class . ':edit')->setName('admin.pages.edit');
    $group->post('/pages/edit/{id}', PageController::class . ':update')->setName('admin.pages.update');
    $group->post('/pages/archive/{id}', PageController::class . ':archive')->setName('admin.pages.archive');

    // Media Management
    $group->get('/media', MediaController::class . ':index')->setName('admin.media');
    $group->get('/media/upload', MediaController::class . ':upload')->setName('admin.media.upload');
    $group->post('/media/upload', MediaController::class . ':store')->setName('admin.media.store');
    $group->get('/media/link', MediaController::class . ':link')->setName('admin.media.link');
    $group->post('/media/link', MediaController::class . ':linkStore')->setName('admin.media.link.store');

    // Tracking Pixel Configuration
    $group->map(['GET', 'POST'], '/tracking-pixel-config', TrackingPixelController::class . ':configure')->setName('admin.tracking_pixel_config');

    // Media Log (AJAX)
    $group->post('/media/log', MediaController::class . ':log')->setName('admin.media.log');

    // Page Views
    $group->get('/page-views', PageController::class . ':pageViews')->setName('admin.page_views');

});

// Tracking Pixel Handler
$app->get('/tracking_pixel.{ext}', TrackingPixelController::class . ':serve')->setName('tracking_pixel');

// Media Proxy
$app->get('/media/{file}', MediaController::class . ':proxy')->setName('media.proxy');

// Run the app
$app->run();
