<?php
namespace App\Entity\Producao;

use App\Entity\Base\EntityId;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use App\Entity\Estoque\UnidadeProduto;

/**
 *
 * @ORM\Entity(repositoryClass="App\Repository\Producao\InsumoRepository")
 * @ORM\Table(name="prod_insumo")
 */
class Insumo extends EntityId
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
     * @ORM\Column(name="descricao", type="string", nullable=false, length=200)
     * @Assert\NotBlank(message="O campo 'descricao' deve ser informado")
     */
    private $descricao;

    /**
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Producao\TipoInsumo")
     * @ORM\JoinColumn(name="tipo_insumo_id", nullable=false)
     *
     * @var $tipoInsumo TipoInsumo
     */
    private $tipoInsumo;

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

    public function getTipoInsumo(): ?TipoInsumo
    {
        return $this->tipoInsumo;
    }

    public function setTipoInsumo(?TipoInsumo $tipoInsumo)
    {
        $this->tipoInsumo = $tipoInsumo;
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