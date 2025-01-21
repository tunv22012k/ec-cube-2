<?php

namespace Plugin\ProductOptions;

use Doctrine\ORM\EntityManagerInterface;
use Eccube\Entity\Cart;
use Eccube\Entity\ProductClass;
use Eccube\Event\EccubeEvents;
use Eccube\Event\TemplateEvent;
use Eccube\Service\CartService;
use Plugin\ProductOptions\Entity\CartOptions;
use Plugin\ProductOptions\Entity\Options;
use Plugin\ProductOptions\Entity\ProductOptions;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class CustomProductOptionsEvent implements EventSubscriberInterface
{
    private $entityManager;

    /**
     * @var CartService
     */
    protected $cartService;

    /**
     * CustomProductOptionsEvent __construct
     *
     * @param EntityManagerInterface $entityManager
     *
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        CartService $cartService
    ) {
        $this->entityManager = $entityManager;
        $this->cartService = $cartService;
    }

    public static function getSubscribedEvents()
    {
        return [
            '@admin/Product/product.twig'                   => 'onRenderAdminProduct',
            'Product/detail.twig'                           => 'onRenderUserDetailProduct',
            'Cart/index.twig'                               => 'onRenderUserListCart',
            'Shopping/index.twig'                           => 'onRenderShoppingIndex',
            'Shopping/confirm.twig'                         => 'onRenderShoppingConfirm',
            EccubeEvents::ADMIN_PRODUCT_EDIT_COMPLETE       => 'onProductEditComplete',
            EccubeEvents::FRONT_PRODUCT_CART_ADD_COMPLETE   => 'onProductCartAddComplete',
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
     * show data options in page user detail product
     *
     * @param TemplateEvent $event
     */
    public function onRenderUserDetailProduct(TemplateEvent $event)
    {
        $event->addSnippet('@ProductOptions/default/Product/custom_detail_cart_options.twig');
    }

    /**
     * show data options in page user list cart
     *
     * @param TemplateEvent $event
     */
    public function onRenderUserListCart(TemplateEvent $event)
    {
        $event->addSnippet('@ProductOptions/default/Product/custom_list_cart_options.twig');
    }

    /**
     * show data options in page user shopping
     *
     * @param TemplateEvent $event
     */
    public function onRenderShoppingIndex(TemplateEvent $event)
    {
        $event->addSnippet('@ProductOptions/default/Product/custom_shoping_index.twig');
    }

    /**
     * show data options in page user shopping confirm
     *
     * @param TemplateEvent $event
     */
    public function onRenderShoppingConfirm(TemplateEvent $event)
    {
        $event->addSnippet('@ProductOptions/default/Product/custom_shoping_confirm.twig');
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

    /**
     * event add cart
     *
     */
    public function onProductCartAddComplete($event)
    {
        // get data cart
        $Carts = $this->cartService->getCarts();
        $cart = $Carts[0];

        // get data event
        $form = $event->getArgument('form');
        $formData = $form->getData();

        // set price default
        $totalPrice = 0;

        // processing data options
        foreach ($formData['Options'] as $key => $value) {
            if ($value > 0) {
                // set data cart options
                $cartOptions = new CartOptions();

                $productClassEntity = $this->entityManager->getRepository(ProductClass::class)->find($formData['product_class_id']);
                $cartOptions->setProductClass($productClassEntity);

                $optionsEntity = $this->entityManager->getRepository(Options::class)->find($key);
                $cartOptions->setOptions($optionsEntity);

                $cartOptions->setCart($cart);
                $cartOptions->setQuantity($value);
                $cartOptions->setPrice($optionsEntity->getFee() * $value);

                // set data price
                $totalPrice += (int)$optionsEntity->getFee();

                // event upsert
                $this->entityManager->persist($cartOptions);
                $this->entityManager->flush();
            }
        }

        if ($totalPrice > 0) {
            // reupdate cart
            $cart->setTotalPrice($cart->getTotalPrice() + $totalPrice);

            // event upsert
            $this->entityManager->persist($cart);
            $this->entityManager->flush();
        }
    }
}
