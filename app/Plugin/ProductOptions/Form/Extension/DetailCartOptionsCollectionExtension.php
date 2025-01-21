<?php

namespace Plugin\ProductOptions\Form\Extension;

use Doctrine\ORM\EntityManagerInterface;
use Eccube\Common\EccubeConfig;
use Eccube\Entity\Product;
use Eccube\Form\Type\AddCartType;
use Eccube\Form\Type\Admin\ProductType;
use Plugin\ProductOptions\Entity\Options;
use Plugin\ProductOptions\Repository\OptionsRepository;
use Plugin\RelatedProduct42\Entity\RelatedProduct;
use Plugin\RelatedProduct42\Form\Type\Admin\RelatedProductType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * Class DetailCartOptionsCollectionExtension.
 */
class DetailCartOptionsCollectionExtension extends AbstractTypeExtension
{
    /**
     * @var EccubeConfig
     */
    private $eccubeConfig;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var OptionsRepository
     */
    private $optionsRepository;

    public function __construct(EccubeConfig $eccubeConfig, EntityManagerInterface $entityManager, OptionsRepository $optionsRepository)
    {
        $this->eccubeConfig = $eccubeConfig;
        $this->entityManager = $entityManager;
        $this->optionsRepository = $optionsRepository;
    }

    /**
     * ProductOptionsCollectionExtension.
     *
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('Options', CollectionType::class, [
            'label' => false,
            'mapped' => true,
            'allow_add' => true,
            'allow_delete' => true,
            'by_reference' => false
        ]);
    }

    /**
     * product admin form name.
     *
     * @return string
     */
    public function getExtendedType()
    {
        return AddCartType::class;
    }

    /**
     * product admin form name.
     *
     * @return string[]
     */
    public static function getExtendedTypes(): iterable
    {
        yield AddCartType::class;
    }
}
