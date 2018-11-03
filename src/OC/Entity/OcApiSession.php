<?php

namespace App\OC\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OcApiSession
 *
 * @ORM\Table(name="oc_api_session")
 * @ORM\Entity
 */
class OcApiSession
{
    /**
     * @var int
     *
     * @ORM\Column(name="api_session_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $apiSessionId;

    /**
     * @var int
     *
     * @ORM\Column(name="api_id", type="integer", nullable=false)
     */
    private $apiId;

    /**
     * @var string
     *
     * @ORM\Column(name="session_id", type="string", length=32, nullable=false)
     */
    private $sessionId;

    /**
     * @var string
     *
     * @ORM\Column(name="ip", type="string", length=40, nullable=false)
     */
    private $ip;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_added", type="datetime", nullable=false)
     */
    private $dateAdded;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_modified", type="datetime", nullable=false)
     */
    private $dateModified;


}
