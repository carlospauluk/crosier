<?php
namespace App\Entity\Financeiro;

use App\Entity\Base\EntityId;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Entidade Regra de Importação de Linha.
 * Configura uma regra para setar corretamente a Movimentação ao importar uma linha de extrato.
 *
 * @author Carlos Eduardo Pauluk
 *        
 * @ORM\Entity(repositoryClass="App\Repository\Financeiro\RegraImportacaoLinhaRepository")
 * @ORM\Table(name="fin_regra_import_linha")
 * @ORM\HasLifecycleCallbacks()
 */
class RegraImportacaoLinha extends EntityId
{

    /**
     *
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * Em casos especiais (como na utilização de named groups) posso usar uma regex em java.
     *
     * @ORM\Column(name="regra_regex_java", type="string", nullable=false, length=500)
     * @Assert\NotBlank()
     */
    private $regraRegexJava;

    /**
     *
     * @ORM\Column(name="tipo_lancto", type="string", nullable=false, length=50)
     * @Assert\NotBlank()
     */
    private $tipoLancto;

    /**
     *
     * @ORM\Column(name="status", type="string", nullable=false, length=50)
     * @Assert\NotBlank()
     */
    private $status;

    /**
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Financeiro\Carteira")
     * @ORM\JoinColumn(name="carteira_id", nullable=true)
     */
    private $carteira;

    /**
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Financeiro\Carteira")
     * @ORM\JoinColumn(name="carteira_destino_id", nullable=true)
     */
    private $carteiraDestino;

    /**
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Financeiro\CentroCusto")
     * @ORM\JoinColumn(name="centrocusto_id", nullable=true)
     */
    private $centroCusto;

    /**
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Financeiro\Modo")
     * @ORM\JoinColumn(name="modo_id", nullable=true)
     */
    private $modo;

    /**
     *
     * @ORM\Column(name="padrao_descricao", type="string", nullable=false, length=500)
     * @Assert\NotBlank()
     */
    private $padraoDescricao;

    /**
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Financeiro\Categoria")
     * @ORM\JoinColumn(name="centrocusto_id", nullable=true)
     */
    private $categoria;

    /**
     * Para poder aplicar a regra somente se for positivo (1), negativo (-1) ou ambos (0)
     *
     * @ORM\Column(name="sinal_valor", type="integer", nullable=false)
     * @Assert\NotBlank()
     * @Assert\Range(min = -1, max = 1)
     */
    private $sinalValor;

    // ---------------------------------------------------------------------------------------
    // ---------- CAMPOS PARA "CHEQUE"
    
    /**
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Financeiro\Banco")
     * @ORM\JoinColumn(name="cheque_banco_id", nullable=true)
     */
    private $chequeBanco;

    /**
     * Código da agência (sem o dígito verificador).
     *
     * @ORM\Column(name="cheque_agencia", type="string", nullable=true, length=30)
     */
    private $chequeAgencia;

    /**
     * Número da conta no banco (não segue um padrão).
     *
     * @ORM\Column(name="cheque_conta", type="string", nullable=true, length=30)
     */
    private $chequeConta;

    /**
     * Número da conta no banco (não segue um padrão).
     *
     * @ORM\Column(name="cheque_num_cheque", type="string", nullable=true, length=30)
     */
    private $chequeNumCheque;
    
    // ---------------------------------------------------------------------------------------

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getRegraRegexJava()
    {
        return $this->regraRegexJava;
    }

    public function setRegraRegexJava($regraRegexJava)
    {
        $this->regraRegexJava = $regraRegexJava;
    }

    public function getTipoLancto()
    {
        return $this->tipoLancto;
    }

    public function setTipoLancto($tipoLancto)
    {
        $this->tipoLancto = $tipoLancto;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus($status)
    {
        $this->status = $status;
    }

    public function getCarteira(): ?Carteira
    {
        return $this->carteira;
    }

    public function setCarteira(?Carteira $carteira)
    {
        $this->carteira = $carteira;
    }

    public function getCarteiraDestino(): ?Carteira
    {
        return $this->carteiraDestino;
    }

    public function setCarteiraDestino(?Carteira $carteiraDestino)
    {
        $this->carteiraDestino = $carteiraDestino;
    }

    public function getCentroCusto(): ?CentroCusto
    {
        return $this->centroCusto;
    }

    public function setCentroCusto(?CentroCusto $centroCusto)
    {
        $this->centroCusto = $centroCusto;
    }

    public function getModo(): ?Modo
    {
        return $this->modo;
    }

    public function setModo(?Modo $modo)
    {
        $this->modo = $modo;
    }

    public function getPadraoDescricao()
    {
        return $this->padraoDescricao;
    }

    public function setPadraoDescricao($padraoDescricao)
    {
        $this->padraoDescricao = $padraoDescricao;
    }

    public function getCategoria(): ?Categoria
    {
        return $this->categoria;
    }

    public function setCategoria(?Categoria $categoria)
    {
        $this->categoria = $categoria;
    }

    public function getSinalValor()
    {
        return $this->sinalValor;
    }

    public function setSinalValor($sinalValor)
    {
        $this->sinalValor = $sinalValor;
    }

    public function getChequeBanco(): ?Banco
    {
        return $this->chequeBanco;
    }

    public function setChequeBanco(?Banco $chequeBanco)
    {
        $this->chequeBanco = $chequeBanco;
    }

    public function getChequeAgencia()
    {
        return $this->chequeAgencia;
    }

    public function setChequeAgencia($chequeAgencia)
    {
        $this->chequeAgencia = $chequeAgencia;
    }

    public function getChequeConta()
    {
        return $this->chequeConta;
    }

    public function setChequeConta($chequeConta)
    {
        $this->chequeConta = $chequeConta;
    }

    public function getChequeNumCheque()
    {
        return $this->chequeNumCheque;
    }

    public function setChequeNumCheque($chequeNumCheque)
    {
        $this->chequeNumCheque = $chequeNumCheque;
    }
    
    public function getSinalValorLabel() {
        switch ($this->sinalValor) {
            case 0: return "Ambos";
            case 1: return "Positivo";
            case -1: return "Negativo";
            default: return null;
        }
    }
    
    
}
