<?php

namespace App\Core;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class ResponseHelper
{
    public static function view(string $template, array $data = [], int $status = 200): Response
    {
        $view = new View();
        $html = $view->render($template, $data);
        return new Response($html, $status, ['Content-Type' => 'text/html']);
    }

    public static function json($data, int $status = 200): JsonResponse
    {
        return new JsonResponse($data, $status);
    }

    public static function redirect(string $url, int $status = 302): Response
    {
        return new Response('', $status, ['Location' => $url]);
    }

    public static function error(string $message, int $status = 400): JsonResponse
    {
        return new JsonResponse(['error' => $message], $status);
    }
}


