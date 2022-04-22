<?php

declare(strict_types=1);

namespace CodeBuds\SyliusFCMPlugin\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class ImportType extends AbstractType
{
    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('file', FileType::class, [
            'required' => true,
            'mapped' => false,
            'help' => $this->translator->trans('codebuds_sylius_fcm_plugin.ui.import.help')
        ]);
    }
}
