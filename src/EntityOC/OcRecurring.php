<?php

namespace App\EntityOC;

use Doctrine\ORM\Mapping as ORM;

/**
 * OcRecurring
 *
 * @ORM\Table(name="oc_recurring")
 * @ORM\Entity
 */
class OcRecurring
{
    /**
     * @var int
     *
     * @ORM\Column(name="recurring_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $recurringId;

    /**
     * @var string
     *
     * @ORM\Column(name="price", type="decimal", precision=10, scale=4, nullable=false)
     */
    private $price;

    /**
     * @var string
     *
     * @ORM\Column(name="frequency", type="string", length=0, nullable=false)
     */
    private $frequency;

    /**
     * @var int
     *
     * @ORM\Column(name="duration", type="integer", nullable=false, options={"unsigned"=true})
     */
    private $duration;

    /**
     * @var int
     *
     * @ORM\Column(name="cycle", type="integer", nullable=false, options={"unsigned"=true})
     */
    private $cycle;

    /**
     * @var bool
     *
     * @ORM\Column(name="trial_status", type="boolean", nullable=false)
     */
    private $trialStatus;

    /**
     * @var string
     *
     * @ORM\Column(name="trial_price", type="decimal", precision=10, scale=4, nullable=false)
     */
    private $trialPrice;

    /**
     * @var string
     *
     * @ORM\Column(name="trial_frequency", type="string", length=0, nullable=false)
     */
    private $trialFrequency;

    /**
     * @var int
     *
     * @ORM\Column(name="trial_duration", type="integer", nullable=false, options={"unsigned"=true})
     */
    private $trialDuration;

    /**
     * @var int
     *
     * @ORM\Column(name="trial_cycle", type="integer", nullable=false, options={"unsigned"=true})
     */
    private $trialCycle;

    /**
     * @var bool
     *
     * @ORM\Column(name="status", type="boolean", nullable=false)
     */
    private $status;

    /**
     * @var int
     *
     * @ORM\Column(name="sort_order", type="integer", nullable=false)
     */
    private $sortOrder;


}
