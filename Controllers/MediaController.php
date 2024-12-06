<?php
// File: app/Controllers/MediaController.php

namespace html\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use html\Models\Media;
use html\Models\Page;
use html\Models\MediaLog;
use html\Models\Session;

class MediaController
{
    /**
     * List all media
     */
    public function index(Request $request, Response $response, $args): Response
    {
        ob_start();
        $mediaItems = Media::all();
        $pages = Page::all(true);
        include __DIR__ . '/../templates/media_list.php';
        $response->getBody()->write(ob_get_clean());
        return $response;
    }

    /**
     * Show upload media form
     */
    public function upload(Request $request, Response $response, $args): Response
    {
        ob_start();
        $pages = Page::all(true);
        include __DIR__ . '/../templates/media_upload.php';
        $response->getBody()->write(ob_get_clean());
        return $response;
    }

    /**
     * Handle media upload
     */
    public function store(Request $request, Response $response, $args): Response
    {
        $uploadedFiles = $request->getUploadedFiles();
        $pageId = isset($_POST['page_id']) ? (int)$_POST['page_id'] : null;
        $mediaType = isset($_POST['media_type']) ? trim($_POST['media_type']) : 'unknown';

        if (!empty($uploadedFiles['media_file'])) {
            $file = $uploadedFiles['media_file'];
            if ($file->getError() === UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . '/../public/uploads';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                $filename = uniqid() . '-' . preg_replace('/[^A-Za-z0-9.\-_]/', '', $file->getClientFilename());
                $filePath = $uploadDir . '/' . $filename;
                $file->moveTo($filePath);

                $relativePath = '/uploads/' . $filename;

                // Save to database
                $media = new Media();
                $media->page_id = $pageId;
                $media->file_path = $relativePath;
                $media->media_type = $mediaType;
                $media->props = json_encode([]); // or other props
                $media->save();
            }
        }

        // Redirect to media list
        return $response
            ->withHeader('Location', '/admin/media')
            ->withStatus(302);
    }

    /**
     * Show media linking form
     */
    public function link(Request $request, Response $response, $args): Response
    {
        ob_start();
        $mediaItems = Media::all();
        $pages = Page::all(true);
        include __DIR__ . '/../templates/media_link.php';
        $response->getBody()->write(ob_get_clean());
        return $response;
    }

    /**
     * Handle media linking to page
     */
    public function linkStore(Request $request, Response $response, $args): Response
    {
        $params = $request->getParsedBody();
        $mediaId = isset($params['media_id']) ? (int)$params['media_id'] : 0;
        $pageId = isset($params['page_id']) ? (int)$params['page_id'] : 0;

        if ($mediaId && $pageId) {
            $media = Media::find($mediaId);
            if ($media) {
                $media->page_id = $pageId;
                $media->save();
            }
        }

        // Redirect to media list
        return $response
            ->withHeader('Location', '/admin/media')
            ->withStatus(302);
    }

    /**
     * Handle media proxy
     */
    public function proxy(Request $request, Response $response, $args): Response
    {
        $fileName = $args['file'] ?? '';
        $media = Media::findByFilePath($fileName);

        if (!$media) {
            $response->getBody()->write("Media not found.");
            return $response->withStatus(404);
        }

        $filePath = __DIR__ . '/../public' . $media->file_path;
        if (!file_exists($filePath)) {
            $response->getBody()->write("File not found.");
            return $response->withStatus(404);
        }

        $mimeType = mime_content_type($filePath);
        $response = $response->withHeader('Content-Type', $mimeType)
                             ->withHeader('Content-Length', filesize($filePath));

        $stream = new \Slim\Psr7\Stream(fopen($filePath, 'rb'));
        return $response->withBody($stream);
    }

    /**
     * Handle media logging via AJAX
     */
    public function log(Request $request, Response $response, $args): Response
    {
        $data = json_decode($request->getBody(), true);

        $mediaId = isset($data['media_id']) ? (int)$data['media_id'] : 0;
        $pageId = isset($data['page_id']) ? (int)$data['page_id'] : 0;
        $action = isset($data['action']) ? trim($data['action']) : '';
        $currentTime = isset($data['current_time']) ? (int)$data['current_time'] : 0;

        if (!$mediaId || !$pageId || !in_array($action, ['play', 'pause', 'progress'])) {
            $response->getBody()->write(json_encode(['success' => false, 'message' => 'Invalid parameters.']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        // Get IP and User Agent
        $ipAddress = $request->getServerParams()['REMOTE_ADDR'] ?? 'unknown';
        $userAgent = $request->getServerParams()['HTTP_USER_AGENT'] ?? 'unknown';
        $isWhatsApp = (stripos($userAgent, 'WhatsApp') !== false) ? 1 : 0;

        // Session management
        $session = Session::getOrCreateSession($ipAddress);

        // Log the action
        $mediaLog = new MediaLog();
        $mediaLog->session_id = $session->id;
        $mediaLog->media_id = $mediaId;
        $mediaLog->action = $action;
        $mediaLog->current_time = $currentTime;
        $mediaLog->user_agent = $userAgent;
        $mediaLog->is_whatsapp = $isWhatsApp;
        $mediaLog->save();

        $response->getBody()->write(json_encode(['success' => true]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }
}
