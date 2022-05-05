<?php

declare(strict_types=1);

namespace CodeBuds\SyliusFCMPlugin\Form\Type;

use App\Entity\Product\Product;
use App\Entity\User\ShopUser;
use CodeBuds\SyliusFCMPlugin\Entity\ProductFCMTopic;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class NotificationType extends AbstractType
{
    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $jsonPlaceholder = json_encode(['id' => 123, 'lang' => 'en_EN'], JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT);

        $builder
            ->add('targetType', ChoiceType::class, [
                'choices' => [
                    'User' => ShopUser::class,
                    'Product' => ProductFCMTopic::class,
                ],
                'mapped' => false
            ])
            ->add('target', TextType::class, [
                'required' => false,
                'mapped' => false,
            ])->add('title', TextType::class, [
                'required' => true,
                'mapped' => true,
            ])->add('body', TextareaType::class, [
                'required' => true,
                'mapped' => true,
            ])->add('data', TextareaType::class, [
                'required' => false,
                'mapped' => true,
                'attr' => ['placeholder' => $jsonPlaceholder]
            ]);

        $formModifier = static function (FormInterface $form, $targetType = null) {
            $form->add('target', EntityType::class, [
                'class' => $targetType,
                'placeholder' => '',
            ]);
        };

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($formModifier) {
                $data = $event->getData();
                $targetType = null;
                if (method_exists($data, 'getShopUser')) {
                    $targetType = ShopUser::class;
                }
                if (method_exists($data, 'getProductFCMTopic')) {
                    $targetType = Product::class;
                }

                $formModifier($event->getForm(), $targetType);
            }
        );

        $builder->get('targetType')->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) use ($formModifier) {
                // It's important here to fetch $event->getForm()->getData(), as
                // $event->getData() will get you the client data (that is, the ID)
                $targetType = $event->getForm()->getData();

                // since we've added the listener to the child, we'll have to pass on
                // the parent to the callback functions!
                $formModifier($event->getForm()->getParent(), $targetType);
            }
        );

    }
}
