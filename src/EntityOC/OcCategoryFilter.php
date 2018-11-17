<?php

namespace App\EntityOC;

use Doctrine\ORM\Mapping as ORM;

/**
 * OcCategoryFilter
 *
 * @ORM\Table(name="oc_category_filter")
 * @ORM\Entity(repositoryClass="App\Repository\OC\OcCategoryFilterRepository")
 */
class OcCategoryFilter
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
     * @ORM\Column(name="filter_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $filterId;

    /**
     * @return int
     */
    public function getCategoryId(): int
    {
        return $this->categoryId;
    }

    /**
     * @param int $categoryId
     */
    public function setCategoryId(int $categoryId): void
    {
        $this->categoryId = $categoryId;
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
