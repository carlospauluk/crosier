<?php

namespace App\Entity\Financeiro;

use App\Entity\Base\EntityId;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Entidade 'Carteira'.
 *
 * @ORM\Entity(repositoryClass="App\Repository\Financeiro\CarteiraRepository")
 * @ORM\Table(name="fin_carteira")
 */
class Carteira extends EntityId
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
     * @Assert\NotBlank()
     */
    private $codigo;

    /**
     *
     * @ORM\Column(name="descricao", type="string", nullable=false, length=40)
     * @Assert\NotBlank()
     */
    private $descricao;

    /**
     * Movimentações desta carteira não poderão ter suas datas alteradas para antes desta.
     *
     * @ORM\Column(name="dt_consolidado", type="datetime", nullable=false)
     * @Assert\NotNull()
     * @Assert\Type("\DateTime")
     */
    private $dtConsolidado;

    /**
     * Uma Carteira concreta é aquela em que podem ser efetuados créditos e
     * débitos, como uma conta corrente ou um caixa.
     * Um Grupo de Movimentação só pode estar vinculado à uma Carteira concreta.
     * Uma movimentação que contenha um grupo de movimentação, precisa ter sua
     * carteira igual a carteira do grupo de movimentação.
     *
     *
     * @ORM\Column(name="concreta", type="boolean", nullable=false)
     * @Assert\NotNull()
     */
    private $concreta = false;

    /**
     * Informa se esta carteira pode conter movimentações com status ABERTA.
     *
     * @ORM\Column(name="abertas", type="boolean", nullable=false)
     * @Assert\NotNull()
     */
    private $abertas = false;

    /**
     * Informa se esta carteira é um caixa (ex.: caixa a vista, caixa a prazo).
     *
     * @ORM\Column(name="caixa", type="boolean", nullable=false)
     * @Assert\NotNull()
     */
    private $caixa = false;

    /**
     * Informa se esta carteira possui talão de cheques.
     *
     * @ORM\Column(name="cheque", type="boolean", nullable=false)
     * @Assert\NotNull()
     */
    private $cheque = false;

    /**
     * No caso da Carteira ser uma conta de banco, informa qual.
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Financeiro\Banco")
     * @ORM\JoinColumn(nullable=true)
     */
    private $banco;

    /**
     * Código da agência (sem o dígito verificador).
     *
     * @ORM\Column(name="agencia", type="string", nullable=true, length=30)
     */
    private $agencia;

    /**
     * Número da conta no banco (não segue um padrão).
     *
     * @ORM\Column(name="conta", type="string", nullable=true, length=30)
     */
    private $conta;

    /**
     * Utilizado para informar o limite disponível.
     *
     * @ORM\Column(name="limite", type="decimal", nullable=true, precision=15, scale=2)
     */
    private $limite;

    /**
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Financeiro\OperadoraCartao")
     * @ORM\JoinColumn(name="operadora_cartao_id", nullable=false)
     *
     * @var $operadoraCartao OperadoraCartao
     */
    private $operadoraCartao;




    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getCodigo($format = false)
    {
        if ($format) {
            return str_pad($this->codigo, 3, "0", STR_PAD_LEFT);
        } else {
            return $this->codigo;
        }
    }

    public function setCodigo($codigo)
    {
        $this->codigo = $codigo;
    }

    public function getDescricao()
    {
        return $this->descricao;
    }

    public function getDescricaoMontada()
    {
        return $this->getCodigo(true) . ' - ' . $this->getDescricao();
    }

    public function setDescricao($descricao)
    {
        $this->descricao = $descricao;
    }

    public function getDtConsolidado()
    {
        return $this->dtConsolidado;
    }

    public function setDtConsolidado($dtConsolidado)
    {
        $this->dtConsolidado = $dtConsolidado;
    }

    public function getConcreta()
    {
        return $this->concreta;
    }

    public function setConcreta($concreta)
    {
        $this->concreta = $concreta;
    }

    public function getAbertas()
    {
        return $this->abertas;
    }

    public function setAbertas($abertas)
    {
        $this->abertas = $abertas;
    }

    public function getCaixa()
    {
        return $this->caixa;
    }

    public function setCaixa($caixa)
    {
        $this->caixa = $caixa;
    }

    public function getCheque()
    {
        return $this->cheque;
    }

    public function setCheque($cheque)
    {
        $this->cheque = $cheque;
    }

    public function getBanco(): ?Banco
    {
        return $this->banco;
    }

    public function setBanco(?Banco $banco): self
    {
        $this->banco = $banco;
        return $this;
    }

    public function getAgencia()
    {
        return $this->agencia;
    }

    public function setAgencia($agencia)
    {
        $this->agencia = $agencia;
    }

    public function getConta()
    {
        return $this->conta;
    }

    public function setConta($conta)
    {
        $this->conta = $conta;
    }

    public function getLimite()
    {
        return $this->limite;
    }

    public function setLimite($limite)
    {
        $this->limite = $limite;
    }

    /**
     * @return OperadoraCartao
     */
    public function getOperadoraCartao(): ?OperadoraCartao
    {
        return $this->operadoraCartao;
    }

    /**
     * @param OperadoraCartao $operadoraCartao
     */
    public function setOperadoraCartao(?OperadoraCartao $operadoraCartao): void
    {
        $this->operadoraCartao = $operadoraCartao;
    }


}
