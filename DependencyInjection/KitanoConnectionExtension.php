<?php

namespace Kitano\ConnectionBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class KitanoConnectionExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        // Persistence config
        if (isset($config['persistence']['managed_class'])) {
            if (isset($config['persistence']['managed_class']['connection'])) {
                $container->setParameter('kitano_connection.managed_class.connection',
                    $config['persistence']['managed_class']['connection']);
            }
        }

        if ('custom' !== $config['persistence']['type']) {
            $loader->load(sprintf('persistence/%s.xml', $config['persistence']['type']));

            if (!$container->hasParameter('kitano_connection.managed_class.connection')) {
                switch ($config['persistence']['type']) {
                    case 'doctrine_mongodb':
                        $container->setParameter('kitano_connection.managed_class.connection', 'Kitano\ConnectionBundle\Document\Connection');
                        break;
                    default:
                        $container->setParameter('kitano_connection.managed_class.connection', 'Kitano\ConnectionBundle\Model\Connection');
                        break;
                }
            }
        }

        // Model
        $loader->load('model.xml');
    }
}
