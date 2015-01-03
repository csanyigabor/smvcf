<?php

namespace WND\SMVCF\App;

use Symfony\Component\DependencyInjection\ContainerBuilder as DIContainerBuilder;

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
                [$this, spritnf('build%s', ucfirst($service))],
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

    protected function buildRouter()
    {
        new \Symfony\Component\Routing\Router;
    }
}
