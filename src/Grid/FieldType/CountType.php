<?php

namespace CodeBuds\SyliusFCMPlugin\Grid\FieldType;

use Sylius\Component\Grid\DataExtractor\DataExtractorInterface;
use Sylius\Component\Grid\Definition\Field;
use Sylius\Component\Grid\FieldTypes\FieldTypeInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CountType implements FieldTypeInterface
{
    public function __construct(DataExtractorInterface $dataExtractor)
    {
        $this->dataExtractor = $dataExtractor;
    }

    public function render(Field $field, $data, array $options = [])
    {
        if ('.' !== $field->getPath()) {
            $data = $this->dataExtractor->get($field, $data);
        }
        return (string)count($data);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'dynamic' => false
            ]);
    }

    public function getName(): string
    {
        return 'count';
    }
}
