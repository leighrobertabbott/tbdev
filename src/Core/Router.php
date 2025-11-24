<?php

namespace App\Core;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use App\Core\Middleware;

class Router
{
    private RouteCollection $routes;
    private RequestContext $context;

    public function __construct()
    {
        $this->routes = new RouteCollection();
        $this->context = new RequestContext();
    }

    public function addRoute(string $name, string $path, array $defaults = [], array $requirements = [], array $options = [], string $host = '', array $schemes = [], array $methods = []): void
    {
        $route = new Route($path, $defaults, $requirements, $options, $host, $schemes, $methods);
        $this->routes->add($name, $route);
    }

    public function get(string $path, $handler, string $name = null, array $middleware = []): void
    {
        $this->addRoute($name ?? uniqid('route_'), $path, ['_controller' => $handler, '_middleware' => $middleware], [], [], '', [], ['GET']);
    }

    public function post(string $path, $handler, string $name = null, array $middleware = []): void
    {
        $this->addRoute($name ?? uniqid('route_'), $path, ['_controller' => $handler, '_middleware' => $middleware], [], [], '', [], ['POST']);
    }

    public function put(string $path, $handler, string $name = null, array $middleware = []): void
    {
        $this->addRoute($name ?? uniqid('route_'), $path, ['_controller' => $handler, '_middleware' => $middleware], [], [], '', [], ['PUT']);
    }

    public function delete(string $path, $handler, string $name = null, array $middleware = []): void
    {
        $this->addRoute($name ?? uniqid('route_'), $path, ['_controller' => $handler, '_middleware' => $middleware], [], [], '', [], ['DELETE']);
    }

    public function dispatch(Request $request): Response
    {
        $this->context->fromRequest($request);
        $matcher = new UrlMatcher($this->routes, $this->context);

        try {
            $pathInfo = $request->getPathInfo() ?: '/';
            $method = $request->getMethod();
            
            // Log for debugging
            if (Config::get('app.debug')) {
                error_log("Router: Matching path='{$pathInfo}', method='{$method}'");
            }
            
            $parameters = $matcher->match($pathInfo);
            $controller = $parameters['_controller'];
            $middleware = $parameters['_middleware'] ?? [];

            unset($parameters['_controller'], $parameters['_middleware'], $parameters['_route']);

            // Build middleware chain
            $next = function($request) use ($controller, $parameters) {
                if (is_callable($controller)) {
                    $response = call_user_func_array($controller, array_merge([$request], array_values($parameters)));
                } elseif (is_array($controller) && count($controller) === 2) {
                    // Handle [ClassName::class, 'method'] format
                    [$class, $method] = $controller;
                    if (is_string($class) && class_exists($class)) {
                        $instance = new $class();
                        $response = call_user_func_array([$instance, $method], array_merge([$request], array_values($parameters)));
                    } else {
                        $response = new Response('Controller class not found: ' .
                            (is_string($class) ? $class : gettype($class)), 404);
                    }
                } elseif (is_string($controller) && strpos($controller, '::') !== false) {
                    [$class, $method] = explode('::', $controller);
                    if (class_exists($class)) {
                        $instance = new $class();
                        $response = call_user_func_array([$instance, $method], array_merge([$request], array_values($parameters)));
                    } else {
                        $response = new Response('Controller class not found: ' .
                            $class, 404);
                    }
                } else {
                    $response = new Response('Controller not found. Type: ' .
                        gettype($controller) .
                        ', Value: ' .
                        (is_string($controller) ? $controller : json_encode($controller)), 404);
                }
                return $response instanceof Response ? $response : new Response((string)$response);
            };

            // Apply middleware in reverse order
            foreach (array_reverse($middleware) as $mw) {
                $next = function($req) use ($mw, $next) {
                    return $mw($req, $next);
                };
            }

            $response = $next($request);
            return $response instanceof Response ? $response : new Response((string)$response);
        } catch (\Symfony\Component\Routing\Exception\ResourceNotFoundException $e) {
            // Show detailed error in debug mode
            if (Config::get('app.debug')) {
                $availableRoutes = array_keys($this->routes->all());
                $matchingRoutes = array_filter($availableRoutes, function($name) use ($request) {
                    $route = $this->routes->get($name);
                    return $route && in_array($request
                        ->getMethod(), $route
                        ->getMethods());
                });
                return new Response(
                    '<h1>Route Not Found</h1>' .
                    '<p><strong>Path:</strong> ' .
                        htmlspecialchars($request->getPathInfo()) .
                        '</p>' .
                    '<p><strong>Method:</strong> ' .
                        htmlspecialchars($request->getMethod()) .
                        '</p>' .
                    '<p><strong>Matching routes:</strong> ' .
                        implode(', ', array_slice($matchingRoutes, 0, 20)) .
                        '</p>',
                    404,
                    ['Content-Type' => 'text/html']
                );
            }
            return new Response('Not Found', 404);
        } catch (\Exception $e) {
            error_log('Router exception: ' . $e->getMessage());
            error_log('Stack trace: ' . $e->getTraceAsString());
            if (Config::get('app.debug')) {
                return new Response(
                    '<h1>Error</h1><pre>' .
                        htmlspecialchars($e->getMessage() .
                        "\n\n" .
                        $e->getTraceAsString()) .
                        '</pre>',
                    500,
                    ['Content-Type' => 'text/html']
                );
            }
            return new Response('Not Found', 404);
        }
    }
}

