<?php
namespace App\Entity\Producao;

use App\Entity\Base\EntityId;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use App\Entity\Estoque\Subdepto;

/**
 *
 * @ORM\Entity(repositoryClass="App\Repository\Producao\TipoArtigoRepository")
 * @ORM\Table(name="prod_tipo_artigo")
 */
class TipoArtigo extends EntityId
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
     * @ORM\Column(name="modo_calculo", type="string", nullable=false, length=15)
     * @Assert\NotBlank(message="O campo 'modo_calculo' deve ser informado")
     */
    private $modoCalculo;

    /**
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Estoque\Subdepto")
     * @ORM\JoinColumn(name="subdepto_id", nullable=false)
     *
     * @var $subdepto Subdepto
     */
    private $subdepto;

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

    public function getModoCalculo()
    {
        return $this->modoCalculo;
    }

    public function setModoCalculo($modoCalculo)
    {
        $this->modoCalculo = $modoCalculo;
    }

    public function getSubdepto(): ?Subdepto
    {
        return $this->subdepto;
    }

    public function setSubdepto(?Subdepto $subdepto)
    {
        $this->subdepto = $subdepto;
    }
}