<?php

namespace App\Entity\Estoque;

use App\Entity\Base\EntityId;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 *
 * @ORM\Entity(repositoryClass="App\Repository\Estoque\ProdutoReduzidoektmesanoRepository")
 * @ORM\Table(name="est_produto_reduzidoektmesano")
 */
class ProdutoReduzidoEktMesano extends EntityId
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
     * @ORM\ManyToOne(targetEntity="App\Entity\Estoque\Produto")
     * @ORM\JoinColumn(name="produto_id", nullable=false)
     *
     * @var $produto Produto
     */
    private $produto;

    /**
     *
     * @ORM\Column(name="reduzido_ekt", type="integer", nullable=false)
     * @Assert\NotBlank(message="O campo 'reduzido_ekt' deve ser informado")
     * @Assert\Range(min = 0)
     */
    private $reduzidoEkt;

    /**
     *
     * @ORM\Column(name="mesano", type="string", nullable=false, length=6)
     * @Assert\NotBlank(message="O campo 'mesano' deve ser informado")
     */
    private $mesano;

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

    public function getProduto()
    {
        return $this->produto;
    }

    public function setProduto($produto)
    {
        $this->produto = $produto;
    }

    public function getReduzidoEkt()
    {
        return $this->reduzidoEkt;
    }

    public function setReduzidoEkt($reduzidoEkt)
    {
        $this->reduzidoEkt = $reduzidoEkt;
    }

    public function getMesano()
    {
        return $this->mesano;
    }

    public function setMesano($mesano)
    {
        $this->mesano = $mesano;
    }
}
    