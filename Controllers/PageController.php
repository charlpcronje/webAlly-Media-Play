<?php
// File: app/Controllers/PageController.php

namespace html\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use html\Models\Page;
use html\Models\PageView;

class PageController
{
    /**
     * List all pages
     */
    public function index(Request $request, Response $response, $args): Response
    {
        ob_start();
        $pages = Page::all();
        include __DIR__ . '/../templates/pages_list.php';
        $response->getBody()->write(ob_get_clean());
        return $response;
    }

    /**
     * Show create page form
     */
    public function create(Request $request, Response $response, $args): Response
    {
        ob_start();
        include __DIR__ . '/../templates/pages_create.php';
        $response->getBody()->write(ob_get_clean());
        return $response;
    }

    /**
     * Handle create page submission
     */
    public function store(Request $request, Response $response, $args): Response
    {
        $params = (array)$request->getParsedBody();
        $name = isset($params['name']) ? trim($params['name']) : '';
        $slug = isset($params['slug']) ? trim($params['slug']) : '';

        if ($name && $slug) {
            $page = new Page();
            $page->name = $name;
            $page->slug = $slug;
            $page->is_archived = 0;
            $page->save();
        }

        // Redirect to pages list
        return $response
            ->withHeader('Location', '/admin/pages')
            ->withStatus(302);
    }

    /**
     * Show edit page form
     */
    public function edit(Request $request, Response $response, $args): Response
    {
        $id = (int)$args['id'];
        $page = Page::find($id);
        if (!$page) {
            $response->getBody()->write("Page not found.");
            return $response->withStatus(404);
        }

        ob_start();
        include __DIR__ . '/../templates/pages_edit.php';
        $response->getBody()->write(ob_get_clean());
        return $response;
    }

    /**
     * Handle edit page submission
     */
    public function update(Request $request, Response $response, $args): Response
    {
        $id = (int)$args['id'];
        $page = Page::find($id);
        if (!$page) {
            $response->getBody()->write("Page not found.");
            return $response->withStatus(404);
        }

        $params = (array)$request->getParsedBody();
        $name = isset($params['name']) ? trim($params['name']) : '';
        $slug = isset($params['slug']) ? trim($params['slug']) : '';

        if ($name && $slug) {
            $page->name = $name;
            $page->slug = $slug;
            $page->save();
        }

        // Redirect to pages list
        return $response
            ->withHeader('Location', '/admin/pages')
            ->withStatus(302);
    }

    /**
     * Archive a page
     */
    public function archive(Request $request, Response $response, $args): Response
    {
        $id = (int)$args['id'];
        $page = Page::find($id);
        if ($page) {
            $page->is_archived = 1;
            $page->save();
        }

        // Redirect to pages list
        return $response
            ->withHeader('Location', '/admin/pages')
            ->withStatus(302);
    }

    /**
     * View Page Views
     */
    public function pageViews(Request $request, Response $response, $args): Response
    {
        ob_start();
        $pageViews = PageView::allWithPages();
        include __DIR__ . '/../templates/page_views_list.php';
        $response->getBody()->write(ob_get_clean());
        return $response;
    }
}
