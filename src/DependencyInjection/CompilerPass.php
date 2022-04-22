<?php

namespace CodeBuds\SyliusFCMPlugin\DependencyInjection;

use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class CompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $pluginExtension = 'code_buds_sylius_fcm';
        $pluginDefinitionName = 'codebuds_sylius_fcm_plugin.controller.fcm_configuration.overridden';

        if (!$container->hasDefinition($pluginDefinitionName)) {
            return;
        }

        $pluginConfigs = $container->getExtensionConfig($pluginExtension);
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $pluginConfigs);
        $definition = $container->findDefinition('codebuds_sylius_fcm_plugin.controller.fcm_configuration.overridden');
        $definition->addMethodCall('setCredentialsFileLocation', [$config['firebase_credentials_filepath']]);
    }

    private function processConfiguration(ConfigurationInterface $configuration, array $configs): array
    {
        return (new Processor())->processConfiguration($configuration, $configs);
    }
}
