<?php
/**
 * User: ketu.lai <ketu.lai@gmail.com>
 * Date: 2017/5/19 16:06
 */

namespace Veda\Flash;

use Veda\Flash\RouteCollection;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Matcher\UrlMatcher as BaseUrlMatcher;


class UrlMatcher
{

    public function __construct(RouteCollection $routeCollection)
    {
        $this->routes = $routeCollection;
    }

    public function match(Request $request)
    {
        $context = new RequestContext();
        $context->fromRequest($request);
        $matcher = new BaseUrlMatcher($this->routes, $context);
        $attributes = $matcher->matchRequest($request);
        $request->attributes->add($attributes);
        //map json body to request post
        if (in_array($request->getMethod(), [Request::METHOD_POST, Request::METHOD_PATCH, Request::METHOD_PUT])) {
            if ($request->getContentType() == "json") {
                $requestData = \json_decode($request->getContent(), true);
                foreach ($requestData as $key=> $value) {
                    if (!$request->request->has($key)) {
                        $request->request->set($key, $value);
                    }
                }
            }
        }
        return $request;
    }

}