<?php

namespace App\Entity\Vendas;

use App\Entity\Base\EntityId;
use App\Entity\Estoque\GradeTamanho;
use App\Entity\Estoque\Produto;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 *
 * @ORM\Entity(repositoryClass="App\Repository\Vendas\VendaItemRepository")
 * @ORM\Table(name="ven_venda_item")
 */
class VendaItem extends EntityId
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
     * @ORM\Column(name="alteracao_preco", type="boolean", nullable=false)
     * @Assert\NotNull(message="O campo 'alteracao_preco' deve ser informado")
     */
    private $alteracaoPreco;

    /**
     *
     * @ORM\Column(name="obs", type="string", nullable=true, length=5000)
     */
    private $obs;

    /**
     *
     * @ORM\Column(name="preco_venda", type="decimal", nullable=false, precision=15, scale=2)
     * @Assert\NotNull(message="O campo 'preco_venda' deve ser informado")
     * @Assert\Type("numeric", message="O campo 'preco_venda' deve ser numérico")
     */
    private $precoVenda;

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
     * @ORM\ManyToOne(targetEntity="App\Entity\vendas\Venda")
     * @ORM\JoinColumn(name="venda_id", nullable=false)
     *
     * @var $venda Venda
     */
    private $venda;

    /**
     *
     * @ORM\Column(name="nc_descricao", type="string", nullable=true, length=200)
     */
    private $ncDescricao;

    /**
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Estoque\Produto")
     * @ORM\JoinColumn(name="produto_id", nullable=true)
     * @Assert\NotNull(message="O campo 'Produto' deve ser informado")
     *
     * @var $produto Produto
     */
    private $produto;

    /**
     *
     * @ORM\Column(name="nc_reduzido", type="bigint", nullable=true)
     * @Assert\Range(min = 0)
     */
    private $ncReduzido;

    /**
     *
     * @ORM\Column(name="nc_grade_tamanho", type="string", nullable=true, length=200)
     */
    private $ncGradeTamanho;

    /**
     *
     * @ORM\Column(name="ncm", type="string", nullable=true, length=20)
     */
    private $ncm;

    /**
     *
     * @ORM\Column(name="ordem", type="integer", nullable=true)
     * @Assert\Range(min = 0)
     */
    private $ordem;

    /**
     *
     * @ORM\Column(name="ncm_existente", type="boolean", nullable=true)
     */
    private $ncmExistente;

    /**
     *
     * @ORM\Column(name="dt_custo", type="datetime", nullable=true)
     * @Assert\NotNull(message="O campo 'dt_custo' deve ser informado")
     * @Assert\Type("\DateTime", message="O campo 'dt_custo' deve ser do tipo data/hora")
     */
    private $dtCusto;

    /**
     *
     * @ORM\Column(name="preco_custo", type="decimal", nullable=true, precision=15, scale=2)
     * @Assert\Type("numeric", message="O campo 'preco_custo' deve ser numérico")
     */
    private $precoCusto;

    // TODO: transformar isso num campo
    private $totalItem;

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

    public function getAlteracaoPreco()
    {
        return $this->alteracaoPreco;
    }

    public function setAlteracaoPreco($alteracaoPreco)
    {
        $this->alteracaoPreco = $alteracaoPreco;
    }

    public function getObs()
    {
        return $this->obs;
    }

    public function setObs($obs)
    {
        $this->obs = $obs;
    }

    public function getPrecoVenda()
    {
        return $this->precoVenda;
    }

    public function setPrecoVenda($precoVenda)
    {
        $this->precoVenda = $precoVenda;
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

    public function getVenda(): ?Venda
    {
        return $this->venda;
    }

    public function setVenda(?Venda $venda)
    {
        $this->venda = $venda;
    }

    public function getNcDescricao()
    {
        return $this->ncDescricao;
    }

    public function setNcDescricao($ncDescricao)
    {
        $this->ncDescricao = $ncDescricao;
    }

    public function getProduto(): ?Produto
    {
        return $this->produto;
    }

    public function setProduto(?Produto $produto)
    {
        $this->produto = $produto;
    }

    public function getNcReduzido()
    {
        return $this->ncReduzido;
    }

    public function setNcReduzido($ncReduzido)
    {
        $this->ncReduzido = $ncReduzido;
    }

    public function getNcGradeTamanho()
    {
        return $this->ncGradeTamanho;
    }

    public function setNcGradeTamanho($ncGradeTamanho)
    {
        $this->ncGradeTamanho = $ncGradeTamanho;
    }

    public function getNcm()
    {
        return $this->ncm;
    }

    public function setNcm($ncm)
    {
        $this->ncm = $ncm;
    }

    public function getOrdem()
    {
        return $this->ordem;
    }

    public function setOrdem($ordem)
    {
        $this->ordem = $ordem;
    }

    public function getNcmExistente()
    {
        return $this->ncmExistente;
    }

    public function setNcmExistente($ncmExistente)
    {
        $this->ncmExistente = $ncmExistente;
    }

    public function getDtCusto()
    {
        return $this->dtCusto;
    }

    public function setDtCusto($dtCusto)
    {
        $this->dtCusto = $dtCusto;
    }

    public function getPrecoCusto()
    {
        return $this->precoCusto;
    }

    public function setPrecoCusto($precoCusto)
    {
        $this->precoCusto = $precoCusto;
    }

    public function getTotalItem()
    {
        $this->totalItem = $this->getQtde() * $this->getPrecoVenda();
        return $this->totalItem;
    }

    public function setTotalItem($totalItem)
    {
        $this->totalItem = $totalItem;
    }
}