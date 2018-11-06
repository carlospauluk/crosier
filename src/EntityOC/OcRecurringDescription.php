<?php

namespace App\EntityOC;

use Doctrine\ORM\Mapping as ORM;

/**
 * OcRecurringDescription
 *
 * @ORM\Table(name="oc_recurring_description")
 * @ORM\Entity
 */
class OcRecurringDescription
{
    /**
     * @var int
     *
     * @ORM\Column(name="recurring_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $recurringId;

    /**
     * @var int
     *
     * @ORM\Column(name="language_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $languageId;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    private $name;


}
