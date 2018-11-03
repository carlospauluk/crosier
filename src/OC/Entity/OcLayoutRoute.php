<?php

namespace App\OC\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OcLayoutRoute
 *
 * @ORM\Table(name="oc_layout_route")
 * @ORM\Entity
 */
class OcLayoutRoute
{
    /**
     * @var int
     *
     * @ORM\Column(name="layout_route_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $layoutRouteId;

    /**
     * @var int
     *
     * @ORM\Column(name="layout_id", type="integer", nullable=false)
     */
    private $layoutId;

    /**
     * @var int
     *
     * @ORM\Column(name="store_id", type="integer", nullable=false)
     */
    private $storeId;

    /**
     * @var string
     *
     * @ORM\Column(name="route", type="string", length=64, nullable=false)
     */
    private $route;


}
