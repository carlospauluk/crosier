<?php

namespace App\OC\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OcOrderShipment
 *
 * @ORM\Table(name="oc_order_shipment")
 * @ORM\Entity
 */
class OcOrderShipment
{
    /**
     * @var int
     *
     * @ORM\Column(name="order_shipment_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $orderShipmentId;

    /**
     * @var int
     *
     * @ORM\Column(name="order_id", type="integer", nullable=false)
     */
    private $orderId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_added", type="datetime", nullable=false)
     */
    private $dateAdded;

    /**
     * @var string
     *
     * @ORM\Column(name="shipping_courier_id", type="string", length=255, nullable=false)
     */
    private $shippingCourierId = '';

    /**
     * @var string
     *
     * @ORM\Column(name="tracking_number", type="string", length=255, nullable=false)
     */
    private $trackingNumber = '';


}
