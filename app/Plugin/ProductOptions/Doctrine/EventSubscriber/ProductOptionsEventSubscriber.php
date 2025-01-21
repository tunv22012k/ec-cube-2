<?php

namespace Plugin\ProductOptions\Doctrine\EventSubscriber;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Eccube\Entity\Cart;
use Eccube\Entity\Order;
use Eccube\Entity\ProductClass;
use Eccube\Repository\CartRepository;
use Eccube\Service\TaxRuleService;
use Plugin\ProductOptions\Entity\OrderOptions;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ProductOptionsEventSubscriber implements EventSubscriber
{
    /**
     * @var TaxRuleService
     */
    protected $container;

    /**
     * @var CartRepository
     */
    protected $cartRepository;

    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    protected $entityManager;

    /**
     * ProductOptionsEventSubscriber constructor.
     */
    public function __construct(ContainerInterface $container, CartRepository $cartRepository, EntityManagerInterface $entityManager)
    {
        $this->cartRepository   = $cartRepository;
        $this->entityManager    = $entityManager;
        $this->container        = $container;
    }

    // public function getTaxRuleService()
    // {
    //     return $this->container->get(TaxRuleService::class);
    // }

    public function getSubscribedEvents()
    {
        return [
            Events::prePersist,
            Events::postLoad,
            Events::postPersist,
            Events::preUpdate,
            Events::postUpdate,
        ];
    }

    /**
     * Sau khi Entity được tải từ cơ sở dữ liệu.
     * Đoạn ni mới load data lên
     *
     * @param LifecycleEventArgs $args
     *
     * @return [type]
     *
     */
    public function postLoad(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        // if ($entity instanceof Order) {
        //     dd($entity);
        // }

        // if ($entity instanceof Cart) {
        //     dd($entity);
        // }
    }

    /**
     * FUNCTION CREATE
     * Trước khi Entity được lưu vào cơ sở dữ liệu
     *
     * @param LifecycleEventArgs $args
     *
     * @return [type]
     *
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        if ($entity instanceof Order) {
            // // get data cart
            // $carts = $this->cartRepository->findOneBy(
            //     [
            //         'Customer'      => $entity->getCustomer(),
            //     ]
            // );

            // // get data cartOptions
            // $cartOptions = $carts->getCartOptions();

            // // process data cartOptions
            // foreach ($cartOptions as $key2 => $value2) {
            //     // set order options
            //     $orderOptions = new OrderOptions();
            //     $orderOptions->setProductClass($value2->getProductClass());
            //     $orderOptions->setOptions($value2->getOptions());
            //     $orderOptions->setCart($entity);
            //     $orderOptions->setQuantity($value2->getQuantity());
            //     $orderOptions->setPrice($value2->getPrice());

            //     // event upsert
            //     $this->entityManager->persist($orderOptions);
            // }

            // $this->entityManager->flush();
        }

        if ($entity instanceof Cart) {
            // dd($entity);
        }
    }

    /**
     * FUNCTION CREATE
     * Sau khi Entity được lưu thành công
     *
     * @param LifecycleEventArgs $args
     *
     * @return [type]
     *
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        if ($entity instanceof Order) {
            // get data cart
            $carts = $this->cartRepository->findOneBy(
                [
                    'Customer'      => $entity->getCustomer(),
                ]
            );

            // get data cartOptions
            $cartOptions = $carts->getCartOptions();

            // process data cartOptions
            foreach ($cartOptions as $key2 => $value2) {
                // caculate price
                $totalPriceOptions = $value2->getPrice() * $value2->getQuantity();
                $entity->setTotal($totalPriceOptions + $entity->getTotal());
                $entity->setPaymentTotal($totalPriceOptions + $entity->getPaymentTotal());

                // set order options
                $orderOptions = new OrderOptions();
                $orderOptions->setProductClass($value2->getProductClass());
                $orderOptions->setOptions($value2->getOptions());
                $orderOptions->setCart($entity);
                $orderOptions->setQuantity($value2->getQuantity());
                $orderOptions->setPrice($value2->getPrice());

                // set data order
                $entity->setOrderOptionsSetNew($orderOptions);

                // event upsert
                $this->entityManager->persist($orderOptions);
            }

            $this->entityManager->flush();
        }

        if ($entity instanceof Cart) {
            // dd($entity);
        }
    }

    /**
     * FUNCTION UPDATE
     * Trước khi Entity được cập nhật.
     *
     * @param LifecycleEventArgs $args
     *
     * @return [type]
     *
     */
    public function preUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        if ($entity instanceof Order) {
            // get data cart
            $carts = $this->cartRepository->findOneBy(
                [
                    'pre_order_id'      => $entity->getPreOrderId(),
                ]
            );

            if (!empty($carts)) {
                // set data default
                $totalPriceOptions = 0;

                // get data cartOptions
                $cartOptions = $carts->getCartOptions();

                // process data cartOptions
                foreach ($cartOptions as $key2 => $value2) {
                    // caculate price
                    $totalPriceOptions += $value2->getPrice() * $value2->getQuantity();
                }

                $entity->setTotal($totalPriceOptions + $entity->getSubtotal() + $entity->getDeliveryFeeTotal());
                $entity->setPaymentTotal($totalPriceOptions + $entity->getSubtotal() + $entity->getDeliveryFeeTotal());
            }
        }

        if ($entity instanceof Cart) {
            // set data default
            $totalPriceCartItem = 0;
            $totalPriceOptions = 0;

            // caculate price cart item
            $listCartItem = $entity->getCartItems();
            foreach ($listCartItem as $key => $value) {
                $totalPriceCartItem += $value->getPrice() * $value->getQuantity();
            }

            // caculate price options
            $listCartOptions = $entity->getCartOptions();
            foreach ($listCartOptions as $key => $value) {
                $totalPriceOptions += $value->getPrice() * $value->getQuantity();
            }

            // set data
            $entity->setTotal($totalPriceOptions + $totalPriceCartItem);
        }
    }

    /**
     * FUNCTION UPDATE
     * Sau khi Entity được cập nhật.
     *
     * @param LifecycleEventArgs $args
     *
     * @return [type]
     *
     */
    public function postUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        if ($entity instanceof Order) {
            // get data cart
            $carts = $this->cartRepository->findOneBy(
                [
                    'pre_order_id'      => $entity->getPreOrderId(),
                ]
            );

            if (!empty($carts)) {
                // remove data OrderOptions
                $qb = $this->entityManager->createQueryBuilder();
                $qb->delete(OrderOptions::class, 'oo')->where('oo.order_id = :order_id')->setParameter('order_id', $entity->getId())->getQuery()->execute();

                // get data cartOptions
                $cartOptions = $carts->getCartOptions();

                // process data cartOptions
                foreach ($cartOptions as $key2 => $value2) {
                    // set order options
                    $orderOptions = new OrderOptions();
                    $orderOptions->setProductClass($value2->getProductClass());
                    $orderOptions->setOptions($value2->getOptions());
                    $orderOptions->setCart($entity);
                    $orderOptions->setQuantity($value2->getQuantity());
                    $orderOptions->setPrice($value2->getPrice());

                    // event upsert
                    $this->entityManager->persist($orderOptions);
                }

                $this->entityManager->flush();
            }
        }
    }
}
