<?php

namespace Plugin\ProductOptions\Controller;

use Eccube\Controller\CartController as BaseCartController;
use Eccube\Entity\BaseInfo;
use Eccube\Entity\ProductClass;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Eccube\Repository\BaseInfoRepository;
use Eccube\Repository\ProductClassRepository;
use Eccube\Service\CartService;
use Eccube\Service\OrderHelper;
use Eccube\Service\PurchaseFlow\PurchaseContext;
use Eccube\Service\PurchaseFlow\PurchaseFlow;
use Eccube\Service\PurchaseFlow\PurchaseFlowResult;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

// class CustomCartController
class CustomCartController extends BaseCartController
{
    /**
     * @var ProductClassRepository
     */
    protected $productClassRepository;

    /**
     * @var CartService
     */
    protected $cartService;

    /**
     * @var PurchaseFlow
     */
    protected $purchaseFlow;

    /**
     * @var BaseInfo
     */
    protected $baseInfo;

    /**
     * CustomCartController constructor.
     *
     * @param ProductClassRepository $productClassRepository
     * @param CartService $cartService
     * @param PurchaseFlow $CartPurchaseFlow
     * @param BaseInfoRepository $baseInfoRepository
     */
    public function __construct(
        ProductClassRepository $productClassRepository,
        CartService $cartService,
        PurchaseFlow $CartPurchaseFlow,
        BaseInfoRepository $baseInfoRepository
    ) {
        $this->productClassRepository = $productClassRepository;
        $this->cartService = $cartService;
        $this->purchaseFlow = $CartPurchaseFlow;
        $this->baseInfo = $baseInfoRepository->get();

        // call parent
        parent::__construct(
            $productClassRepository,
            $cartService,
            $CartPurchaseFlow,
            $baseInfoRepository
        );
    }

    /**
     * カート画面.
     *
     * @Route("/cart", name="cart", methods={"GET"})
     * @Template("Cart/index.twig")
     */
    public function index(Request $request)
    {
        // call data parent
        $response = parent::index($request);

        // dd($response);

        return $response;

        // return [
        //     'totalPrice' => $totalPrice,
        //     'totalQuantity' => $totalQuantity,
        //     // 空のカートを削除し取得し直す
        //     'Carts' => $this->cartService->getCarts(true),
        //     'least' => $least,
        //     'quantity' => $quantity,
        //     'is_delivery_free' => $isDeliveryFree,
        // ];
    }
}
