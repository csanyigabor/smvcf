<?php

namespace WND\SMVCF\App;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder as DIContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Form\Extension\Csrf\CsrfExtension;
use Symfony\Component\Form\Extension\Csrf\CsrfProvider\SessionCsrfProvider;
use Symfony\Component\Form\Extension\HttpFoundation\HttpFoundationExtension;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\Forms;
use Symfony\Component\Routing\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader as DIYamlFileLoader;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Templating\Helper\SlotsHelper;
use Symfony\Component\Templating\Loader\FilesystemLoader;
use Symfony\Component\Templating\TemplateNameParser;
use WND\SMVCF\DependencyInjection as DIExtension;

class ContainerBuilder
{
    protected $container;

    /**
     * @param \WND\SMVCF\App\Kernel $kernel
     */
    public function __construct(Kernel $kernel)
    {
        $this->container = new DIContainerBuilder();
        $this->container->setParameter('config_dir', $kernel->getConfigDir());
        $this->container->setParameter('cache_dir', $kernel->getCacheDir());
        $this->container->setParameter('root_dir', $kernel->getRootDir());
        $this->container->setParameter('src_dir', $kernel->getSrcDir());

        $this->container->registerExtension(new DIExtension\DoctrineExtension());
        $this->container->registerExtension(new DIExtension\CsrfExtension());

        $locator = new FileLocator($kernel->getConfigDir());
        $configLoader = new DIYamlFileLoader($this->container, $locator);
        $configLoader->load('config.yml');

        $services = [
            'router',
            'templating',
            'requestStack',
            'session',
            'validator',
            'formFactory',
        ];

        foreach ($services as $service) {
            call_user_func(
                [$this, sprintf('build%s', ucfirst($service))],
                $this->container,
                $kernel
            );
        }

        $this->container->compile();
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
            $kernel->getSrcDir().DIRECTORY_SEPARATOR.'%name%'
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

    protected function buildSession(ContainerInterface $container, Kernel $kernel)
    {
        $container
            ->register(
                'session',
                'Symfony\Component\HttpFoundation\Session\Session'
            );
    }

    protected function buildValidator(ContainerInterface $container, Kernel $kernel)
    {
        $container
            ->register(
                'validator',
                'Symfony\Component\Validator\ValidatorInterface'
            )
            ->setFactory(
                [
                    'Symfony\Component\Validator\Validation',
                    'createValidator',
                ]
            )
        ;
    }

    protected function buildFormFactory(ContainerInterface $container, Kernel $kernel)
    {
        $csrfProvider = new SessionCsrfProvider(
            $container->get('session'),
            'some_csrf_secret'
        );
        $csrfExtension = new CsrfExtension($csrfProvider);

        $httpExtension = new HttpFoundationExtension();

        $formFactoryBuilder = Forms::createFormFactoryBuilder()
            ->addExtension($csrfExtension)
            ->addExtension($httpExtension)
            ->addExtension(new ValidatorExtension($container->get('validator')))
        ;

        $container
            ->register(
                'form_factory',
                'Symfony\Component\Form\FormFactory'
            )
            ->setFactory(
                [
                    $formFactoryBuilder,
                    'getFormFactory',
                ]
            )
        ;
    }
}
