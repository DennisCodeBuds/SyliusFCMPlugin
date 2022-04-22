<?php

declare(strict_types=1);

namespace CodeBuds\SyliusFCMPlugin\Menu;

use Sylius\Bundle\UiBundle\Menu\Event\MenuBuilderEvent;

final class NotificationsMenuBuilder
{
    public function buildMenu(MenuBuilderEvent $menuBuilderEvent): void
    {
        $menu = $menuBuilderEvent->getMenu();

        $FCMRootMenuItem = $menu
            ->addChild('codebuds_sylius_fcm')
            ->setLabel('codebuds_sylius_fcm_plugin.ui.label');

        $FCMRootMenuItem
            ->addChild('codebuds_sylius_fcm_configuration', ['route' => 'codebuds_sylius_fcm_plugin_admin_configuration'])
            ->setLabel('codebuds_sylius_fcm_plugin.ui.configuration')
            ->setLabelAttribute('icon', 'cogs');

        $FCMRootMenuItem
            ->addChild('codebuds_sylius_fcm_shop_user_notifications', ['route' => 'codebuds_sylius_fcm_plugin_admin_shop_user_fcm_notification_index'])
            ->setLabel('codebuds_sylius_fcm_plugin.ui.shop_user_fcm_notifications')
            ->setLabelAttribute('icon', 'envelope');

        $FCMRootMenuItem
            ->addChild('codebuds_sylius_fcm_shop_user_tokens', ['route' => 'codebuds_sylius_fcm_plugin_admin_shop_user_fcm_token_index'])
            ->setLabel('codebuds_sylius_fcm_plugin.ui.shop_user_fcm_tokens')
            ->setLabelAttribute('icon', 'key');
    }
}
