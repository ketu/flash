<?php

namespace Veda\Flash;

use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

final class App
{
    private $container;

    private $booted = false;
    private $debug = false;

    public function __construct(Container $container = null)
    {
        $this->container = $container ? $container : new Container();
        $this->routes = new RouteCollection();
    }

    public function setContainer(Container $container)
    {
        $this->container = $container;
    }

    public function getContainer()
    {
        return $this->container;
    }

    // route methods
    public function put($path, $to = null, $name = null, array $defaultOptions = [])
    {
        $this->routes->map($path, $to, $name, $defaultOptions)->setMethods(["PUT"]);
    }

    public function delete($path, $to = null, $name = null, array $defaultOptions = [])
    {
        $this->routes->map($path, $to, $name, $defaultOptions)->setMethods(["DELETE"]);
    }

    public function options($path, $to = null, $name = null, array $defaultOptions = [])
    {
        $this->routes->map($path, $to, $name, $defaultOptions)->setMethods(["OPTIONS"]);
    }

    public function get($path, $to = null, $name = null, array $defaultOptions = [])
    {
        $this->routes->map($path, $to, $name, $defaultOptions)->setMethods(["GET"]);
    }

    public function post($path, $to = null, $name = null, array $defaultOptions = [])
    {
        $this->routes->map($path, $to, $name, $defaultOptions)->setMethods(["POST"]);
    }

    public function patch($path, $to = null, $name = null, array $defaultOptions = [])
    {
        $this->routes->map($path, $to, $name, $defaultOptions)->setMethods(["PATCH"]);
    }

    public function any($path, $to = null, $name = null, array $defaultOptions = [])
    {
        $this->routes->map($path, $to, $name, $defaultOptions)->setMethods(
            [
                "GET",
                "POST",
                "PUT",
                "OPTIONS",
                "DELETE",
                "PATCH"
            ]
        );
    }

    private function boot()
    {
        $this->booted = true;
    }

    public function run()
    {
        if (!$this->booted) {
            $this->boot();
        }

        $request = Request::createFromGlobals();
        $matcher = new UrlMatcher($this->routes);
        try {
            $request = $matcher->match($request);
            $controllerResolver = new ControllerResolver();
            $defaultArgumentValueResolvers = ArgumentResolver::getDefaultArgumentValueResolvers();

            // app value resolver
            $defaultArgumentValueResolvers[] = new AppValueResolver($this);
            $argumentResolver = new ArgumentResolver(null, $defaultArgumentValueResolvers);


            $controller = $controllerResolver->getController($request);
            $arguments = $argumentResolver->getArguments($request, $controller);
            $response = call_user_func_array($controller, $arguments);

            if (!$response instanceof Response && !is_subclass_of($response, ResponseInterface::class)) {
                $response = new Response($response);
            }

            return $response->send();

        } catch (ResourceNotFoundException $e) {
            $response = new Response('Not Found', 404);
            return $response->send();
        } catch (\Exception $e) {
            $response = new Response('An error occurred', 500);
            return $response->send();
        }

    }

    public function terminate()
    {

    }

}