<?php

namespace Hypilon\Http\Routing;


class RouterHelper
{

    /**
     * @var RouteParser $currentRoute
     */
    private $currentRoute;

    /**
     * @var Router $router
     */
    private $router;

    /**
     * RouterHelper constructor.
     * @param RouteParser $currentRoute
     */
    public function __construct(Router $router)
    {
        $this->router = $router;
        $this->currentRoute = $router->getMatched();
    }

    /**
     * @return null|string
     */
    public function getCallback()
    {
        if($this->currentRoute) {
            return $this->currentRoute->getCallback();
        }
        return null;
    }

    /**
     * @return null|string
     */
    public function getController()
    {
        if($this->currentRoute) {
            $callback = $this->getCallback();
            $parts = explode('@', $callback);
            return $parts[0];
        }
        return null;
    }

    /**
     * @return null|string
     */
    public function getAction()
    {
        if($this->currentRoute) {
            $callback = $this->getCallback();
            $parts = explode('@', $callback);
            return $parts[1];
        }
        return null;
    }

    /**
     * @return null|string
     */
    public function getMethod()
    {
        if($this->currentRoute) {
            return $this->currentRoute->getMethod();
        }
        return null;
    }

    public function getAttributes()
    {
        return $this->router->getAttributes();
    }
}