<?php

namespace Plugin\ProductOptions\Service\PurchaseFlow;

use Eccube\Entity\ItemHolderInterface;
use Eccube\Service\PurchaseFlow\PurchaseContext;
use Eccube\Service\PurchaseFlow\PurchaseFlow;

class CustomPurchaseFlow extends PurchaseFlow
{
    public function __construct()
    {
        parent::__construct();
    }

    public function validate(ItemHolderInterface $itemHolder, PurchaseContext $context)
    {
        // call parents
        $response = parent::validate($itemHolder, $context);

        // caculate price options
        // $listCartOptions = $itemHolder->getCartOptions();
        // foreach ($listCartOptions as $key => $value) {
        //     $totalPriceOptions = $value->getPrice() * $value->getQuantity();
        //     $itemHolder->setTotal($totalPriceOptions + $itemHolder->getTotal());
        // }

        return $response;
    }
}
