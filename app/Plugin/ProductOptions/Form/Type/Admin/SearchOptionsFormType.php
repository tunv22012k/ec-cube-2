<?php

namespace Plugin\ProductOptions\Form\Type\Admin;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class SearchOptionsFormType extends AbstractType
{
    /**
     * SearchOptionsFormType __construct
     *
     */
    public function __construct()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id', TextType::class, [
                'label'     => "seach data id and name",
                'required'  => false,
            ])
            // ソート用
            ->add('sortkey', HiddenType::class, [
                'label'     => 'admin.list.sort.key',
                'required'  => false,
            ])
            ->add('sorttype', HiddenType::class, [
                'label'     => 'admin.list.sort.type',
                'required'  => false,
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'admin_search_options';
    }
}
