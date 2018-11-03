<?php

namespace App\OC\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OcReturnHistory
 *
 * @ORM\Table(name="oc_return_history")
 * @ORM\Entity
 */
class OcReturnHistory
{
    /**
     * @var int
     *
     * @ORM\Column(name="return_history_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $returnHistoryId;

    /**
     * @var int
     *
     * @ORM\Column(name="return_id", type="integer", nullable=false)
     */
    private $returnId;

    /**
     * @var int
     *
     * @ORM\Column(name="return_status_id", type="integer", nullable=false)
     */
    private $returnStatusId;

    /**
     * @var bool
     *
     * @ORM\Column(name="notify", type="boolean", nullable=false)
     */
    private $notify;

    /**
     * @var string
     *
     * @ORM\Column(name="comment", type="text", length=65535, nullable=false)
     */
    private $comment;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_added", type="datetime", nullable=false)
     */
    private $dateAdded;


}
