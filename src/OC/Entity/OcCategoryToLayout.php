<?php

namespace App\OC\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OcCategoryToLayout
 *
 * @ORM\Table(name="oc_category_to_layout")
 * @ORM\Entity
 */
class OcCategoryToLayout
{
    /**
     * @var int
     *
     * @ORM\Column(name="category_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $categoryId;

    /**
     * @var int
     *
     * @ORM\Column(name="store_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $storeId;

    /**
     * @var int
     *
     * @ORM\Column(name="layout_id", type="integer", nullable=false)
     */
    private $layoutId;


}
