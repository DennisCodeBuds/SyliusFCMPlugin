<?php

declare(strict_types=1);

namespace CodeBuds\SyliusFCMPlugin\Form\Type;

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

abstract class FCMNotificationType extends AbstractResourceType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'codebuds_sylius_fcm_plugin.ui.title'
            ])
            ->add('body', TextareaType::class, [
                'label' => 'codebuds_sylius_fcm_plugin.ui.body',
            ])
//            ->add('products', ProductAutocompleteChoiceType::class, [
//                'label' => 'bitbag_sylius_cms_plugin.ui.products',
//                'multiple' => true,
//            ])
        ;
    }
}
