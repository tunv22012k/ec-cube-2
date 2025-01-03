<?php

namespace Plugin\ProductOptions\Entity;

use Doctrine\ORM\Mapping as ORM;
use Eccube\Entity\AbstractEntity;
use Eccube\Entity\Product;

/**
 * ProductOptions
 *
 * @ORM\Table(name="dtb_product_options")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discriminator_type", type="string", length=255)
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity(repositoryClass="Plugin\ProductOptions\Repository\ProductOptionsRepository")
 */
class ProductOptions extends AbstractEntity
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", options={"unsigned":true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="product_id", type="integer", options={"unsigned":true})
     */
    private $product_id;

    /**
     * @var int
     *
     * @ORM\Column(name="option_id", type="integer", options={"unsigned":true})
     */
    private $option_id;

    /**
     * @var \Eccube\Entity\Product
     *
     * @ORM\ManyToOne(targetEntity="Eccube\Entity\Product", inversedBy="productOptions")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="product_id", referencedColumnName="id", nullable=false)
     * })
     */
    private $product;

    /**
     * @var \Plugin\ProductOptions\Entity\Options
     *
     * @ORM\ManyToOne(targetEntity="Plugin\ProductOptions\Entity\Options", inversedBy="productOptions")
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
     * Constructor
     */
    public function __construct()
    {
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set productId.
     *
     * @param int $productId
     *
     * @return ProductCategory
     */
    public function setProductId($productId)
    {
        $this->product_id = $productId;

        return $this;
    }

    /**
     * Get productId.
     *
     * @return int
     */
    public function getProductId()
    {
        return $this->product_id;
    }

    /**
     * Set optionId.
     *
     * @param int $optionId
     *
     * @return ProductCategory
     */
    public function setOptionId($optionId)
    {
        $this->option_id = $optionId;

        return $this;
    }

    /**
     * Get optionId.
     *
     * @return int
     */
    public function getOptionId()
    {
        return $this->option_id;
    }

    /**
     * Set Product.
     *
     * @param Eccube\Entity\Product $product
     *
     * @return CouponDetail
     */
    public function setProduct(Product $product = null)
    {
        $this->product = $product;

        return $this;
    }

    /**
     * Get Product.
     *
     * @return Eccube\Entity\Product
     */
    public function getProduct()
    {
        return $this->product;
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
