<?php
/**
 * User: ketu.lai <ketu.lai@gmail.com>
 * Date: 2017/5/19 16:48
 */

namespace Veda\Flash;

use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection as BaseRouteCollection;

class RouteCollection extends BaseRouteCollection
{
    public function map($path, $to = null, $name = null, array $defaultOptions = [])
    {
        $route = new Route($path);
        //$route->setDefaults($defaultOptions);
        $route->setDefault('_controller', $to);
        if (!$name) {
            //echo $path;
        }
        $this->add($name, $route);
        return $route;
    }
}