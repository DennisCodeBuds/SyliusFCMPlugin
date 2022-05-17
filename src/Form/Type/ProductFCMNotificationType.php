<?php

declare(strict_types=1);

namespace CodeBuds\SyliusFCMPlugin\Form\Type;

use CodeBuds\SyliusFCMPlugin\Entity\ProductFCMNotification;
use CodeBuds\SyliusFCMPlugin\Entity\ProductFCMTopic;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class ProductFCMNotificationType extends FCMNotificationType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if (($notification = $options['data']) && (null !== $topic = $notification->getTopic())) {
            $builder
                ->add('topic', EntityType::class, [
                    'class' => ProductFCMTopic::class,
                    'multiple' => false,
                    'data' => $topic,
                    'choices' => [$topic],
                    'disabled' => true,
                ]);

            $jsonData = json_encode(['type' => 'product', 'id' => $topic->getProduct()->getId()], JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT);
        } else {
            //TODO do not load all choices, this generates too many database requests
            $builder
                ->add('topic', EntityType::class, [
                    'class' => ProductFCMTopic::class,
                    'multiple' => false,
                ]);

            $jsonData = json_encode(['type' => 'product', 'id' => 'not set'], JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT);
        }


        $builder
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
                'data' => $jsonData,
                'constraints' => [
                    new Assert\Json()
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ProductFCMNotification::class,
            'attr' => ['id' => 'productNotificationForm']
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'codebuds_sylius_fcm_plugin_product_notification';
    }
}
