<?php

namespace Plugin\ProductOptions\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Eccube\Annotation\EntityExtension;

/**
  * @EntityExtension("Eccube\Entity\Product")
 */
trait ProductTrait
{
    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="Plugin\ProductOptions\Entity\ProductOptions", mappedBy="product", cascade={"persist", "remove"})
     */
    private $productOptions;

    /**
     * Get ProductOptions.
     *
     * @return \Plugin\ProductOptions\Entity\ProductOptions
     */
    public function getProductOptions()
    {
        if (null === $this->productOptions) {
            $this->productOptions = new ArrayCollection();
        }

        return $this->productOptions;
    }
}
