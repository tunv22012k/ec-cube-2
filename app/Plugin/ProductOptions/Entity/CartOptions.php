<?php

namespace Plugin\ProductOptions\Entity;

use Doctrine\ORM\Mapping as ORM;
use Eccube\Entity\AbstractEntity;
use Eccube\Entity\Cart;
use Eccube\Entity\ProductClass;

/**
 * CartOptions
 *
 * @ORM\Table(name="dtb_cart_options")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discriminator_type", type="string", length=255)
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity(repositoryClass="Plugin\ProductOptions\Repository\CartOptionsRepository")
 */
class CartOptions extends AbstractEntity
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", options={"unsigned":true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="price", type="decimal", precision=12, scale=2, options={"default":0})
     */
    private $price = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="quantity", type="decimal", precision=10, scale=0, options={"default":0})
     */
    private $quantity = 0;

    /**
     * @var \Eccube\Entity\ProductClass
     *
     * @ORM\ManyToOne(targetEntity="Eccube\Entity\ProductClass")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="product_class_id", referencedColumnName="id")
     * })
     */
    private $ProductClass;

    /**
     * @var \Eccube\Entity\Cart
     *
     * @ORM\ManyToOne(targetEntity="Eccube\Entity\Cart", inversedBy="CartOptions")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="cart_id", referencedColumnName="id")
     * })
     */
    private $Cart;

    /**
     * @var \Plugin\ProductOptions\Entity\Options
     *
     * @ORM\ManyToOne(targetEntity="Plugin\ProductOptions\Entity\Options", inversedBy="CartOptions")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="option_id", referencedColumnName="id", nullable=false)
     * })
     */
    private $options;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="create_date", type="datetimetz")
     */
    private $create_date;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="update_date", type="datetimetz")
     */
    private $update_date;

    /**
     * sessionのシリアライズのために使われる
     *
     * @var int
     */
    private $product_class_id;

    /**
     * Constructor
     */
    public function __construct()
    {
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param  integer  $price
     *
     * @return CartItem
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * @return string
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param  integer  $quantity
     *
     * @return CartItem
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * @return string
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @return integer
     */
    public function getTotalPrice()
    {
        return $this->getPrice() * $this->getQuantity();
    }

    /**
     * 商品明細かどうか.
     *
     * @return boolean 商品明細の場合 true
     */
    public function isProduct()
    {
        return true;
    }

    /**
     * 送料明細かどうか.
     *
     * @return boolean 送料明細の場合 true
     */
    public function isDeliveryFee()
    {
        return false;
    }

    /**
     * 手数料明細かどうか.
     *
     * @return boolean 手数料明細の場合 true
     */
    public function isCharge()
    {
        return false;
    }

    /**
     * 値引き明細かどうか.
     *
     * @return boolean 値引き明細の場合 true
     */
    public function isDiscount()
    {
        return false;
    }

    /**
     * 税額明細かどうか.
     *
     * @return boolean 税額明細の場合 true
     */
    public function isTax()
    {
        return false;
    }

    /**
     * ポイント明細かどうか.
     *
     * @return boolean ポイント明細の場合 true
     */
    public function isPoint()
    {
        return false;
    }

    public function getOrderItemType()
    {
        // TODO OrderItemType::PRODUCT
        $ItemType = new \Eccube\Entity\Master\OrderItemType();

        return $ItemType;
    }

    /**
     * @param ProductClass $ProductClass
     *
     * @return $this
     */
    public function setProductClass(ProductClass $ProductClass)
    {
        $this->ProductClass = $ProductClass;

        $this->product_class_id = is_object($ProductClass) ?
        $ProductClass->getId() : null;

        return $this;
    }

    /**
     * @return ProductClass
     */
    public function getProductClass()
    {
        return $this->ProductClass;
    }

    /**
     * @return int|null
     */
    public function getProductClassId()
    {
        return $this->product_class_id;
    }

    public function getPriceIncTax()
    {
        // TODO ItemInterfaceに追加, Cart::priceは税込み金額が入っているので,フィールドを分ける必要がある
        return $this->price;
    }

    /**
     * @return Cart
     */
    public function getCart()
    {
        return $this->Cart;
    }

    /**
     * @param Cart $Cart
     */
    public function setCart(Cart $Cart)
    {
        $this->Cart = $Cart;

        return $this;
    }

    /**
     * Set options.
     *
     * @param \Plugin\ProductOptions\Entity\Options|null $options
     *
     * @return ProductOptions
     */
    public function setOptions(Options $options = null)
    {
        $this->options = $options;

        return $this;
    }

    /**
     * Get options.
     *
     * @return \Plugin\ProductOptions\Entity\Options|null
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Set createDate.
     *
     * @param \DateTime $createDate
     *
     * @return Options
     */
    public function setCreateDate($createDate)
    {
        $this->create_date = $createDate;

        return $this;
    }

    /**
     * Get createDate.
     *
     * @return \DateTime
     */
    public function getCreateDate()
    {
        return $this->create_date;
    }

    /**
     * Set updateDate.
     *
     * @param \DateTime $updateDate
     *
     * @return Options
     */
    public function setUpdateDate($updateDate)
    {
        $this->update_date = $updateDate;

        return $this;
    }

    /**
     * Get updateDate.
     *
     * @return \DateTime
     */
    public function getUpdateDate()
    {
        return $this->update_date;
    }
}
