<?php

namespace Plugin\ProductOptions\Form\Extension\Admin;

use Doctrine\ORM\EntityManagerInterface;
use Eccube\Common\EccubeConfig;
use Eccube\Entity\Product;
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
 * Class ProductOptionsCollectionExtension.
 */
class ProductOptionsCollectionExtension extends AbstractTypeExtension
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
        $builder
            ->add('Options', ChoiceType::class, [
                'choice_label'  => 'Name',
                'multiple'      => true,
                'mapped'        => false,
                'expanded'      => true,
                'choices'       => $this->optionsRepository->getListDatafindUseFlgActive(),
                'choice_value' => function (Options $Options = null) {
                    return $Options ? $Options->getId() : null;
                },
            ]);

        // $builder->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event) {
        //     /** @var Product $Product */
        //     $Product = $event->getData();
        //     $max = $this->eccubeConfig['related_product.max_item_count'];
        //     $RelatedProducts = $Product->getRelatedProducts();

        //     for ($i = 0; $i < $max; $i++) {
        //         if (!isset($RelatedProducts[$i])) {
        //             $RelatedProduct = new RelatedProduct();
        //             $RelatedProduct->setProduct($Product);
        //             $Product->addRelatedProduct($RelatedProduct);
        //         }
        //     }
        //     $form = $event->getForm();
        //     $form['RelatedProducts']->setData($Product->getRelatedProducts());
        // });

        // $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
        //     /** @var Product $Product */
        //     $Product = $event->getData();
        //     $RelatedProducts = $Product->getRelatedProducts();
        //     foreach ($RelatedProducts as $RelatedProduct) {
        //         if (null === $RelatedProduct->getChildProduct()) {
        //             $Product->removeRelatedProduct($RelatedProduct);
        //             $this->entityManager->remove($RelatedProduct);
        //         }
        //     }
        // });
    }

    /**
     * product admin form name.
     *
     * @return string
     */
    public function getExtendedType()
    {
        return ProductType::class;
    }

    /**
     * product admin form name.
     *
     * @return string[]
     */
    public static function getExtendedTypes(): iterable
    {
        yield ProductType::class;
    }
}
