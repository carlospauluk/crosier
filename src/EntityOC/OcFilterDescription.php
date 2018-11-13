<?php

namespace App\EntityOC;

use Doctrine\ORM\Mapping as ORM;

/**
 * OcFilterDescription
 *
 * @ORM\Table(name="oc_filter_description")
 * @ORM\Entity(repositoryClass="App\Repository\OC\OcFilterDescriptionRepository")
 */
class OcFilterDescription
{
    /**
     * @var int
     *
     * @ORM\Column(name="filter_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $filterId;

    /**
     * @var int
     *
     * @ORM\Column(name="language_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $languageId;

    /**
     * @var int
     *
     * @ORM\Column(name="filter_group_id", type="integer", nullable=false)
     */
    private $filterGroupId;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=64, nullable=false)
     */
    private $name;

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
    public function getLanguageId(): int
    {
        return $this->languageId;
    }

    /**
     * @param int $languageId
     */
    public function setLanguageId(int $languageId): void
    {
        $this->languageId = $languageId;
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
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }


}
