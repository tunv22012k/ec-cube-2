<?php

namespace Plugin\ProductOptions\Controller;

use Eccube\Controller\ShoppingController as BaseShoppingController;
use Eccube\Repository\BaseInfoRepository;
use Eccube\Repository\CartRepository;
use Eccube\Repository\OrderRepository;
use Eccube\Repository\TradeLawRepository;
use Eccube\Service\CartService;
use Eccube\Service\MailService;
use Eccube\Service\OrderHelper;
use Eccube\Service\PurchaseFlow\PurchaseFlow;
use Plugin\ProductOptions\Entity\CartOptions;
use Plugin\ProductOptions\Repository\CartOptionsRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Component\Routing\Annotation\Route;

class CustomShoppingController extends BaseShoppingController
{
    /**
     * @var CartRepository
     */
    protected $cartRepository;

    /**
     * @var CartOptionsRepository
     */
    protected $cartOptionsRepository;

    public function __construct(
        CartService $cartService,
        MailService $mailService,
        OrderRepository $orderRepository,
        OrderHelper $orderHelper,
        ContainerInterface $serviceContainer,
        TradeLawRepository $tradeLawRepository,
        RateLimiterFactory $shoppingConfirmIpLimiter,
        RateLimiterFactory $shoppingConfirmCustomerLimiter,
        RateLimiterFactory $shoppingCheckoutIpLimiter,
        RateLimiterFactory $shoppingCheckoutCustomerLimiter,
        BaseInfoRepository $baseInfoRepository,
        CartRepository $cartRepository,
        CartOptionsRepository $cartOptionsRepository
    ) {
        $this->cartRepository = $cartRepository;
        $this->cartOptionsRepository = $cartOptionsRepository;

        parent::__construct(
            $cartService,
            $mailService,
            $orderRepository,
            $orderHelper,
            $serviceContainer,
            $tradeLawRepository,
            $shoppingConfirmIpLimiter,
            $shoppingConfirmCustomerLimiter,
            $shoppingCheckoutIpLimiter,
            $shoppingCheckoutCustomerLimiter,
            $baseInfoRepository
        );
    }

    /**
     * 注文手続き画面を表示する
     *
     * 未ログインまたはRememberMeログインの場合はログイン画面に遷移させる.
     * ただし、非会員でお客様情報を入力済の場合は遷移させない.
     *
     * カート情報から受注データを生成し, `pre_order_id`でカートと受注の紐付けを行う.
     * 既に受注が生成されている場合(pre_order_idで取得できる場合)は, 受注の生成を行わずに画面を表示する.
     *
     * purchaseFlowの集計処理実行後, warningがある場合はカートど同期をとるため, カートのPurchaseFlowを実行する.
     *
     * @Route("/shopping", name="shopping", methods={"GET"})
     * @Template("Shopping/index.twig")
     */
    public function index(PurchaseFlow $cartPurchaseFlow)
    {
        // call data parent
        $response = parent::index($cartPurchaseFlow);

        // get data parent
        // $Order = $response['Order'];

        // // get data cart
        // $cart = $this->cartRepository->findOneBy([
        //     'pre_order_id'      => $Order->getPreOrderId(),
        // ]);

        // // get data cart options
        // $cartOptions = $cart->getCartOptions();

        // // set data cart
        // $response['cartOptions'] = $cartOptions;

        return $response;

        // return [
        //     'form' => $form->createView(),
        //     'Order' => $Order,
        //     'activeTradeLaws' => $activeTradeLaws,
        // ];
    }

    /**
     * 注文確認画面を表示する.
     *
     * ここではPaymentMethod::verifyがコールされます.
     * PaymentMethod::verifyではクレジットカードの有効性チェック等, 注文手続きを進められるかどうかのチェック処理を行う事を想定しています.
     * PaymentMethod::verifyでエラーが発生した場合は, 注文手続き画面へリダイレクトします.
     *
     * @Route("/shopping/confirm", name="shopping_confirm", methods={"POST"})
     * @Template("Shopping/confirm.twig")
     */
    public function confirm(Request $request)
    {
        // call data parent
        $response = parent::confirm($request);

        // if ($response instanceof RedirectResponse) {
        //     return $response;
        // } elseif (is_array($response)) {
        //     // get data parent
        //     $Order = $response['Order'];

        //     // get data cart
        //     $cart = $this->cartRepository->findOneBy([
        //         'pre_order_id'      => $Order->getPreOrderId(),
        //     ]);

        //     // get data cart options
        //     $cartOptions = $cart->getCartOptions();

        //     // set data cart
        //     $response['cartOptions'] = $cartOptions;

        //     return $response;
        // }

        return $response;
    }
}
