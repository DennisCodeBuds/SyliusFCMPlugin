<?php

declare(strict_types=1);

namespace CodeBuds\SyliusFCMPlugin\DependencyInjection;

use Exception;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

final class CodeBudsSyliusFCMExtension extends Extension //implements PrependExtensionInterface
{
    /**
     * @psalm-suppress UnusedVariable
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config/services'));
        try {
            $loader->load('controller.yml');
        } catch (Exception $exception) {
            var_dump($exception);
        }

        $configuration = $this->getConfiguration($configs, $container);

        $config = $this->processConfiguration($configuration, $configs);

        $definition = $container->getDefinition("codebuds_sylius_fcm_plugin.controller.fcm_configuration.overridden");
        $definition->setArgument(0, $config['firebase_credentials_filepath']);
    }

//    public function prepend(ContainerBuilder $container): void
//    {
//        $configs = $container->getExtensionConfig($this->getAlias());
//        $fileSystem = new Filesystem();
//+
//        $credentialsExist = $fileSystem->exists($configs['firebase_credentials_filepath']);
//
//        if ($credentialsExist) {
//            $newConfig = [
//                'projects' => [
//                    'fcm_codebuds_sylius_plugin' => [
//                        'credentials' => $configs['firebase_credentials_filepath']
//                    ]
//                ]
//            ];
//
//            $container->prependExtensionConfig('kreait_firebase', $newConfig);
//        }
//    }
}
