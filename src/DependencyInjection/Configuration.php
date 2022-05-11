<?php

declare(strict_types=1);

namespace CodeBuds\SyliusFCMPlugin\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    /**
     * @psalm-suppress UnusedVariable
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('code_buds_sylius_fcm');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
            ->scalarNode("firebase_credentials_filepath")->defaultValue('%kernel.project_dir%/var/config/firebase_credentials.json')->end()
            ->end();


        return $treeBuilder;
    }
}
