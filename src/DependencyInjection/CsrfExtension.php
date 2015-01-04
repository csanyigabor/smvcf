<?php

namespace WND\SMVCF\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class CsrfExtension extends Extension
{
    /**
     * @inheritdoc
     */
    public function load(array $config, ContainerBuilder $container)
    {}

    /**
     * @inheritdoc
     */
    public function getAlias()
    {
        return "csrf";
    }
}
