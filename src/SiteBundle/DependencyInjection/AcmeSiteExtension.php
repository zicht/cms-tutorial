<?php
/**
 * @author Gerard van Helden <gerard@zicht.nl>
 * @copyright Zicht Online <http://zicht.nl>
 */

namespace Acme\SiteBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection as DI;

class AcmeSiteExtension extends DI\Extension\Extension
{
    public function load(array $configs, DI\ContainerBuilder $container)
    {
        $loader = new DI\Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        $loader->load('admin.xml');
    }
}