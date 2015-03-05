<?php

namespace Rezzza\DoctrineSchemaMultiMapping\App\Bundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

/**
 * DoctrineSchemaMultiMappingExtension
 *
 * @uses Extension
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class DoctrineSchemaMultiMappingExtension extends Extension
{
    /**
     * Loads the services based on your application configuration.
     *
     * @param array            $configs
     * @param ContainerBuilder $container
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config/services'));
        $loader->load('ui.xml');
    }
}
