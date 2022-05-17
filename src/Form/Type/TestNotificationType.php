<?php

declare(strict_types=1);

namespace CodeBuds\SyliusFCMPlugin\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Contracts\Translation\TranslatorInterface;

final class TestNotificationType extends AbstractType
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
                    'token' => 'Token',
                    'topic' => 'topic',
                ]
            ])
            ->add('target', TextType::class, [
                'required' => true,
                'mapped' => false])
            ->add('title', TextType::class, [
                'required' => true,
                'mapped' => false,
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length([
                        'max' => 100
                    ])
                ]
            ])->add('body', TextareaType::class, [
                'required' => true,
                'mapped' => false,
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length([
                        'max' => 250
                    ])
                ]
            ])->add('data', TextareaType::class, [
                'required' => false,
                'mapped' => false,
                'attr' => ['placeholder' => $jsonPlaceholder],
                'constraints' => [
                    new Assert\Json()
                ]
            ]);
    }
}
