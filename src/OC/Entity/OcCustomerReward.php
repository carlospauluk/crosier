<?php

namespace App\OC\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OcCustomerReward
 *
 * @ORM\Table(name="oc_customer_reward")
 * @ORM\Entity
 */
class OcCustomerReward
{
    /**
     * @var int
     *
     * @ORM\Column(name="customer_reward_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $customerRewardId;

    /**
     * @var int
     *
     * @ORM\Column(name="customer_id", type="integer", nullable=false)
     */
    private $customerId = '0';

    /**
     * @var int
     *
     * @ORM\Column(name="order_id", type="integer", nullable=false)
     */
    private $orderId = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", length=65535, nullable=false)
     */
    private $description;

    /**
     * @var int
     *
     * @ORM\Column(name="points", type="integer", nullable=false)
     */
    private $points = '0';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_added", type="datetime", nullable=false)
     */
    private $dateAdded;


}
