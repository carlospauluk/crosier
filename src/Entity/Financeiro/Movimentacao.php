<?php

namespace App\Entity\Financeiro;

use App\Entity\Base\EntityId;
use App\Entity\Base\Pessoa;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Entidade 'Movimentação'.
 *
 * @ORM\Entity(repositoryClass="App\Repository\Financeiro\MovimentacaoRepository")
 * @ORM\Table(name="fin_movimentacao")
 */
class Movimentacao extends EntityId
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
     * @ORM\Column(name="id_sistema_antigo", type="bigint", nullable=true)
     */
    private $idSistemaAntigo;

    /**
     *
     * @ORM\Column(name="status", type="string", nullable=false, length=50)
     */
    private $status;

    /**
     * Tipo de lançamento que originou esta movimentação.
     *
     * @ORM\Column(name="tipo_lancto", type="string", nullable=false, length=50)
     *
     * @var $tipoLancto
     */
    private $tipoLancto;

    /**
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Financeiro\Carteira")
     * @ORM\JoinColumn(name="carteira_id", nullable=false)
     *
     * @var $carteira Carteira
     */
    private $carteira;

    /**
     * Carteira informada em casos de TRANSF_PROPRIA.
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Financeiro\Carteira")
     * @ORM\JoinColumn(name="carteira_destino_id", nullable=true)
     *
     * @var $carteiraDestino Carteira
     */
    private $carteiraDestino;

    /**
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Financeiro\CentroCusto")
     * @ORM\JoinColumn(name="centrocusto_id", nullable=false)
     *
     * @var $centroCusto CentroCusto
     */
    private $centroCusto;

    /**
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Financeiro\Modo")
     * @ORM\JoinColumn(name="modo_id", nullable=false)
     *
     * @var $modo Modo
     */
    private $modo;

    /**
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Financeiro\BandeiraCartao")
     * @ORM\JoinColumn(name="bandeira_cartao_id", nullable=true)
     *
     * @var $bandeiraCartao BandeiraCartao
     */
    private $bandeiraCartao;

    /**
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Financeiro\OperadoraCartao")
     * @ORM\JoinColumn(name="operadora_cartao_id", nullable=true)
     *
     * @var $operadoraCartao OperadoraCartao
     */
    private $operadoraCartao;

    /**
     *
     * @ORM\Column(name="plano_pagto_cartao", type="string", nullable=true, length=50)
     */
    private $planoPagtoCartao;

    /**
     *
     * @ORM\Column(name="descricao", type="string", nullable=false, length=500)
     */
    private $descricao;

    /**
     * Campo para manter como único uma movimentação que tenha todas as características de outra movimentação do mesmo
     * dia/carteira/valor/categoria etc.
     *
     * @ORM\Column(name="unq_controle", type="string", nullable=true, length=15)
     */
    private $unqControle;

    /**
     *
     * @ORM\Column(name="obs", type="string", nullable=true, length=5000)
     */
    private $obs;

    /**
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Financeiro\Parcelamento", inversedBy="parcelas")
     * @ORM\JoinColumn(name="parcelamento_id", nullable=true)
     *
     * @var $parcelamento Parcelamento
     */
    private $parcelamento;

    /**
     * Caso a movimentação seja uma parcela, informa qual.
     *
     * @ORM\Column(name="num_parcela", type="integer", nullable=true)
     */
    private $numParcela;

    /**
     * Inclui aqui para não precisar dar um SELECT na tabela de parcelamentos toda hora que quiser a qtde de parcelas.
     *
     * @ORM\Column(name="qtde_parcelas", type="integer", nullable=true)
     */
    private $qtdeParcelas;

    /**
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Financeiro\Cadeia")
     * @ORM\JoinColumn(name="cadeia_id", referencedColumnName="id", nullable=true)
     *
     * @var $cadeia Cadeia
     */
    private $cadeia;

    /**
     * Caso a movimentação faça parte de uma cadeia, informa em qual posição.
     *
     * @ORM\Column(name="cadeia_ordem", type="integer", nullable=true)
     */
    private $cadeiaOrdem;

    /**
     * Para casos onde existe um documento numerado representando a movimentação
     * (boleto, código transferência bancária, nota fiscal, etc).
     *
     * @ORM\Column(name="documento_num", type="string", nullable=true, length=100)
     */
    private $documentoNum;

    /**
     * Código do Banco (conforme códigos oficiais).
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Financeiro\Banco")
     * @ORM\JoinColumn(name="documento_banco_id", nullable=true)
     *
     * @var $documentoBanco Banco
     */
    private $documentoBanco;

    /**
     * Informa o número do documento (nota, etc) fiscal.
     *
     * FIXME: mas o documentoNum já não é isso?
     *
     * @ORM\Column(name="documento_fiscal", type="string", nullable=true, length=255)
     */
    private $documentoFiscal;

    /**
     * Informa o número do código do pedido.
     *
     * @ORM\Column(name="codigo_pedido", type="string", nullable=true, length=255)
     */
    private $codigoPedido;

    /**
     * Caso seja uma movimentação agrupada em um Grupo de Movimentação (item).
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Financeiro\GrupoItem", inversedBy="movimentacoes")
     * @ORM\JoinColumn(name="grupo_item_id", nullable=true)
     *
     * @var $grupoItem GrupoItem
     */
    private $grupoItem;

    /**
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Financeiro\Categoria")
     * @ORM\JoinColumn(name="categoria_id", nullable=false)
     * @var $categoria Categoria
     */
    private $categoria;

    /**
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Base\Pessoa")
     * @ORM\JoinColumn(name="pessoa_id", nullable=true)
     *
     * @var $pessoa Pessoa
     */
    private $pessoa;

    /**
     * Data em que a movimentação efetivamente aconteceu.
     *
     * @ORM\Column(name="dt_moviment", type="datetime", nullable=false)
     * @Assert\Type("\DateTime")
     */
    private $dtMoviment;

    /**
     * Data prevista para pagamento.
     *
     * @ORM\Column(name="dt_vencto", type="datetime", nullable=false)
     * @Assert\Type("\DateTime")
     */
    private $dtVencto;

    /**
     * Data prevista (postergando para dia útil) para pagamento.
     *
     * @ORM\Column(name="dt_vencto_efetiva", type="datetime", nullable=false)
     * @Assert\Type("\DateTime")
     */
    private $dtVenctoEfetiva;

    /**
     * Data em que a movimentação foi paga.
     *
     * @ORM\Column(name="dt_pagto", type="datetime", nullable=true)
     * @Assert\Type("\DateTime")
     */
    private $dtPagto;

    /**
     * Se dtPagto != null ? dtPagto : dtVencto.
     *
     * @ORM\Column(name="dt_util", type="datetime", nullable=false)
     * @Assert\Type("\DateTime")
     */
    private $dtUtil;

    /**
     * Valor bruto da movimentação.
     *
     * @ORM\Column(name="valor", type="decimal", nullable=false, precision=15, scale=2)
     * @Assert\Type("numeric")
     */
    private $valor;

    /**
     * Possíveis descontos (sempre negativo).
     *
     * @ORM\Column(name="descontos", type="decimal", nullable=true, precision=15, scale=2)
     * @Assert\Type("numeric")
     */
    private $descontos;

    /**
     * Possíveis acréscimos (sempre positivo).
     *
     * @ORM\Column(name="acrescimos", type="decimal", nullable=true, precision=15, scale=2)
     * @Assert\Type("numeric")
     */
    private $acrescimos;

    /**
     * Valor total informado no campo e que é salvo no banco (pode divergir da
     * conta por algum motivo).
     *
     * @ORM\Column(name="valor_total", type="decimal", nullable=false, precision=15, scale=2)
     * @Assert\Type("numeric", message="O campo 'Valor Total' deve ser numérico")
     */
    private $valorTotal;

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
    // ---------- CAMPOS PARA "RECORRÊNCIA"

    /**
     *
     * @ORM\Column(name="recorrente", type="boolean", nullable=false)
     */
    private $recorrente = false;

    /**
     *
     * @ORM\Column(name="recorr_frequencia", type="string", nullable=true, length=50)
     */
    private $recorrFrequencia;

    /**
     *
     * @ORM\Column(name="recorr_tipo_repet", type="string", nullable=true, length=50)
     */
    private $recorrTipoRepet;

    /**
     * Utilizar 32 para marcar o último dia do mês.
     *
     * FIXME: meio burro isso (podia usar o 31 mesmo).
     *
     * @ORM\Column(name="recorr_dia", type="integer", nullable=true)
     */
    private $recorrDia;

    /**
     * Utilizado para marcar a variação em relação ao dia em que seria o vencimento.
     * Exemplo: dia=32 (último dia do mês) + variacao=-2 >>> 2 dias antes do último dia do mês
     *
     * FIXME: meio burro isso (podia usar o 31 mesmo).
     *
     * @ORM\Column(name="recorr_variacao", type="integer", nullable=true)
     */
    private $recorrVariacao;

    // ---------------------------------------------------------------------------------------
    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getIdSistemaAntigo()
    {
        return $this->idSistemaAntigo;
    }

    public function setIdSistemaAntigo($idSistemaAntigo)
    {
        $this->idSistemaAntigo = $idSistemaAntigo;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus($status)
    {
        $this->status = $status;
    }

    public function getTipoLancto()
    {
        // FIXME: depois que ajustar tudo, remover esta gambi
        switch ($this->tipoLancto) {
            case 'REALIZADA':
            case 'A_PAGAR_RECEBER':
            case 'CHEQUE_PROPRIO':
            case 'CHEQUE_TERCEIROS':
            case 'GERAL':
            case 'ESTORNO_CORRECAO':
                $this->tipoLancto = 'GERAL';
                break;
            case 'TRANSF_CAIXA':
                $this->tipoLancto = 'TRANSF_PROPRIA';
                break;
            case 'MOVIMENTACAO_AGRUPADA':
                $this->tipoLancto = 'GRUPO';
                break;
        }

        return $this->tipoLancto;
    }

    public function setTipoLancto($tipoLancto)
    {
        $this->tipoLancto = $tipoLancto;
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

    public function getBandeiraCartao(): ?BandeiraCartao
    {
        return $this->bandeiraCartao;
    }

    public function setBandeiraCartao(?BandeiraCartao $bandeiraCartao)
    {
        $this->bandeiraCartao = $bandeiraCartao;
    }

    public function getOperadoraCartao(): ?OperadoraCartao
    {
        return $this->operadoraCartao;
    }

    public function setOperadoraCartao(?OperadoraCartao $operadoraCartao)
    {
        $this->operadoraCartao = $operadoraCartao;
    }

    public function getPlanoPagtoCartao()
    {
        return $this->planoPagtoCartao;
    }

    // FIXME: depois criar uma subtabela
    public function setPlanoPagtoCartao($planoPagtoCartao)
    {
        $this->planoPagtoCartao = $planoPagtoCartao;
    }

    public function getDescricao()
    {
        return $this->descricao;
    }

    public function setDescricao($descricao)
    {
        $this->descricao = $descricao;
    }

    public function getUnqControle()
    {
        return $this->unqControle;
    }

    public function setUnqControle($unqControle)
    {
        $this->unqControle = $unqControle;
    }

    public function getObs()
    {
        return $this->obs;
    }

    public function setObs($obs)
    {
        $this->obs = $obs;
    }

    public function getParcelamento(): ?Parcelamento
    {
        return $this->parcelamento;
    }

    public function setParcelamento(?Parcelamento $parcelamento)
    {
        $this->parcelamento = $parcelamento;
    }

    public function getNumParcela()
    {
        return $this->numParcela;
    }

    public function setNumParcela($numParcela)
    {
        $this->numParcela = $numParcela;
    }

    public function getQtdeParcelas()
    {
        return $this->qtdeParcelas;
    }

    public function setQtdeParcelas($qtdeParcelas)
    {
        $this->qtdeParcelas = $qtdeParcelas;
    }

    public function getCadeia(): ?Cadeia
    {
        return $this->cadeia;
    }

    public function setCadeia(?Cadeia $cadeia)
    {
        $this->cadeia = $cadeia;
    }

    public function getCadeiaOrdem()
    {
        return $this->cadeiaOrdem;
    }

    public function setCadeiaOrdem($cadeiaOrdem)
    {
        $this->cadeiaOrdem = $cadeiaOrdem;
    }

    public function getDocumentoNum()
    {
        return $this->documentoNum;
    }

    public function setDocumentoNum($documentoNum)
    {
        $this->documentoNum = $documentoNum;
    }

    public function getDocumentoBanco(): ?Banco
    {
        return $this->documentoBanco;
    }

    public function setDocumentoBanco(?Banco $documentoBanco)
    {
        $this->documentoBanco = $documentoBanco;
    }

    public function getDocumentoFiscal()
    {
        return $this->documentoFiscal;
    }

    public function setDocumentoFiscal($documentoFiscal)
    {
        $this->documentoFiscal = $documentoFiscal;
    }

    public function getCodigoPedido()
    {
        return $this->codigoPedido;
    }

    public function setCodigoPedido($codigoPedido)
    {
        $this->codigoPedido = $codigoPedido;
    }

    public function getGrupoItem(): ?GrupoItem
    {
        return $this->grupoItem;
    }

    public function setGrupoItem(?GrupoItem $grupoItem)
    {
        $this->grupoItem = $grupoItem;
    }

    public function getCategoria(): ?Categoria
    {
        return $this->categoria;
    }

    public function setCategoria(?Categoria $categoria)
    {
        $this->categoria = $categoria;
    }

    public function getPessoa(): ?Pessoa
    {
        return $this->pessoa;
    }

    public function setPessoa(?Pessoa $pessoa)
    {
        $this->pessoa = $pessoa;
    }

    public function getDtMoviment()
    {
        return $this->dtMoviment;
    }

    public function setDtMoviment($dtMoviment)
    {
        $this->dtMoviment = $dtMoviment;
    }

    public function getDtVencto()
    {
        return $this->dtVencto;
    }

    public function setDtVencto($dtVencto)
    {
        $this->dtVencto = $dtVencto;
    }

    public function getDtVenctoEfetiva()
    {
        return $this->dtVenctoEfetiva;
    }

    public function setDtVenctoEfetiva($dtVenctoEfetiva)
    {
        $this->dtVenctoEfetiva = $dtVenctoEfetiva;
    }

    public function getDtPagto()
    {
        return $this->dtPagto;
    }

    public function setDtPagto($dtPagto)
    {
        $this->dtPagto = $dtPagto;
    }

    public function getDtUtil()
    {
        return $this->dtUtil;
    }

    public function setDtUtil($dtUtil)
    {
        $this->dtUtil = $dtUtil;
    }

    public function getValor()
    {
        return $this->valor;
    }

    public function setValor($valor)
    {
        $this->valor = $valor;
    }

    public function getDescontos()
    {
        return $this->descontos;
    }

    public function setDescontos($descontos)
    {
        $this->descontos = -abs(floatval($descontos));
    }

    public function getAcrescimos()
    {
        return $this->acrescimos;
    }

    public function setAcrescimos($acrescimos)
    {
        $this->acrescimos = abs(floatval($acrescimos));
    }

    public function getValorTotal()
    {
        return $this->valorTotal;
    }

    public function setValorTotal($valorTotal)
    {
        $this->valorTotal = $valorTotal;
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

    public function getRecorrente()
    {
        return $this->recorrente;
    }

    public function setRecorrente($recorrente)
    {
        $this->recorrente = $recorrente;
    }

    public function getRecorrFrequencia()
    {
        return $this->recorrFrequencia;
    }

    public function setRecorrFrequencia($recorrFrequencia)
    {
        $this->recorrFrequencia = $recorrFrequencia;
    }

    public function getRecorrTipoRepet()
    {
        return $this->recorrTipoRepet;
    }

    public function setRecorrTipoRepet($recorrTipoRepet)
    {
        $this->recorrTipoRepet = $recorrTipoRepet;
    }

    public function getRecorrDia()
    {
        return $this->recorrDia;
    }

    public function setRecorrDia($recorrDia)
    {
        $this->recorrDia = $recorrDia;
    }

    public function getRecorrVariacao()
    {
        return $this->recorrVariacao;
    }

    public function setRecorrVariacao($recorrVariacao)
    {
        $this->recorrVariacao = $recorrVariacao;
    }

    public function getDescricaoMontada()
    {
        $sufixo = "";

        if ($this->getQtdeParcelas() > 0) {
            $zerosfill = strlen(parse_str($this->getQtdeParcelas()));
            $zerosfill = $zerosfill < 2 ? 2 : $zerosfill;
            $sufixo .= " (" . str_pad($this->getNumParcela(), $zerosfill, "0", STR_PAD_LEFT) . "/" . str_pad($this->getQtdeParcelas(), $zerosfill, "0", STR_PAD_LEFT) . ")";
        }

        if ($this->getDocumentoFiscal()) {
            $sufixo .= "\n(NF: " . $this->getDocumentoFiscal() . ")";
        }

        if ($this->getChequeNumCheque()) {
            $sufixo .= "\n(CHQ: " . $this->getChequeBanco()->getNome() . " - nº " . $this->getChequeNumCheque() . ")";
        }

        if ($this->getBandeiraCartao()) {
            $sufixo .= "\n(Bandeira: " . $this->getBandeiraCartao()->getDescricao() . ")";
        }

        if ($this->getOperadoraCartao()) {
            $sufixo .= "\n(Operadora: " . $this->getOperadoraCartao()->getDescricao() . ")";
        }

        if ($this->getGrupoItem()) {
            $sufixo .= "\n" . $this->getGrupoItem()->getDescricao() . ")";
        }

        $descricaoMontada = $this->getDescricao() . $sufixo;

        return $descricaoMontada;
    }

    public function calcValorTotal()
    {
        $valorTotal = $this->getValor() + $this->getDescontos() + $this->getAcrescimos();
        $this->setValorTotal($valorTotal);
    }

    // ---------------------------------------------------------------------------------------

    /**
     * Utilitário para pegar as informações mais rapidamente via JSF.
     *
     * @return
     */
    public function infoDatas()
    {
        // StringBuilder str = new StringBuilder("");
        // if (getDtMoviment() != null) {
        // str.append("Dt Moviment: " + CalendarUtil.sdfDate.format(getDtMoviment()));
        // }
        // if (getDtVencto() != null) {
        // if (str.toString() != "")
        // str.append("\n");
        // str.append("Dt Vencto: " + CalendarUtil.sdfDate.format(getDtVencto()));
        // }
        // if (getDtVenctoEfetiva() != null) {
        // if (str.toString() != "")
        // str.append("\n");
        // str.append("Dt Vencto Efet: " + CalendarUtil.sdfDate.format(getDtVenctoEfetiva()));
        // }
        // if (getDtPagto() != null) {
        // if (str.toString() != "")
        // str.append("\n");
        // str.append("Dt Pagto: " + CalendarUtil.sdfDate.format(getDtPagto()));
        // }
        // return str.toString();
    }

    /**
     * Utilitário para pegar as informações mais rapidamente via JSF.
     *
     * @return
     */
    public function infoValores()
    {
        // StringBuilder str = new StringBuilder("");
        // if (getValor() != null) {
        // str.append("Valor: " + NumberFormat.getCurrencyInstance().format(getValor()));
        // }
        // if (getAcrescimos() != null) {
        // if (str.toString() != "")
        // str.append("\n");
        // str.append("Acréscimos: " + NumberFormat.getCurrencyInstance().format(getAcrescimos()));
        // }
        // if (getDescontos() != null) {
        // if (str.toString() != "")
        // str.append("\n");
        // str.append("Descontos: " + NumberFormat.getCurrencyInstance().format(getDescontos()));
        // }
        // if (getValorTotal() != null) {
        // if (str.toString() != "")
        // str.append("\n");
        // str.append("Total: " + NumberFormat.getCurrencyInstance().format(getValorTotal()));
        // }
        // return str.toString();
    }

    public function getStatusIcone()
    {
        return Status::get($this->getStatus())['icone'];
    }
}

