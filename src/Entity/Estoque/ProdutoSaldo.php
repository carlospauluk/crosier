<?php

namespace App\Entity\Estoque;

use App\Entity\Base\EntityId;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 *
 * @ORM\Entity(repositoryClass="App\Repository\Estoque\ProdutoSaldoRepository")
 * @ORM\Table(name="est_produto_saldo")
 */
class ProdutoSaldo extends EntityId
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
     * @ORM\Column(name="qtde", type="decimal", nullable=false, precision=15, scale=2)
     * @Assert\NotNull(message="O campo 'qtde' deve ser informado")
     * @Assert\Type("numeric", message="O campo 'qtde' deve ser numérico")
     */
    private $qtde;

    /**
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Estoque\GradeTamanho")
     * @ORM\JoinColumn(name="grade_tamanho_id", nullable=false)
     *
     * @var $gradeTamanho GradeTamanho
     */
    private $gradeTamanho;

    /**
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Estoque\Produto", inversedBy="saldos")
     * @ORM\JoinColumn(name="produto_id", nullable=false)
     *
     * @var $produto Produto
     */
    private $produto;

    /**
     *
     * @ORM\Column(name="selec", type="boolean", nullable=false)
     * @Assert\NotNull(message="O campo 'selec' deve ser informado")
     */
    private $selec;

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

    public function getQtde()
    {
        return $this->qtde;
    }

    public function setQtde($qtde)
    {
        $this->qtde = $qtde;
    }

    public function getGradeTamanho(): ?GradeTamanho
    {
        return $this->gradeTamanho;
    }

    public function setGradeTamanho(?GradeTamanho $gradeTamanho)
    {
        $this->gradeTamanho = $gradeTamanho;
    }

    public function getProduto(): ?Produto
    {
        return $this->produto;
    }

    public function setProduto(?Produto $produto)
    {
        $this->produto = $produto;
    }

    public function getSelec()
    {
        return $this->selec;
    }

    public function setSelec($selec)
    {
        $this->selec = $selec;
    }
}