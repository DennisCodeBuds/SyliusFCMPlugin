<?php

declare(strict_types=1);

namespace CodeBuds\SyliusFCMPlugin\Form\Type;

use App\Entity\User\ShopUser;
use CodeBuds\SyliusFCMPlugin\Entity\ShopUserFCMNotification;
use CodeBuds\SyliusFCMPlugin\Entity\ShopUserFCMToken;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class ShopUserFCMNotificationType extends FCMNotificationType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $jsonPlaceholder = json_encode(['id' => 123, 'lang' => 'en_EN'], JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT);

        $builder
            ->add('shopUser', EntityType::class, [
                'class' => ShopUser::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('shopUser')
                        ->innerJoin(ShopUserFCMToken::class, 'token', Join::WITH, 'token.shopUser = shopUser');
                },
                'label' => 'codebuds_sylius_fcm_plugin.ui.recipient',
                'constraints' => [
                    new Assert\NotBlank()
                ]
            ])
            ->add('title', TextType::class, [
                'required' => true,
                'mapped' => true,
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length([
                        'max' => 100
                    ])
                ]
            ])->add('body', TextareaType::class, [
                'required' => true,
                'mapped' => true,
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length([
                        'max' => 250
                    ])
                ]
            ])->add('data', TextareaType::class, [
                'required' => false,
                'mapped' => true,
                'attr' => ['placeholder' => $jsonPlaceholder],
                'constraints' => [
                    new Assert\Json()
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ShopUserFCMNotification::class,
            'attr' => ['id' => 'shopUserNotificationForm']
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'codebuds_sylius_fcm_plugin_shop_user_notification';
    }
}
