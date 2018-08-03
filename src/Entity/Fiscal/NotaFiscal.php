<?php
namespace App\Entity\Fiscal;

use App\Entity\Base\EntityId;
use App\Entity\Base\Pessoa;
use App\Entity\Estoque\Fornecedor;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 *
 * @ORM\Entity(repositoryClass="App\Repository\Fiscal\NotaFiscalRepository")
 * @ORM\Table(name="fis_nf")
 */
class NotaFiscal extends EntityId
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
     * @ORM\Column(name="dt_emissao", type="datetime", nullable=true)
     * @Assert\NotNull(message="O campo 'dt_emissao' deve ser informado")
     * @Assert\Type("\DateTime", message="O campo 'dt_emissao' deve ser do tipo data/hora")
     */
    private $dtEmissao;

    /**
     *
     * @ORM\Column(name="numero", type="integer", nullable=false)
     * @Assert\NotBlank(message="O campo 'numero' deve ser informado")
     * @Assert\Range(min = 0)
     */
    private $numero;

    /**
     *
     * @ORM\Column(name="valor_total", type="decimal", nullable=false, precision=15, scale=2)
     * @Assert\NotNull(message="O campo 'valor_total' deve ser informado")
     * @Assert\Type("numeric", message="O campo 'valor_total' deve ser numérico")
     */
    private $valorTotal;

    /**
	 * Se for de saída, é a própria empresa, se for de entrada é o fornecedor.
	 * 
     * @ORM\ManyToOne(targetEntity="App\Entity\Base\Pessoa")
     * @ORM\JoinColumn(name="pessoa_emitente_id", nullable=false)
     *
     * @var $pessoaEmitente Pessoa
     */
    private $pessoaEmitente;

    /**
     *
     * @ORM\Column(name="tipo", type="string", nullable=true, length=30)
     */
    private $tipoNotaFiscal;

    /**
     *
     * @ORM\Column(name="entrada_saida", type="boolean", nullable=false)
     * @Assert\NotNull(message="O campo 'entrada_saida' deve ser informado")
     */
    private $entrada;

    /**
     *
     * @ORM\Column(name="serie", type="integer", nullable=false)
     * @Assert\NotBlank(message="O campo 'serie' deve ser informado")
     * @Assert\Range(min = 0)
     */
    private $serie;

    /**
	 * Aqui pode ser null, pois pode ser uma NFCe anônima.
	 * 
     * @ORM\ManyToOne(targetEntity="App\Entity\Base\Pessoa")
     * @ORM\JoinColumn(name="pessoa_destinatario_id", nullable=true)
     * @Assert\NotNull(message="O campo 'Pessoa_destinatario' deve ser informado")
     *
     * @var $pessoaDestinatario Pessoa
     */
    private $pessoaDestinatario;

    /**
     * Informa em qual tabela está a pessoaDestinatario.
     * Pode ser 'CLIENTE' ou 'FORNECEDOR'.
     *
     * @ORM\Column(name="pessoa_cadastro", type="string", nullable=true, length=30)
     */
    private $pessoaCadastro;

    /**
     *
     * @ORM\Column(name="motivo_cancelamento", type="string", nullable=true, length=3000)
     */
    private $motivoCancelamento;

    /**
     *
     * @ORM\Column(name="ambiente", type="string", nullable=true, length=4)
     */
    private $ambiente;

    /**
     *
     * @ORM\Column(name="spartacus_id_nota", type="integer", nullable=true)
     * @Assert\Range(min = 0)
     */
    private $spartacusIdNota;

    /**
     *
     * @ORM\Column(name="spartacus_status", type="integer", nullable=true)
     * @Assert\Range(min = 0)
     */
    private $spartacusStatus;

    /**
     *
     * @ORM\Column(name="spartacus_status_receita", type="integer", nullable=true)
     * @Assert\Range(min = 0)
     */
    private $spartacusStatusReceita;

    /**
     *
     * @ORM\Column(name="info_compl", type="string", nullable=true, length=3000)
     */
    private $infoCompl;

    /**
     *
     * @ORM\Column(name="spartacus_mensretorno_receita", type="string", nullable=true, length=2000)
     */
    private $spartacusMensretornoReceita;

    /**
     *
     * @ORM\Column(name="total_descontos", type="decimal", nullable=false, precision=15, scale=2)
     * @Assert\NotNull(message="O campo 'total_descontos' deve ser informado")
     * @Assert\Type("numeric", message="O campo 'total_descontos' deve ser numérico")
     */
    private $totalDescontos;

    /**
     *
     * @ORM\Column(name="subtotal", type="decimal", nullable=false, precision=15, scale=2)
     * @Assert\NotNull(message="O campo 'subtotal' deve ser informado")
     * @Assert\Type("numeric", message="O campo 'subtotal' deve ser numérico")
     */
    private $subtotal;

    /**
     *
     * @ORM\Column(name="transp_especie_volumes", type="string", nullable=true, length=200)
     */
    private $transpEspecieVolumes;

    /**
     *
     * @ORM\Column(name="transp_marca_volumes", type="string", nullable=true, length=200)
     */
    private $transpMarcaVolumes;

    /**
     *
     * @ORM\Column(name="transp_modalidade_frete", type="string", nullable=false, length=30)
     * @Assert\NotBlank(message="O campo 'transp_modalidade_frete' deve ser informado")
     */
    private $transpModalidadeFrete;

    /**
     *
     * @ORM\Column(name="transp_numeracao_volumes", type="string", nullable=true, length=200)
     */
    private $transpNumeracaoVolumes;

    /**
     *
     * @ORM\Column(name="transp_peso_bruto", type="decimal", nullable=true, precision=15, scale=2)
     * @Assert\Type("numeric", message="O campo 'transp_peso_bruto' deve ser numérico")
     */
    private $transpPesoBruto;

    /**
     *
     * @ORM\Column(name="transp_peso_liquido", type="decimal", nullable=true, precision=15, scale=2)
     * @Assert\Type("numeric", message="O campo 'transp_peso_liquido' deve ser numérico")
     */
    private $transpPesoLiquido;

    /**
     *
     * @ORM\Column(name="transp_qtde_volumes", type="decimal", nullable=true, precision=15, scale=2)
     * @Assert\Type("numeric", message="O campo 'transp_qtde_volumes' deve ser numérico")
     */
    private $transpQtdeVolumes;

    /**
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Estoque\Fornecedor")
     * @ORM\JoinColumn(name="transp_fornecedor_id", nullable=true)
     * @Assert\NotNull(message="O campo 'Transp_fornecedor' deve ser informado")
     *
     * @var $transpFornecedor Fornecedor
     */
    private $transpFornecedor;

    /**
     *
     * @ORM\Column(name="indicador_forma_pagto", type="string", nullable=false, length=30)
     * @Assert\NotBlank(message="O campo 'indicador_forma_pagto' deve ser informado")
     */
    private $indicadorFormaPagto;

    /**
     *
     * @ORM\Column(name="natureza_operacao", type="string", nullable=false, length=60)
     * @Assert\NotBlank(message="O campo 'natureza_operacao' deve ser informado")
     */
    private $naturezaOperacao;

    /**
     *
     * @ORM\Column(name="a03id_nf_referenciada", type="string", nullable=true, length=100)
     */
    private $a03idNfReferenciada;

    /**
     *
     * @ORM\Column(name="finalidade_nf", type="string", nullable=false, length=30)
     * @Assert\NotBlank(message="O campo 'finalidade_nf' deve ser informado")
     */
    private $finalidadeNf;

    /**
     *
     * @ORM\Column(name="dt_spartacus_status", type="datetime", nullable=true)
     * @Assert\NotNull(message="O campo 'dt_spartacus_status' deve ser informado")
     * @Assert\Type("\DateTime", message="O campo 'dt_spartacus_status' deve ser do tipo data/hora")
     */
    private $dtSpartacusStatus;

    /**
     *
     * @ORM\Column(name="transp_valor_total_frete", type="decimal", nullable=true, precision=15, scale=2)
     * @Assert\Type("numeric", message="O campo 'transp_valor_total_frete' deve ser numérico")
     */
    private $transpValorTotalFrete;

    /**
     *
     * @ORM\Column(name="xml_nota", type="string", nullable=true)
     */
    private $xmlNota;

    /**
     *
     * @ORM\Column(name="spartacus_mensretorno", type="string", nullable=true)
     */
    private $spartacusMensretorno;
    
    /**
     *
     * @var NotaFiscalItem[]|ArrayCollection
     *
     * @ORM\OneToMany(
     *      targetEntity="NotaFiscalItem",
     *      cascade={"persist"},
     *      mappedBy="notaFiscal",
     *      orphanRemoval=true
     * )
     */
    private $itens;

    public function __construct()
    {
        ORM\Annotation::class;
        Assert\All::class;
        
        $this->itens = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getDtEmissao()
    {
        return $this->dtEmissao;
    }

    public function setDtEmissao($dtEmissao)
    {
        $this->dtEmissao = $dtEmissao;
    }

    public function getNumero()
    {
        return $this->numero;
    }

    public function setNumero($numero)
    {
        $this->numero = $numero;
    }

    public function getValorTotal()
    {
        return $this->valorTotal;
    }

    public function setValorTotal($valorTotal)
    {
        $this->valorTotal = $valorTotal;
    }

    public function getPessoaEmitente(): ?Pessoa
    {
        return $this->pessoaEmitente;
    }

    public function setPessoaEmitente(?Pessoa $pessoaEmitente)
    {
        $this->pessoaEmitente = $pessoaEmitente;
    }

    public function getTipoNotaFiscal()
    {
        return $this->tipoNotaFiscal;
    }

    public function setTipoNotaFiscal($tipoNotaFiscal)
    {
        $this->tipoNotaFiscal = $tipoNotaFiscal;
    }

    public function getEntrada()
    {
        return $this->entrada;
    }

    public function setEntradaSaida($entrada)
    {
        $this->entrada = $entrada;
    }

    public function getSerie()
    {
        return $this->serie;
    }

    public function setSerie($serie)
    {
        $this->serie = $serie;
    }

    public function getPessoaDestinatario(): ?Pessoa
    {
        return $this->pessoaDestinatario;
    }

    public function setPessoaDestinatario(?Pessoa $pessoaDestinatario)
    {
        $this->pessoaDestinatario = $pessoaDestinatario;
    }

    public function getMotivoCancelamento()
    {
        return $this->motivoCancelamento;
    }

    public function setMotivoCancelamento($motivoCancelamento)
    {
        $this->motivoCancelamento = $motivoCancelamento;
    }

    public function getAmbiente()
    {
        return $this->ambiente;
    }

    public function setAmbiente($ambiente)
    {
        $this->ambiente = $ambiente;
    }

    public function getSpartacusIdNota()
    {
        return $this->spartacusIdNota;
    }

    public function setSpartacusIdNota($spartacusIdNota)
    {
        $this->spartacusIdNota = $spartacusIdNota;
    }

    public function getSpartacusStatus()
    {
        return $this->spartacusStatus;
    }

    public function setSpartacusStatus($spartacusStatus)
    {
        $this->spartacusStatus = $spartacusStatus;
    }

    public function getSpartacusStatusReceita()
    {
        return $this->spartacusStatusReceita;
    }

    public function setSpartacusStatusReceita($spartacusStatusReceita)
    {
        $this->spartacusStatusReceita = $spartacusStatusReceita;
    }

    public function getInfoCompl()
    {
        return $this->infoCompl;
    }

    public function setInfoCompl($infoCompl)
    {
        $this->infoCompl = $infoCompl;
    }

    public function getSpartacusMensretornoReceita()
    {
        return $this->spartacusMensretornoReceita;
    }

    public function setSpartacusMensretornoReceita($spartacusMensretornoReceita)
    {
        $this->spartacusMensretornoReceita = $spartacusMensretornoReceita;
    }

    public function getPessoaCadastro()
    {
        return $this->pessoaCadastro;
    }

    public function setPessoaCadastro($pessoaCadastro)
    {
        $this->pessoaCadastro = $pessoaCadastro;
    }

    public function getTotalDescontos()
    {
        return $this->totalDescontos;
    }

    public function setTotalDescontos($totalDescontos)
    {
        $this->totalDescontos = $totalDescontos;
    }

    public function getSubtotal()
    {
        return $this->subtotal;
    }

    public function setSubtotal($subtotal)
    {
        $this->subtotal = $subtotal;
    }

    public function getTranspEspecieVolumes()
    {
        return $this->transpEspecieVolumes;
    }

    public function setTranspEspecieVolumes($transpEspecieVolumes)
    {
        $this->transpEspecieVolumes = $transpEspecieVolumes;
    }

    public function getTranspMarcaVolumes()
    {
        return $this->transpMarcaVolumes;
    }

    public function setTranspMarcaVolumes($transpMarcaVolumes)
    {
        $this->transpMarcaVolumes = $transpMarcaVolumes;
    }

    public function getTranspModalidadeFrete()
    {
        return $this->transpModalidadeFrete;
    }

    public function setTranspModalidadeFrete($transpModalidadeFrete)
    {
        $this->transpModalidadeFrete = $transpModalidadeFrete;
    }

    public function getTranspNumeracaoVolumes()
    {
        return $this->transpNumeracaoVolumes;
    }

    public function setTranspNumeracaoVolumes($transpNumeracaoVolumes)
    {
        $this->transpNumeracaoVolumes = $transpNumeracaoVolumes;
    }

    public function getTranspPesoBruto()
    {
        return $this->transpPesoBruto;
    }

    public function setTranspPesoBruto($transpPesoBruto)
    {
        $this->transpPesoBruto = $transpPesoBruto;
    }

    public function getTranspPesoLiquido()
    {
        return $this->transpPesoLiquido;
    }

    public function setTranspPesoLiquido($transpPesoLiquido)
    {
        $this->transpPesoLiquido = $transpPesoLiquido;
    }

    public function getTranspQtdeVolumes()
    {
        return $this->transpQtdeVolumes;
    }

    public function setTranspQtdeVolumes($transpQtdeVolumes)
    {
        $this->transpQtdeVolumes = $transpQtdeVolumes;
    }

    public function getTranspFornecedor(): ?Fornecedor
    {
        return $this->transpFornecedor;
    }

    public function setTranspFornecedor(?Fornecedor $transpFornecedor)
    {
        $this->transpFornecedor = $transpFornecedor;
    }

    public function getIndicadorFormaPagto()
    {
        return $this->indicadorFormaPagto;
    }

    public function setIndicadorFormaPagto($indicadorFormaPagto)
    {
        $this->indicadorFormaPagto = $indicadorFormaPagto;
    }

    public function getNaturezaOperacao()
    {
        return $this->naturezaOperacao;
    }

    public function setNaturezaOperacao($naturezaOperacao)
    {
        $this->naturezaOperacao = $naturezaOperacao;
    }

    public function getA03idNfReferenciada()
    {
        return $this->a03idNfReferenciada;
    }

    public function setA03idNfReferenciada($a03idNfReferenciada)
    {
        $this->a03idNfReferenciada = $a03idNfReferenciada;
    }

    public function getFinalidadeNf()
    {
        return $this->finalidadeNf;
    }

    public function setFinalidadeNf($finalidadeNf)
    {
        $this->finalidadeNf = $finalidadeNf;
    }

    public function getDtSpartacusStatus()
    {
        return $this->dtSpartacusStatus;
    }

    public function setDtSpartacusStatus($dtSpartacusStatus)
    {
        $this->dtSpartacusStatus = $dtSpartacusStatus;
    }

    public function getTranspValorTotalFrete()
    {
        return $this->transpValorTotalFrete;
    }

    public function setTranspValorTotalFrete($transpValorTotalFrete)
    {
        $this->transpValorTotalFrete = $transpValorTotalFrete;
    }

    public function getXmlNota()
    {
        return $this->xmlNota;
    }

    public function setXmlNota($xmlNota)
    {
        $this->xmlNota = $xmlNota;
    }

    public function getSpartacusMensretorno()
    {
        return $this->spartacusMensretorno;
    }

    public function setSpartacusMensretorno($spartacusMensretorno)
    {
        $this->spartacusMensretorno = $spartacusMensretorno;
    }
    
    /**
     *
     * @return Collection|NotaFiscalItem[]
     */
    public function getItens(): Collection
    {
        return $this->itens;
    }
    
    public function addItem(NotaFiscalItem $item) {
        if (! $this->itens->contains($item)) {
            $this->itens->add($item);
        }
    }
}