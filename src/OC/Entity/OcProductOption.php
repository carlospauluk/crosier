<?php

namespace App\OC\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OcProductOption
 *
 * @ORM\Table(name="oc_product_option")
 * @ORM\Entity
 */
class OcProductOption
{
    /**
     * @var int
     *
     * @ORM\Column(name="product_option_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $productOptionId;

    /**
     * @var int
     *
     * @ORM\Column(name="product_id", type="integer", nullable=false)
     */
    private $productId;

    /**
     * @var int
     *
     * @ORM\Column(name="option_id", type="integer", nullable=false)
     */
    private $optionId;

    /**
     * @var string
     *
     * @ORM\Column(name="value", type="text", length=65535, nullable=false)
     */
    private $value;

    /**
     * @var bool
     *
     * @ORM\Column(name="required", type="boolean", nullable=false)
     */
    private $required;


}
