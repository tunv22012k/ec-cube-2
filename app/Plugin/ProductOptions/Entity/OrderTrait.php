<?php

namespace Plugin\ProductOptions\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Eccube\Annotation\EntityExtension;

/**
  * @EntityExtension("Eccube\Entity\Order")
 */
trait OrderTrait
{
    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="Plugin\ProductOptions\Entity\OrderOptions", mappedBy="Order", cascade={"remove"})
     */
    private $OrderOptions;

    private $OrderOptionsSetNew;

    /**
     * set OrderOptions.
     *
     * @return \Plugin\ProductOptions\Entity\OrderOptions
     */
    public function setOrderOptionsSetNew(OrderOptions $OrderOptions)
    {
        $this->OrderOptionsSetNew[] = $OrderOptions;

        return $this;
    }

    /**
     * Get OrderOptions.
     *
     * @return \Plugin\ProductOptions\Entity\OrderOptions
     */
    public function getOrderOptionsSetNew()
    {
        if (null === $this->OrderOptionsSetNew) {
            $this->OrderOptionsSetNew = new ArrayCollection();
        }

        return $this->OrderOptionsSetNew;
    }

    /**
     * Get OrderOptions.
     *
     * @return \Plugin\ProductOptions\Entity\OrderOptions
     */
    public function getOrderOptions()
    {
        if (null === $this->OrderOptions) {
            $this->OrderOptions = new ArrayCollection();
        }

        return $this->OrderOptions;
    }
}
