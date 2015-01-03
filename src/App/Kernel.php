<?php

namespace WND\SMVCF\App;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

/**
 * Class Kernel
 */
class Kernel
{
    /**
     * @type ContainerInterface
     */
    protected $container;

    /**
     * @type string
     */
    protected $rootDir;

    public function __construct()
    {
        $this->container = (new ContainerBuilder($this))->getContainer();
    }

    /**
     * @return string
     */
    public function getRootDir()
    {
        if ($this->rootDir === null) {
            $r = new \ReflectionObject($this);
            $this->rootDir = str_replace(
                DIRECTORY_SEPARATOR == '/' ? '\\' : '/',
                DIRECTORY_SEPARATOR,
                dirname($r->getFileName())
            );

            $this->rootDir = realpath($this->rootDir . DIRECTORY_SEPARATOR . '..');
        }

        return $this->rootDir;
    }

    public function getSrcDir()
    {
        return implode(DIRECTORY_SEPARATOR, [$this->getRootDir(), 'src']);
    }

    /**
     * @return string
     */
    public function getConfigDir()
    {
        return implode(DIRECTORY_SEPARATOR, [$this->getRootDir(), 'config']);
    }

    /**
     * @return string
     */
    public function getCacheDir()
    {
        return implode(DIRECTORY_SEPARATOR, [$this->getRootDir(), 'cache']);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function process(Request $request)
    {
        // adding current request to the request stack
        $this->container->get('request_stack')->push($request);

        list($controllerClass, $actionMethod) = $this->resolvePath($request);

        $controller = new $controllerClass($this->container);
        $response = call_user_func([$controller, $actionMethod]);

        return $response;
    }

    /**
     * Getting controllers class and action from path.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return array
     */
    protected function resolvePath(Request $request)
    {
        try {
            $match = $this->container->get('router')->match($request->getPathInfo());
        } catch (ResourceNotFoundException $e) {
            die('404: Unknown route');
        }

        $ns = $match['_ns'];
        $match = explode(':', $match['_controller']);
        $controller = sprintf('%s\%sController', $ns, $match[0]);
        $action = sprintf('%sAction', $match[1]);

        return [$controller, $action];
    }
}
