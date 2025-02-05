<?php

namespace PhpArsenal\SalesforceOutboundMessageBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerInterface;

/** @todo move out to a separate repo */
class DependencyInjectionBuilder
{
    /**
     * @param ContainerInterface $container
     * @param array $config
     * @param string $rootNode
     */
    public static function setupConfigurationParameters(ContainerInterface $container, $config, $rootNode)
    {
        foreach ($config as $key => $value) {
            self::setupConfigurationParameter($container, $key, $value, $rootNode);
        }
    }

    /**
     * @param ContainerInterface $container
     * @param string $key
     * @param array|string $child
     * @param string $rootNode
     */
    public static function setupConfigurationParameter(ContainerInterface $container, $key, $child, $rootNode)
    {
        $container->setParameter(sprintf('%s.%s', $rootNode, $key), $child);

        if (is_array($child)) {
            foreach ($child as $k => $value) {
                self::setupConfigurationParameter($container, $k, $value, sprintf('%s.%s', $rootNode, $key));
            }
        }
    }
}