<?php

namespace WND\SMVCF\App;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

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

    public function getRootDir()
    {
        if ($this->rootDir === null) {
            $r = new \ReflectionObject($this);
            $this->rootDir = str_replace(
                DIRECTORY_SEPARATOR == '/' ? '\\' : '/',
                DIRECTORY_SEPARATOR,
                dirname($r->getFileName())
            );
        }

        return $this->rootDir;
    }

    /**
     * @return string
     */
    public function getConfigDir()
    {
        return implode(DIRECTORY_SEPARATOR, [$this->getRootDir(), 'config']);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function process(Request $request)
    {}
}
