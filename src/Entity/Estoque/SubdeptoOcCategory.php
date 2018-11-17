<?php

namespace App\Entity\Estoque;

use Doctrine\ORM\Mapping as ORM;

/**
 *
 * @ORM\Entity(repositoryClass="App\Repository\Estoque\SubdeptoOcCategoryRepository")
 * @ORM\Table(name="est_subdepto_oc_category")
 */
class SubdeptoOcCategory
{

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="App\Entity\Estoque\Subdepto")
     * @ORM\JoinColumn(name="est_subdepto_id", nullable=false)
     *
     * @var $subdepto Subdepto
     */
    private $subdepto;

    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(name="oc_category_id", type="integer", nullable=false)
     */
    private $categoryId;

    /**
     * @return Subdepto
     */
    public function getSubdepto(): Subdepto
    {
        return $this->subdepto;
    }

    /**
     * @param Subdepto $subdepto
     */
    public function setSubdepto(Subdepto $subdepto): void
    {
        $this->subdepto = $subdepto;
    }

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


}