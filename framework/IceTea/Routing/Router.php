<?php

namespace IceTea\Routing;

use Closure;
use IceTea\Utils\Config;
use IceTea\Hub\Singleton;
use IceTea\Exceptions\Http\NotFoundHttpException;
use IceTea\Exceptions\Http\MethodNotAllowedException;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com>
 * @license MIT
 */
class Router
{
    use Singleton;

    /**
     * @var array|string
     */
    private $uri;

    /**
     * @var bool
     */
    private $isEndPointWithFile = false;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->uri = $_SERVER['REQUEST_URI'];
    }

    /**
     * Fire.
     *
     * @return mixed
     */
    public function fire()
    {
        $func = $this->urlGenerator();
        foreach (RouteCollector::getInstance()->getRoutes() as $route => $val) {
            if ($this->isMatch($route)) {
                $reqMethod = $_SERVER['REQUEST_METHOD'];
                if (isset($val[$reqMethod]) or (isset($val[true]) and $reqMethod = true)) {
                    if ($val[$reqMethod] instanceof Closure) {
                        return $val[$reqMethod](RouteBinding::getBindedValue());
                    } else {
                        $a = explode("@", $val[$reqMethod]);
                        if (count($a) != 2) {
                            throw new InvalidArgumentException("Invalid route", 1);
                        } else {
                            $provider = RouteCollector::getProviderInstance();
                            $controller = $provider->getControllerNamespace()."\\".$a[0];
                            if (class_exists($controller)) {
                                $controller = new $controller();
                                if (is_callable([$controller, $a[1]])) {
                                    return $controller->{$a[1]}(
                                        RouteBinding::getBindedValue()
                                    );
                                } else {
                                    throw new \Exception("Function {$a[1]} does not exists.", 1);
                                }
                                var_dump(123);
                            } else {
                                throw new \Exception("Class {$controller} does not exists.", 1);
                            }
                        }
                    }
                } else {
                    throw new MethodNotAllowedException("Method not allowed", 1);
                }
            }
        }
        throw new NotFoundHttpException("Not found");
    }

    private function urlGenerator()
    {
        $this->uri = explode("?", $this->uri);
        if (($c = count($this->uri)) > 1) {
            unset($this->uri[$c - 1]);
        }
        $this->uri = implode("/", $this->uri);
        do {
            $this->uri = str_replace("//", "/", $this->uri, $n);
        } while ($n);
        $this->uri = "/".trim($this->uri, "/");
        if ($routerFile = Config::get("router_path")) {
            if (substr($this->uri, 1, strlen($routerFile)) === $routerFile) {
                $this->uri = explode($routerFile, $this->uri, 2);
                $this->uri = empty($this->uri[1]) ? ["/"] : explode("/", $this->uri[1]);
            } else {
                $this->uri = explode("/", $this->uri);
            }
        } else {
        }
    }

    private function isMatch($route)
    {
        do {
            $route = str_replace("//", "/", $route, $n);
        } while ($n);
        $route = explode("/", $route);
        if (count($route) === count($this->uri)) {
            foreach ($route as $key => $val) {
                if (substr($val, 0, 1) === "{" && substr($val, -1) === "}") {
                    RouteBinding::bind(substr($val, 1, -1), $this->uri[$key]);
                } else {
                    if ($val !== $this->uri[$key]) {
                        RouteBinding::destroy();
                        return false;
                    }
                }
            }
        } else {
            RouteBinding::destroy();
            return false;
        }
        return true;
    }
}
