<?php

namespace App\Entity\financeiro;

use App\Entity\base\EntityId;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 *
 * @ORM\Entity(repositoryClass="App\Repository\financeiro\CarteiraRepository")
 * @ORM\Table(name="fin_carteira")
 */
class Carteira extends EntityId {

	/**
	 *
	 * @ORM\Id()
	 * @ORM\GeneratedValue()
	 * @ORM\Column(type="integer")
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
	 * débitos, como uma conta corrente.
	 * Um Grupo de Movimentação só pode ser
	 * filho de uma Carteira concreta. Uma movimentação que contenha um grupo de
	 * movimentação, precisa ter sua carteira igual a carteira do grupo de
	 * movimentação.
	 *
	 *
	 * @ORM\Column(name="concreta", type="boolean", nullable=false)
	 * @Assert\NotNull()
	 */
	private $concreta = false;

	/**
	 * Informa se esta carteira pode conter movimentações com status ABERTA.
	 * útil principalmente para o relatório de contas a pagar/receber, para não considerar movimentações de outras carteiras.
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
	 * Código do Banco (conforme códigos oficiais).
	 *
	 * @Assert\NotNull()
	 * @ORM\Column(type="boolean", nullable=false)
	 */
	// @ManyToOne(optional = true)
	// @JoinColumn(name = "banco_id", nullable = true)
	// private $banco;
	
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

	public function getId() {
		return $this->id;
	}

	public function setId($id) {
		$this->id = $id;
	}

	public function getCodigo() {
		return $this->codigo;
	}

	public function setCodigo($codigo) {
		$this->codigo = $codigo;
	}

	public function getDescricao() {
		return $this->descricao;
	}

	public function setDescricao($descricao) {
		$this->descricao = $descricao;
	}

	public function getDtConsolidado() {
		return $this->dtConsolidado;
	}

	public function setDtConsolidado($dtConsolidado) {
		$this->dtConsolidado = $dtConsolidado;
	}

	public function getConcreta() {
		return $this->concreta;
	}

	public function setConcreta($concreta) {
		$this->concreta = $concreta;
	}

	public function getAbertas() {
		return $this->abertas;
	}

	public function setAbertas($abertas) {
		$this->abertas = $abertas;
	}

	public function getCaixa() {
		return $this->caixa;
	}

	public function setCaixa($caixa) {
		$this->caixa = $caixa;
	}

	public function getCheque() {
		return $this->cheque;
	}

	public function setCheque($cheque) {
		$this->cheque = $cheque;
	}

	public function getAgencia() {
		return $this->agencia;
	}

	public function setAgencia($agencia) {
		$this->agencia = $agencia;
	}

	public function getConta() {
		return $this->conta;
	}

	public function setConta($conta) {
		$this->conta = $conta;
	}

	public function getLimite() {
		return $this->limite;
	}

	public function setLimite($limite) {
		$this->limite = $limite;
	}
}
