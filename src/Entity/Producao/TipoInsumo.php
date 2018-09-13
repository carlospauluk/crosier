<?php

namespace App\Entity\Producao;

use App\Entity\Base\EntityId;
use App\Entity\Estoque\UnidadeProduto;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 *
 * @ORM\Entity(repositoryClass="App\Repository\Producao\TipoInsumoRepository")
 * @ORM\Table(name="prod_tipo_insumo")
 */
class TipoInsumo extends EntityId
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
     * @ORM\Column(name="descricao", type="string", nullable=false, length=100)
     * @Assert\NotBlank(message="O campo 'descricao' deve ser informado")
     */
    private $descricao;

    /**
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Estoque\UnidadeProduto")
     * @ORM\JoinColumn(name="unidade_produto_id", nullable=false)
     *
     * @var $unidadeProduto UnidadeProduto
     */
    private $unidadeProduto;

    public function __construct()
    {
        ORM\Annotation::class;
        Assert\All::class;
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

    public function getDescricao()
    {
        return $this->descricao;
    }

    public function setDescricao($descricao)
    {
        $this->descricao = $descricao;
    }

    public function getUnidadeProduto(): ?UnidadeProduto
    {
        return $this->unidadeProduto;
    }

    public function setUnidadeProduto(?UnidadeProduto $unidadeProduto)
    {
        $this->unidadeProduto = $unidadeProduto;
    }
}