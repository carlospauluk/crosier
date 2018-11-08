<?php

namespace App\EntityOC;

use Doctrine\ORM\Mapping as ORM;

/**
 * OcOptionValue
 *
 * @ORM\Table(name="oc_option_value")
 * @ORM\Entity
 */
class OcOptionValue
{
    /**
     * @var int
     *
     * @ORM\Column(name="option_value_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $optionValueId;

    /**
     * @var int
     *
     * @ORM\Column(name="option_id", type="integer", nullable=false)
     */
    private $optionId;

    /**
     * @var string
     *
     * @ORM\Column(name="image", type="string", length=255, nullable=false)
     */
    private $image;

    /**
     * @var int
     *
     * @ORM\Column(name="sort_order", type="integer", nullable=false)
     */
    private $sortOrder;

    /**
     * @return int
     */
    public function getOptionValueId(): int
    {
        return $this->optionValueId;
    }

    /**
     * @param int $optionValueId
     */
    public function setOptionValueId(int $optionValueId): void
    {
        $this->optionValueId = $optionValueId;
    }

    /**
     * @return int
     */
    public function getOptionId(): int
    {
        return $this->optionId;
    }

    /**
     * @param int $optionId
     */
    public function setOptionId(int $optionId): void
    {
        $this->optionId = $optionId;
    }

    /**
     * @return string
     */
    public function getImage(): string
    {
        return $this->image;
    }

    /**
     * @param string $image
     */
    public function setImage(string $image): void
    {
        $this->image = $image;
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
