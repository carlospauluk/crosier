<?php

namespace App\OC\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OcProductDiscount
 *
 * @ORM\Table(name="oc_product_discount", indexes={@ORM\Index(name="product_id", columns={"product_id"})})
 * @ORM\Entity
 */
class OcProductDiscount
{
    /**
     * @var int
     *
     * @ORM\Column(name="product_discount_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $productDiscountId;

    /**
     * @var int
     *
     * @ORM\Column(name="product_id", type="integer", nullable=false)
     */
    private $productId;

    /**
     * @var int
     *
     * @ORM\Column(name="customer_group_id", type="integer", nullable=false)
     */
    private $customerGroupId;

    /**
     * @var int
     *
     * @ORM\Column(name="quantity", type="integer", nullable=false)
     */
    private $quantity = '0';

    /**
     * @var int
     *
     * @ORM\Column(name="priority", type="integer", nullable=false, options={"default"="1"})
     */
    private $priority = '1';

    /**
     * @var string
     *
     * @ORM\Column(name="price", type="decimal", precision=15, scale=4, nullable=false, options={"default"="0.0000"})
     */
    private $price = '0.0000';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_start", type="date", nullable=false, options={"default"="0000-00-00"})
     */
    private $dateStart = '0000-00-00';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_end", type="date", nullable=false, options={"default"="0000-00-00"})
     */
    private $dateEnd = '0000-00-00';


}
