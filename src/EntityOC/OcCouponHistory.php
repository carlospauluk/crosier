<?php

namespace App\EntityOC;

use Doctrine\ORM\Mapping as ORM;

/**
 * OcCouponHistory
 *
 * @ORM\Table(name="oc_coupon_history")
 * @ORM\Entity
 */
class OcCouponHistory
{
    /**
     * @var int
     *
     * @ORM\Column(name="coupon_history_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $couponHistoryId;

    /**
     * @var int
     *
     * @ORM\Column(name="coupon_id", type="integer", nullable=false)
     */
    private $couponId;

    /**
     * @var int
     *
     * @ORM\Column(name="order_id", type="integer", nullable=false)
     */
    private $orderId;

    /**
     * @var int
     *
     * @ORM\Column(name="customer_id", type="integer", nullable=false)
     */
    private $customerId;

    /**
     * @var string
     *
     * @ORM\Column(name="amount", type="decimal", precision=15, scale=4, nullable=false)
     */
    private $amount;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_added", type="datetime", nullable=false)
     */
    private $dateAdded;


}
