<?php

namespace App\EntityOC;

use Doctrine\ORM\Mapping as ORM;

/**
 * OcProductReward
 *
 * @ORM\Table(name="oc_product_reward")
 * @ORM\Entity
 */
class OcProductReward
{
    /**
     * @var int
     *
     * @ORM\Column(name="product_reward_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $productRewardId;

    /**
     * @var int
     *
     * @ORM\Column(name="product_id", type="integer", nullable=false)
     */
    private $productId = '0';

    /**
     * @var int
     *
     * @ORM\Column(name="customer_group_id", type="integer", nullable=false)
     */
    private $customerGroupId = '0';

    /**
     * @var int
     *
     * @ORM\Column(name="points", type="integer", nullable=false)
     */
    private $points = '0';


}
