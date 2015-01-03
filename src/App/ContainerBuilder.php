<?php

namespace WND\SMVCF\App;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder as DIContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Routing\Loader\YamlFileLoader;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Templating\Helper\SlotsHelper;
use Symfony\Component\Templating\Loader\FilesystemLoader;
use Symfony\Component\Templating\TemplateNameParser;

class ContainerBuilder
{
    protected $container;

    /**
     * @param \WND\SMVCF\App\Kernel $kernel
     */
    public function __construct(Kernel $kernel)
    {
        $this->container = new DIContainerBuilder();

        $services = ['router', 'templating', 'requestStack'];

        foreach ($services as $service) {
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

        $requestContext = new RequestContext(
            str_replace($_SERVER['PATH_INFO'], '', $_SERVER['REQUEST_URI'])
        );

        $container
            ->register('router', 'Symfony\Component\Routing\Router')
            ->addArgument($loader)
            ->addArgument('routing.yml')
            ->addArgument([])
            ->addArgument($requestContext)
        ;
    }

    protected function buildTemplating(ContainerInterface $container, Kernel $kernel)
    {
        $nameParser = new TemplateNameParser();
        $loader = new FilesystemLoader(
            $kernel->getSrcDir() . DIRECTORY_SEPARATOR . '%name%'
        );

        $container
            ->register('templating', 'Symfony\Component\Templating\PhpEngine')
            ->addArgument($nameParser)
            ->addArgument($loader)
            ->addMethodCall('set', [new SlotsHelper()])
            ->addMethodCall('addGlobal', ['router', new Reference('router')])
        ;
    }

    protected function buildRequestStack(ContainerInterface $container, Kernel $kernel)
    {
        $container
            ->register(
                'request_stack',
                'Symfony\Component\HttpFoundation\RequestStack'
            );
    }
}
