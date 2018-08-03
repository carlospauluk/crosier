<?php
namespace App\Entity\RH;

use App\Entity\Base\EntityId;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 *
 * @ORM\Entity(repositoryClass="App\Repository\RH\CargoRepository")
 * @ORM\Table(name="rh_cargo")
 */
class Cargo extends EntityId
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
     * @ORM\Column(name="comissaoPorVendas", type="boolean", nullable=false)
     * @Assert\NotNull(message="O campo 'comissaoPorVendas' deve ser informado")
     */
    private $comissaoPorVendas;

    /**
     *
     * @ORM\Column(name="descricao", type="string", nullable=true, length=200)
     */
    private $descricao;

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

    public function getComissaoPorVendas()
    {
        return $this->comissaoPorVendas;
    }

    public function setComissaoPorVendas($comissaoPorVendas)
    {
        $this->comissaoPorVendas = $comissaoPorVendas;
    }

    public function getDescricao()
    {
        return $this->descricao;
    }

    public function setDescricao($descricao)
    {
        $this->descricao = $descricao;
    }
}