<?php

namespace WND\SMVCF\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class DoctrineExtension extends Extension
{
    /**
     * @inheritdoc
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new DoctrineConfiguration();
        $config = $this->processConfiguration($configuration, $configs);

        $container
            ->register(
                'doctrine',
                'WND\SMVCF\Doctrine\Registry'
            )
            ->addMethodCall('setConfiguration', [$config])
        ;
    }

    /**
     * @inheritdoc
     */
    public function getAlias()
    {
        return "doctrine";
    }
}
