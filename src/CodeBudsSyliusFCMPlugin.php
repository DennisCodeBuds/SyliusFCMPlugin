<?php

declare(strict_types=1);

namespace CodeBuds\SyliusFCMPlugin;

use CodeBuds\SyliusFCMPlugin\DependencyInjection\CodeBudsSyliusFCMExtension;
use CodeBuds\SyliusFCMPlugin\DependencyInjection\CompilerPass;
use Sylius\Bundle\CoreBundle\Application\SyliusPluginTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class CodeBudsSyliusFCMPlugin extends Bundle
{
    use SyliusPluginTrait;


    public function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new CompilerPass());
    }

    public function getContainerExtension(): ?ExtensionInterface
    {
        if (null === $this->extension) {
            $this->extension = new CodeBudsSyliusFCMExtension();
        }

        return $this->extension;
    }
}
