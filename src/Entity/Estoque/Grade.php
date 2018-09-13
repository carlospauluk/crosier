<?php

namespace App\Entity\Estoque;

use App\Entity\Base\EntityId;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 *
 * @ORM\Entity(repositoryClass="App\Repository\Estoque\GradeRepository")
 * @ORM\Table(name="est_grade")
 */
class Grade extends EntityId
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
     * @ORM\Column(name="codigo", type="integer", nullable=false)
     * @Assert\NotBlank(message="O campo 'codigo' deve ser informado")
     * @Assert\Range(min = 0)
     */
    private $codigo;

    /**
     *
     * @ORM\Column(name="obs", type="string", nullable=true, length=5000)
     */
    private $obs;

    /**
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Estoque\UnidadeProduto")
     * @ORM\JoinColumn(name="unidade_produto_id", nullable=false)
     *
     * @var $unidadeProduto UnidadeProduto
     */
    private $unidadeProduto;

    /**
     *
     * @var GradeTamanho[]|ArrayCollection
     *
     * @ORM\OneToMany(
     *      targetEntity="GradeTamanho",
     *      mappedBy="grade",
     *      orphanRemoval=true
     * )
     */
    private $tamanhos;

    public function __construct()
    {
        ORM\Annotation::class;
        Assert\All::class;
        $this->tamanhos = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getCodigo()
    {
        return $this->codigo;
    }

    public function setCodigo($codigo)
    {
        $this->codigo = $codigo;
    }

    public function getObs()
    {
        return $this->obs;
    }

    public function setObs($obs)
    {
        $this->obs = $obs;
    }

    public function getUnidadeProduto(): ?UnidadeProduto
    {
        return $this->unidadeProduto;
    }

    public function setUnidadeProduto(?UnidadeProduto $unidadeProduto)
    {
        $this->unidadeProduto = $unidadeProduto;
    }

    /**
     *
     * @return Collection|GradeTamanho[]
     */
    public function getTamanhos(): Collection
    {
        return $this->tamanhos;
    }
}
