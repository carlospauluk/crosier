<?php

namespace App\Entity\Vendas;

use App\Entity\Base\EntityId;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 *
 * @ORM\Entity(repositoryClass="App\Repository\Vendas\TipoVendaRepository")
 * @ORM\Table(name="ven_tipo_venda")
 */
class TipoVenda extends EntityId
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
     * @ORM\Column(name="descricao", type="string", nullable=false, length=100)
     * @Assert\NotBlank(message="O campo 'descricao' deve ser informado")
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

    public function getDescricao()
    {
        return $this->descricao;
    }

    public function setDescricao($descricao)
    {
        $this->descricao = $descricao;
    }
}
    