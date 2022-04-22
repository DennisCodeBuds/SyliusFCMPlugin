<?php

declare(strict_types=1);

namespace CodeBuds\SyliusFCMPlugin\Form\Type;

use App\Entity\User\ShopUser;
use CodeBuds\SyliusFCMPlugin\Entity\ShopUserFCMToken;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;

class ShopUserFCMNotificationType extends FCMNotificationType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);
        $builder
            ->add('recipient', EntityType::class, [
                'class' => ShopUser::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('shop_user')
                        ->join(ShopUserFCMToken::class, 'token', Join::WITH, 'token.owner = shop_user');
                },
                'label' => 'codebuds_sylius_fcm_plugin.ui.recipient',
            ]);
    }

    public function getBlockPrefix(): string
    {
        return 'codebuds_sylius_fcm_plugin_shop_user_notification';
    }
}
