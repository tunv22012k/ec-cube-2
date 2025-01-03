<?php

namespace Plugin\ProductOptions\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Eccube\Entity\AbstractEntity;

/**
 * Options
 *
 * @ORM\Table(name="dtb_options")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discriminator_type", type="string", length=255)
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity(repositoryClass="Plugin\ProductOptions\Repository\OptionsRepository")
 */
class Options extends AbstractEntity
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
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="fee", type="decimal", precision=12, scale=2, options={"unsigned":true,"default":0})
     */
    private $fee = 0;

    /**
     * @ORM\Column(name="use_flg",type="boolean",nullable=false,options={"default":false})
     *
     * @var integer
     */
    private $use_flg = false;

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
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="Plugin\ProductOptions\Entity\ProductOptions", mappedBy="options", cascade={"persist", "remove"})
     */
    private $productOptions;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->productOptions = new ArrayCollection();
    }

    /**
     * Set id.
     *
     * @param string $id
     *
     * @return Options
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
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
     * Set name.
     *
     * @param string $name
     *
     * @return Options
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get fee.
     *
     * @return integer
     */
    public function getFee()
    {
        return $this->fee;
    }

    /**
     * Set fee.
     *
     * @param integer $fee
     *
     * @return Options
     */
    public function setFee($fee)
    {
        $this->fee = $fee;

        return $this;
    }

    /**
     * Set use_flg.
     *
     * @param boolean $use_flg
     *
     * @return Options
     */
    public function setUseFlg($use_flg = false)
    {
        $this->use_flg = $use_flg;

        return $this;
    }

    /**
     * Get use_flg.
     *
     * @return boolean
     */
    public function getUseFlg()
    {
        return $this->use_flg;
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

    /**
     * Get ProductOptions.
     *
     * @return \Plugin\ProductOptions\Entity\ProductOptions
     */
    public function getProductOptions()
    {
        return $this->productOptions;
    }
}
