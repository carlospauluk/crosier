<?php

namespace App\EntityOC;

use Doctrine\ORM\Mapping as ORM;

/**
 * OcFilter
 *
 * @ORM\Table(name="oc_filter")
 * @ORM\Entity
 */
class OcFilter
{
    /**
     * @var int
     *
     * @ORM\Column(name="filter_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $filterId;

    /**
     * @var int
     *
     * @ORM\Column(name="filter_group_id", type="integer", nullable=false)
     */
    private $filterGroupId;

    /**
     * @var int
     *
     * @ORM\Column(name="sort_order", type="integer", nullable=false)
     */
    private $sortOrder;

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

    /**
     * @return int
     */
    public function getFilterGroupId(): int
    {
        return $this->filterGroupId;
    }

    /**
     * @param int $filterGroupId
     */
    public function setFilterGroupId(int $filterGroupId): void
    {
        $this->filterGroupId = $filterGroupId;
    }

    /**
     * @return int
     */
    public function getSortOrder(): int
    {
        return $this->sortOrder;
    }

    /**
     * @param int $sortOrder
     */
    public function setSortOrder(int $sortOrder): void
    {
        $this->sortOrder = $sortOrder;
    }


}
