<?php

namespace Plugin\ProductOptions\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Eccube\Annotation\EntityExtension;

/**
  * @EntityExtension("Eccube\Entity\CartItem")
 */
trait CartItemTrait
{
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $Options;

    /**
     * Set options.
     *
     * @param \Plugin\ProductOptions\Entity\Options|null $options
     *
     * @return ProductOptions
     */
    public function setOptions($Options = null)
    {
        $this->Options = $Options;

        return $this;
    }


    /**
     * Get Options.
     *
     * @return \Plugin\ProductOptions\Entity\Options
     */
    public function getOptions()
    {
        if (null === $this->Options) {
            $this->Options = new ArrayCollection();
        }

        return $this->Options;
    }
}
