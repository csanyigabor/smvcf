<?php

namespace WND\SMVCF\App;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder as DIContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\Loader\YamlFileLoader;
use Symfony\Component\Routing\RequestContext;

class ContainerBuilder
{
    protected $container;

    /**
     * @param \WND\SMVCF\App\Kernel $kernel
     */
    public function __construct(Kernel $kernel)
    {
        $this->container = new DIContainerBuilder();

        foreach (['router'] as $service) {
            call_user_func(
                [$this, sprintf('build%s', ucfirst($service))],
                $this->container,
                $kernel
            );
        }
    }

    /**
     * @return \Symfony\Component\DependencyInjection\ContainerInterface
     */
    public function getContainer()
    {
        return $this->container;
    }

    protected function buildRouter(ContainerInterface $container, Kernel $kernel)
    {
        $locator = new FileLocator($kernel->getConfigDir());
        $loader = new YamlFileLoader($locator);

        $requestContext = new RequestContext($_SERVER['REQUEST_URI']);

        $container
            ->register('router', 'Symfony\Component\Routing\Router')
            ->addArgument($loader)
            ->addArgument('routing.yml')
            ->addArgument([])
            ->addArgument($requestContext);
    }
}
