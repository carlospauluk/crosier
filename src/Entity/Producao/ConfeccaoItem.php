<?php

namespace App\Entity\Producao;

use App\Entity\Base\EntityId;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 *
 * @ORM\Entity(repositoryClass="App\Repository\Producao\ConfeccaoItemRepository")
 * @ORM\Table(name="prod_confeccao_item")
 */
class ConfeccaoItem extends EntityId
{

    /**
     *
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Producao\Confeccao")
     * @ORM\JoinColumn(name="confeccao_id", nullable=false)
     *
     * @var $confeccao Confeccao
     */
    private $confeccao;

    /**
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Producao\Insumo")
     * @ORM\JoinColumn(name="insumo_id", nullable=false)
     *
     * @var $insumo Insumo
     */
    private $insumo;

    /**
     *
     * @var ConfeccaoItemQtde[]|ArrayCollection
     *
     * @ORM\OneToMany(
     *      targetEntity="ConfeccaoItemQtde",
     *      mappedBy="confeccaoItem",
     *      orphanRemoval=true
     * )
     */
    private $qtdesGrade;


    public function __construct()
    {
        ORM\Annotation::class;
        Assert\All::class;
        $this->qtdesGrade = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getConfeccao()
    {
        return $this->confeccao;
    }

    public function setConfeccao($confeccao)
    {
        $this->confeccao = $confeccao;
    }

    public function getInsumo()
    {
        return $this->insumo;
    }

    public function setInsumo($insumo)
    {
        $this->insumo = $insumo;
    }

    /**
     *
     * @return Collection|ConfeccaoItemQtde[]
     */
    public function getQtdesGrade(): Collection
    {
        return $this->qtdesGrade;
    }
}