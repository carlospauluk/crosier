<?php

namespace App\EntityOC;

use Doctrine\ORM\Mapping as ORM;

/**
 * OcProductFilter
 *
 * @ORM\Table(name="oc_product_filter")
 * @ORM\Entity
 */
class OcProductFilter
{
    /**
     * @var int
     *
     * @ORM\Column(name="product_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $productId;

    /**
     * @var int
     *
     * @ORM\Column(name="filter_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $filterId;

    /**
     * @return int
     */
    public function getProductId(): int
    {
        return $this->productId;
    }

    /**
     * @param int $productId
     */
    public function setProductId($productId): void
    {
        $this->productId = $productId;
    }

    /**
     * @return int
     */
    public function getFilterId(): int
    {
        return $this->filterId;
    }

    /**
     * @param int $filterId
     */
    public function setFilterId(int $filterId): void
    {
        $this->filterId = $filterId;
    }


}
