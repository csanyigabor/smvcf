<?php

namespace WND\SMVCF\Http;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

abstract class Controller
{
    /**
     * @type \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container;

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param string $service
     *
     * @return object
     */
    protected function get($service)
    {
        return $this->container->get($service);
    }

    /**
     * @param string $template
     * @param array  $params
     *
     * @return string
     */
    protected function renderView($template, array $params = [])
    {
        return $this->container->get('templating')->render($template, $params);
    }

    /**
     * @param string $template
     * @param array  $params
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function render($template, array $params = [])
    {
        return new Response($this->renderView($template, $params));
    }

    /**
     * @param string $route
     * @param array  $params
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function redirect($route, array $params = [])
    {
        return new RedirectResponse(
            $this->get('router')->generate($route, $params)
        );
    }

    /**
     * @param \Symfony\Component\Form\FormTypeInterface $type
     * @param mixed                                     $data
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    protected function createForm(FormTypeInterface $type, $data)
    {
        return $this->get('form_factory')->create($type, $data);
    }
}
