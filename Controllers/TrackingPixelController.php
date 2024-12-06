<?php
// File: app/Controllers/TrackingPixelController.php

namespace html\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use html\Models\TrackingPixel;
use html\Models\TrackingPixelSession;
use html\Models\Page;
use html\Models\Media;

class TrackingPixelController
{
    /**
     * Configure tracking pixels for a page
     */
    public function configure(Request $request, Response $response, $args): Response
    {
        $pageId = isset($_GET['page_id']) ? (int)$request->getQueryParams()['page_id'] : null;

        if (!$pageId) {
            $response->getBody()->write("<div class='alert alert-danger'>Page ID not specified.</div>");
            return $response->withStatus(400);
        }

        if ($request->getMethod() === 'GET') {
            $trackingPixels = TrackingPixel::findByPageId($pageId);
            $success = $_SESSION['success'] ?? null;
            $error = $_SESSION['error'] ?? null;
            unset($_SESSION['success'], $_SESSION['error']);
            ob_start();
            include __DIR__ . '/../templates/tracking_pixel_config.php';
            $response->getBody()->write(ob_get_clean());
            return $response;
        }

        if ($request->getMethod() === 'POST') {
            $params = $request->getParsedBody();

            if (isset($params['add_pixel'])) {
                // Adding a new tracking pixel
                $pixelName = isset($params['pixel_name']) ? trim($params['pixel_name']) : '';
                $downloadSpeed = isset($params['download_speed']) ? (int)$params['download_speed'] : 1;

                if ($pixelName && isset($_FILES['pixel_image'])) {
                    $file = $_FILES['pixel_image'];
                    if ($file['error'] === UPLOAD_ERR_OK) {
                        $uploadDir = __DIR__ . '/../public/tracking_pixels';
                        if (!is_dir($uploadDir)) {
                            mkdir($uploadDir, 0755, true);
                        }
                        $filename = uniqid() . '-' . preg_replace('/[^A-Za-z0-9.\-_]/', '', $file['name']);
                        $filePath = $uploadDir . '/' . $filename;
                        move_uploaded_file($file['tmp_name'], $filePath);

                        $relativePath = '/tracking_pixels/' . $filename;

                        // Validate MIME type
                        $finfo = finfo_open(FILEINFO_MIME_TYPE);
                        $mimeType = finfo_file($finfo, $filePath);
                        finfo_close($finfo);
                        $allowedMimeTypes = ['image/gif', 'image/png'];

                        if (!in_array($mimeType, $allowedMimeTypes)) {
                            unlink($filePath);
                            $_SESSION['error'] = "Invalid file type. Only GIF and PNG are allowed.";
                            return $response
                                ->withHeader('Location', "/admin/tracking-pixel-config?page_id=$pageId")
                                ->withStatus(302);
                        }

                        // Save tracking pixel config
                        $trackingPixel = new TrackingPixel();
                        $trackingPixel->page_id = $pageId;
                        $trackingPixel->name = $pixelName;
                        $trackingPixel->image_path = $relativePath;
                        $trackingPixel->download_speed = $downloadSpeed;
                        $trackingPixel->save();

                        $_SESSION['success'] = "Tracking pixel '{$pixelName}' added successfully.";
                    } else {
                        $_SESSION['error'] = "Error uploading image. Please try again.";
                    }
                } else {
                    $_SESSION['error'] = "Pixel name and image are required.";
                }
            }

            if (isset($params['delete_pixel'])) {
                // Deleting a tracking pixel
                $pixelId = isset($params['pixel_id']) ? (int)$params['pixel_id'] : 0;
                if ($pixelId) {
                    $trackingPixel = TrackingPixel::find($pixelId);
                    if ($trackingPixel) {
                        // Delete the image file
                        $filePath = __DIR__ . '/../../public' . $trackingPixel->image_path;
                        if (file_exists($filePath)) {
                            unlink($filePath);
                        }

                        // Delete the record
                        $trackingPixel->delete();

                        $_SESSION['success'] = "Tracking pixel deleted successfully.";
                    }
                }
            }

            // Redirect back to tracking pixel config
            return $response
                ->withHeader('Location', "/admin/tracking-pixel-config?page_id=$pageId")
                ->withStatus(302);
        }

        // If not GET or POST, return 405 Method Not Allowed
        return $response->withStatus(405);
    }

    /**
     * Serve tracking pixel image
     */
    public function serve(Request $request, Response $response, $args): Response
    {
        $ext = $args['ext'] ?? 'gif';
        $queryParams = $request->getQueryParams();
        $pageId = isset($queryParams['page_id']) ? (int)$queryParams['page_id'] : null;
        $trackingId = isset($queryParams['tracking_id']) ? trim($queryParams['tracking_id']) : null;

        if (!$pageId || !$trackingId) {
            $response->getBody()->write("Bad Request: Missing parameters.");
            return $response->withStatus(400);
        }

        // Find the tracking pixel session
        $trackingSession = TrackingPixelSession::findByTrackingId($trackingId);
        if (!$trackingSession || $trackingSession->page_id != $pageId) {
            return $response->withStatus(204); // No Content
        }

        // Get tracking pixel config
        $trackingPixel = TrackingPixel::find($trackingSession->tracking_pixel_id);
        if (!$trackingPixel) {
            return $response->withStatus(404)->write("Tracking pixel not configured for this page.");
        }

        // Get image path
        $imagePath = __DIR__ . '/../../public' . $trackingPixel->image_path;
        if (!file_exists($imagePath)) {
            return $response->withStatus(404)->write("Tracking pixel image not found.");
        }

        // Set headers
        $mimeType = mime_content_type($imagePath);
        $response = $response->withHeader('Content-Type', $mimeType)
                             ->withHeader('Content-Length', filesize($imagePath))
                             ->withHeader('Cache-Control', 'no-cache, no-store, must-revalidate')
                             ->withHeader('Pragma', 'no-cache')
                             ->withHeader('Expires', '0');

        // Implement controlled download speed
        $downloadSpeed = (int)$trackingPixel->download_speed; // Bytes per second

        if ($downloadSpeed > 0) {
            // Implement controlled download speed
            // Note: PHP isn't ideal for precise streaming control
            // This is a basic implementation
            $handle = fopen($imagePath, 'rb');
            if (!$handle) {
                return $response->withStatus(500)->write("Internal Server Error: Unable to read image.");
            }

            while (!feof($handle)) {
                $data = fread($handle, $downloadSpeed);
                echo $data;
                flush();
                sleep(1);
            }
            fclose($handle);
            exit; // Terminate after streaming
        } else {
            // No speed limit, stream normally
            $stream = fopen($imagePath, 'rb');
            $body = new \Slim\Psr7\Stream($stream);
            return $response->withBody($body);
        }
    }
}
