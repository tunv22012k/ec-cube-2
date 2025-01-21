<?php

namespace Plugin\ProductOptions\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Eccube\Annotation\EntityExtension;

/**
  * @EntityExtension("Eccube\Entity\Cart")
 */
trait CartTrait
{
    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="Plugin\ProductOptions\Entity\CartOptions", mappedBy="Cart", cascade={"remove"})
     */
    private $CartOptions;

    /**
     * Get CartOptions.
     *
     * @return \Plugin\ProductOptions\Entity\CartOptions
     */
    public function getCartOptions()
    {
        if (null === $this->CartOptions) {
            $this->CartOptions = new ArrayCollection();
        }

        return $this->CartOptions;
    }
}
