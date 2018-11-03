<?php

namespace App\OC\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OcShippingCourier
 *
 * @ORM\Table(name="oc_shipping_courier")
 * @ORM\Entity
 */
class OcShippingCourier
{
    /**
     * @var int
     *
     * @ORM\Column(name="shipping_courier_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $shippingCourierId;

    /**
     * @var string
     *
     * @ORM\Column(name="shipping_courier_code", type="string", length=255, nullable=false)
     */
    private $shippingCourierCode = '';

    /**
     * @var string
     *
     * @ORM\Column(name="shipping_courier_name", type="string", length=255, nullable=false)
     */
    private $shippingCourierName = '';


}
