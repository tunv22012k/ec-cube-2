<?php

namespace Plugin\ProductOptions\Service\PurchaseFlow\Processor;

use Doctrine\ORM\EntityManagerInterface;
use Eccube\Entity\ItemHolderInterface;
use Eccube\Service\PurchaseFlow\ItemHolderPostValidator;
use Eccube\Service\PurchaseFlow\PurchaseContext;

/**
 * OptionsProcessor
 */
class OptionsProcessor extends ItemHolderPostValidator
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * DeliveryFeePreprocessor constructor.
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param ItemHolderInterface $itemHolder
     * @param PurchaseContext $context
     *
     * @throws InvalidItemException
     */
    protected function validate(ItemHolderInterface $itemHolder, PurchaseContext $context)
    {
        // $CartOptions = $itemHolder->getCartOptions();
        // foreach ($CartOptions as $key => $value) {
        //     $CartItem = new CartItem();
        //     $CartItem->setPrice((int)$value->getPrice());
        //     $CartItem->setQuantity((int)$value->getQuantity());
        //     $CartItem->setProductClass($value->getProductClass());
        //     $CartItem->setCart($value->getCart());

        //     $itemHolder->addItem($CartItem);
        // }

        // dd($itemHolder);
    }
}
