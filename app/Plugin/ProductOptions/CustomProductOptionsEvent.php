<?php

namespace Plugin\ProductOptions;

use Doctrine\ORM\EntityManagerInterface;
use Eccube\Event\EccubeEvents;
use Eccube\Event\TemplateEvent;
use Plugin\ProductOptions\Entity\Options;
use Plugin\ProductOptions\Entity\ProductOptions;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CustomProductOptionsEvent implements EventSubscriberInterface
{
    private $entityManager;

    /**
     * CustomProductOptionsEvent __construct
     *
     * @param EntityManagerInterface $entityManager
     *
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public static function getSubscribedEvents()
    {
        return [
            '@admin/Product/product.twig' => 'onRenderAdminProduct',
            EccubeEvents::ADMIN_PRODUCT_EDIT_COMPLETE => 'onProductEditComplete',
        ];
    }

    /**
     * show data options in page admin product
     *
     * @param TemplateEvent $event
     */
    public function onRenderAdminProduct(TemplateEvent $event)
    {
        $event->addSnippet('@ProductOptions/admin/Product/custom_product_options.twig');
    }

    /**
     * upsert data
     *
     */
    public function onProductEditComplete($event)
    {
        // get data event
        $form = $event->getArgument('form');
        $product = $event->getArgument('Product');

        // remove product options
        foreach ($product->getProductOptions() as $ProductOptions) {
            $this->entityManager->remove($ProductOptions);
            $this->entityManager->flush();
        }

        // add product options
        $options = $form['Options']->getViewData();
        foreach ($options as $option) {
            // set product options
            $productOptions = new ProductOptions();
            $productOptions->setProductId($product['id']);
            $productOptions->setOptionId($option);
            $productOptions->setProduct($product);
            $optionEntity = $this->entityManager->getRepository(Options::class)->find($option);
            $productOptions->setOptions($optionEntity);

            // event upsert
            $this->entityManager->persist($productOptions);
            $this->entityManager->flush();
        }
    }
}
