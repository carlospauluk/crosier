<?php

namespace App\Business\Fiscal;

use App\Business\Base\PessoaBusiness;
use App\Business\CRM\ClienteBusiness;
use App\Entity\Base\Endereco;
use App\Entity\Base\Municipio;
use App\Entity\Base\Pessoa;
use App\Entity\CRM\Cliente;
use App\Entity\Estoque\Fornecedor;
use App\Entity\Fiscal\FinalidadeNF;
use App\Entity\Fiscal\IndicadorFormaPagto;
use App\Entity\Fiscal\NCM;
use App\Entity\Fiscal\NotaFiscal;
use App\Entity\Fiscal\NotaFiscalHistorico;
use App\Entity\Fiscal\NotaFiscalItem;
use App\Entity\Fiscal\NotaFiscalVenda;
use App\Entity\Fiscal\TipoNotaFiscal;
use App\Entity\Vendas\Venda;
use App\EntityHandler\Fiscal\NotaFiscalEntityHandler;
use App\EntityHandler\Fiscal\NotaFiscalHistoricoEntityHandler;
use App\EntityHandler\Fiscal\NotaFiscalVendaEntityHandler;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Classe responsável pelos trâmites com a entidade NotaFiscal.
 *
 * @package App\Business\Fiscal
 */
class NotaFiscalBusiness
{

    private $doctrine;

    private $unimakeBusiness;

    private $notaFiscalEntityHandler;

    private $notaFiscalVendaEntityHandler;

    private $notaFiscalHistoricoEntityHandler;

    private $pessoaBusiness;

    private $clienteBusiness;

    public function __construct(RegistryInterface $doctrine,
                                UnimakeBusiness $unimakeBusiness,
                                NotaFiscalEntityHandler $notaFiscalEntityHandler,
                                NotaFiscalVendaEntityHandler $notaFiscalVendaEntityHandler,
                                NotaFiscalHistoricoEntityHandler $notaFiscalHistoricoEntityHandler,
                                PessoaBusiness $pessoaBusiness,
                                ClienteBusiness $clienteBusiness)
    {
        $this->doctrine = $doctrine;
        $this->unimakeBusiness = $unimakeBusiness;
        $this->notaFiscalEntityHandler = $notaFiscalEntityHandler;
        $this->notaFiscalVendaEntityHandler = $notaFiscalVendaEntityHandler;
        $this->notaFiscalHistoricoEntityHandler = $notaFiscalHistoricoEntityHandler;
        $this->pessoaBusiness = $pessoaBusiness;
        $this->clienteBusiness = $clienteBusiness;
    }

    public function parseFormData(&$formData)
    {
        $nf = new \NumberFormatter(\Locale::getDefault(), \NumberFormatter::DECIMAL);
        $formData['transpPesoBruto'] = (isset($formData['transpPesoBruto']) and $formData['transpPesoBruto']) ? $nf->parse($formData['transpPesoBruto']) : null;
        $formData['transpPesoLiquido'] = (isset($formData['transpPesoLiquido']) and $formData['transpPesoLiquido']) ? $nf->parse($formData['transpPesoLiquido']) : null;
    }

    /**
     * Transforma um objeto NotaFiscal em um array com os dados para o EmissaoFiscalType.
     *
     * @param NotaFiscal $notaFiscal
     * @return NULL[]|string[]|\App\Entity\Financeiro\TipoPessoa[]
     * @throws \Exception
     */
    public function notaFiscal2FormData(NotaFiscal $notaFiscal, $tipoPessoa = null)
    {
        $formData = array();

        if ($notaFiscal->getPessoaDestinatario()) {
            $tipoPessoa = $notaFiscal->getPessoaDestinatario()->getTipoPessoa();
        }

        // Passo para o EmissaoFiscalType para que possa decidir se os inputs serão desabilitados.
        $formData['permiteFaturamento'] = $this->permiteFaturamento($notaFiscal);

        $formData['nota_fiscal_id'] = $notaFiscal->getId();
        $formData['uuid'] = $notaFiscal->getUuid();
        $formData['dtEmissao'] = ($notaFiscal->getDtEmissao() instanceof \DateTime) ? $notaFiscal->getDtEmissao()->format('d/m/Y H:i:s') : null;
        $formData['dtSaiEnt'] = ($notaFiscal->getDtSaiEnt() instanceof \DateTime) ? $notaFiscal->getDtSaiEnt()->format('d/m/Y H:i:s') : null;
        $formData['numero_nf'] = $notaFiscal->getNumero();
        $formData['serie'] = $notaFiscal->getSerie();
        $formData['ambiente'] = $notaFiscal->getAmbiente();
        $formData['spartacusStatus'] = $notaFiscal->getSpartacusStatus();
        $formData['spartacusStatusReceita'] = $notaFiscal->getSpartacusStatusReceita();
        $formData['spartacusMensretornoReceita'] = $notaFiscal->getSpartacusMensretornoReceita();

        $formData['cancelamento_motivo'] = $notaFiscal->getMotivoCancelamento();

        $formData['_info_status'] = $notaFiscal->getInfoStatus();

        $formData['tipo'] = $notaFiscal->getTipoNotaFiscal();
        $formData['tipoPessoa'] = $tipoPessoa;

        if ($notaFiscal->getPessoaDestinatario()) {
            $formData['pessoa_id'] = $notaFiscal->getPessoaDestinatario()->getId();
            if ($tipoPessoa == 'PESSOA_FISICA') {
                $formData['cpf'] = $notaFiscal->getPessoaDestinatario()->getDocumento();
                $formData['nome'] = $notaFiscal->getPessoaDestinatario()->getNome();
            } else if ($tipoPessoa == 'PESSOA_JURIDICA') {
                $formData['cnpj'] = $notaFiscal->getPessoaDestinatario()->getDocumento();
                $formData['razaoSocial'] = $notaFiscal->getPessoaDestinatario()->getNome();
                $formData['nomeFantasia'] = $notaFiscal->getPessoaDestinatario()->getNomeFantasia();
                $formData['inscricaoEstadual'] = $notaFiscal->getPessoaDestinatario()->getInscricaoEstadual();
            }
        }
        if ($notaFiscal->getTipoNotaFiscal() == 'NFE') {
            $this->pessoaBusiness->fillTransients($notaFiscal->getPessoaDestinatario());
            if ($notaFiscal->getPessoaDestinatario() and $notaFiscal->getPessoaDestinatario()->getEndereco()) {
                $formData['logradouro'] = $notaFiscal->getPessoaDestinatario()
                    ->getEndereco()
                    ->getLogradouro();
                $formData['numero'] = $notaFiscal->getPessoaDestinatario()
                    ->getEndereco()
                    ->getNumero();
                $formData['complemento'] = $notaFiscal->getPessoaDestinatario()
                    ->getEndereco()
                    ->getComplemento();
                $formData['bairro'] = $notaFiscal->getPessoaDestinatario()
                    ->getEndereco()
                    ->getBairro();

                $cidade = $notaFiscal->getPessoaDestinatario()->getEndereco()->getCidade();
                $estado = $notaFiscal->getPessoaDestinatario()->getEndereco()->getEstado();
                $formData['cidade'] = $cidade;
                $formData['estado'] = $estado;

                $formData['cep'] = $notaFiscal->getPessoaDestinatario()
                    ->getEndereco()
                    ->getCep();

            }
            $formData['fone1'] = $notaFiscal->getPessoaDestinatario()->getFone1();
        }

        // Campos para 'frete'
        $formData['transp_modalidade_frete'] = $notaFiscal->getTranspModalidadeFrete();
        // if ($notaFiscal->getTranspModalidadeFrete() != 'SEM_FRETE') {
        $formData['transpEspecieVolumes'] = $notaFiscal->getTranspEspecieVolumes();
        $formData['transpMarcaVolumes'] = $notaFiscal->getTranspMarcaVolumes();
        $formData['transpNumeracaoVolumes'] = $notaFiscal->getTranspNumeracaoVolumes();
        $formData['transpPesoBruto'] = $notaFiscal->getTranspPesoBruto();
        $formData['transpPesoLiquido'] = $notaFiscal->getTranspPesoLiquido();
        $formData['transpQtdeVolumes'] = $notaFiscal->getTranspQtdeVolumes();
        if ($notaFiscal->getTranspFornecedor() and $notaFiscal->getTranspFornecedor()->getId()) {
            $formData['transpFornecedor_id'] = $notaFiscal->getTranspFornecedor()->getId();
            $formData['transpFornecedor_cnpj'] = $notaFiscal->getTranspFornecedor()->getPessoa()->getDocumento();
            $formData['transpFornecedor_razaoSocial'] = $notaFiscal->getTranspFornecedor()->getPessoa()->getNome();
            $formData['transpFornecedor_nomeFantasia'] = $notaFiscal->getTranspFornecedor()->getPessoa()->getNomeFantasia();
        } else {
            $formData['transpFornecedor_id'] = null;
            $formData['transpFornecedor_cnpj'] = null;
            $formData['transpFornecedor_razaoSocial'] = null;
            $formData['transpFornecedor_nomeFantasia'] = null;
        }
        // }

        $formData['indicador_forma_pagto'] = $notaFiscal->getIndicadorFormaPagto();
        $formData['natureza_operacao'] = $notaFiscal->getNaturezaOperacao();
        $formData['finalidade_nf'] = $notaFiscal->getFinalidadeNf();
        $formData['entrada'] = $notaFiscal->getEntrada();

//        $formData['subtotal'] = number_format($notaFiscal->getSubtotal(),2,',','.');
//        $formData['total_descontos'] = number_format($notaFiscal->getTotalDescontos(),2,',','.');
//        $formData['valor_total'] = number_format($notaFiscal->getValorTotal(),2,',','.');

        $formData['subtotal'] = $notaFiscal->getSubtotal();
        $formData['totalDescontos'] = $notaFiscal->getTotalDescontos();
        $formData['valorTotal'] = $notaFiscal->getValorTotal();

        $formData['info_compl'] = $notaFiscal->getInfoCompl();


        $formData['carta_correcao'] = $notaFiscal->getCartaCorrecao();

        $formData['a03idNfReferenciada'] = $notaFiscal->getA03idNfReferenciada();

        return $formData;
    }

    /**
     * Transforma o array para uma NotaFiscal
     *
     * @param array $formData
     * @return NotaFiscal|null|object
     */
    public function formData2NotaFiscal($formData)
    {
        if (isset($formData['nota_fiscal_id'])) {
            $notaFiscal = $this->doctrine->getRepository(NotaFiscal::class)->find($formData['nota_fiscal_id']);
            if (!$notaFiscal) {
                $notaFiscal = new NotaFiscal();
            }
        } else {
            $notaFiscal = new NotaFiscal();
        }

        $notaFiscal->setTipoNotaFiscal(isset($formData['tipo']) ? $formData['tipo'] : null);

        // Se veio o pessoa_id, é porque achou na busca por CPF/CNPJ
        if (isset($formData['pessoa_id']) and $formData['pessoa_id']) {
            $pessoaDestinatario = $this->doctrine->getRepository(Pessoa::class)->find($formData['pessoa_id']);
            $notaFiscal->setPessoaDestinatario($pessoaDestinatario);
        } else {
            // tipoPessoa sempre deverá estar setado, mas verifica se passou o cpf ou cnpj (não é NFCe anônima)
            if (isset($formData['tipoPessoa']) and (isset($formData['cpf']) or isset($formData['cnpj']))) {
                $tipoPessoa = $formData['tipoPessoa'];
                $pessoa = new Pessoa();
                $pessoa->setTipoPessoa($tipoPessoa);
                if ($tipoPessoa == 'PESSOA_FISICA') {
                    $documento = preg_replace("/[^0-9]/", "", $formData['cpf']);
                    $nome = $formData['nome'];
                    $pessoa->setDocumento($documento);
                    $pessoa->setNome($nome);

                } else {
                    $documento = preg_replace("/[^0-9]/", "", $formData['cnpj']);
                    $razaoSocial = $formData['razaoSocial'];
                    $nomeFantasia = $formData['nomeFantasia'];
                    $inscricaoEstadual = $formData['inscricaoEstadual'];

                    $pessoa->setDocumento($documento);
                    $pessoa->setNome($razaoSocial);
                    $pessoa->setNomeFantasia($nomeFantasia);
                    $pessoa->setInscricaoEstadual($inscricaoEstadual);
                }
                $notaFiscal->setPessoaDestinatario($pessoa);

                // FIXME: depois que resolver a zona de pessoa/cliente/fornecedor/funcionário, arrumar aqui...
                if ($notaFiscal->getTipoNotaFiscal() == 'NFE') {
                    $endereco = new Endereco();
                    $endereco->setTipoEndereco('OUTROS');
                    $endereco->setLogradouro($formData['logradouro']);
                    $endereco->setNumero($formData['numero']);
                    $endereco->setComplemento($formData['complemento']);
                    $endereco->setBairro($formData['bairro']);
                    $endereco->setCep($formData['cep']);
                    $endereco->setCidade($formData['cidade']);
                    $endereco->setEstado($formData['estado']);
                    $pessoa->setEndereco($endereco);

                    $pessoa->setFone1($formData['fone1']);
                    $pessoa->setEmail($formData['email']);
                }


            }
        }

        $notaFiscal->setUuid(isset($formData['uuid']) ? $formData['uuid'] : null);


        if ($formData['dtEmissao']) {
            $dtEmissao = \DateTime::createFromFormat('d/m/Y H:i:s', $formData['dtEmissao']);
            $notaFiscal->setDtEmissao($dtEmissao);
        }
        if ($formData['dtSaiEnt']) {
            $dtSaiEnt = \DateTime::createFromFormat('d/m/Y H:i:s', $formData['dtSaiEnt']);
            $notaFiscal->setDtSaiEnt($dtSaiEnt);
        }

        $notaFiscal->setNumero(isset($formData['numero_nf']) ? $formData['numero_nf'] : null);
        $notaFiscal->setSerie(isset($formData['serie']) ? $formData['serie'] : null);
        $notaFiscal->setAmbiente(isset($formData['ambiente']) ? $formData['ambiente'] : null);

        $notaFiscal->setTranspModalidadeFrete(isset($formData['transp_modalidade_frete']) ? $formData['transp_modalidade_frete'] : null);
        $notaFiscal->setIndicadorFormaPagto(isset($formData['indicador_forma_pagto']) ? $formData['indicador_forma_pagto'] : null);
        $notaFiscal->setFinalidadeNf(isset($formData['finalidade_nf']) ? $formData['finalidade_nf'] : null);
        $notaFiscal->setNaturezaOperacao(isset($formData['natureza_operacao']) ? $formData['natureza_operacao'] : null);
        $notaFiscal->setEntrada(isset($formData['entrada']) ? $formData['entrada'] : null);

        $notaFiscal->setInfoCompl(isset($formData['info_compl']) ? $formData['info_compl'] : null);

        $notaFiscal->setSubtotal(isset($formData['subtotal']) ? floatval($formData['subtotal']) : null);
        $notaFiscal->setTotalDescontos(isset($formData['totalDescontos']) ? floatval($formData['totalDescontos']) : null);
        $notaFiscal->setValorTotal(isset($formData['valorTotal']) ? floatval($formData['valorTotal']) : null);

        $notaFiscal->setCartaCorrecao(isset($formData['carta_correcao']) ? $formData['carta_correcao'] : null);


        // Campos para 'frete'
        $notaFiscal->setTranspModalidadeFrete($formData['transp_modalidade_frete']);
        $notaFiscal->setTranspEspecieVolumes(isset($formData['transpEspecieVolumes']) ? $formData['transpEspecieVolumes'] : null);
        $notaFiscal->setTranspMarcaVolumes(isset($formData['transpMarcaVolumes']) ? $formData['transpMarcaVolumes'] : null);
        $notaFiscal->setTranspNumeracaoVolumes(isset($formData['transpNumeracaoVolumes']) ? $formData['transpNumeracaoVolumes'] : null);
        $notaFiscal->setTranspPesoBruto(isset($formData['transpPesoBruto']) ? $formData['transpPesoBruto'] : null);
        $notaFiscal->setTranspPesoLiquido(isset($formData['transpPesoLiquido']) ? $formData['transpPesoLiquido'] : null);
        $notaFiscal->setTranspQtdeVolumes(isset($formData['transpQtdeVolumes']) ? $formData['transpQtdeVolumes'] : null);

        if ($formData['transpFornecedor_id']) {
            $transpFornecedor = $this->doctrine->getRepository(Fornecedor::class)->find($formData['transpFornecedor_id']);
            $notaFiscal->setTranspFornecedor($transpFornecedor);
        }

        $notaFiscal->setA03idNfReferenciada($formData['a03idNfReferenciada']);

        return $notaFiscal;
    }

    /**
     * Transforma um ven_venda em um fis_nf.
     *
     * @param Venda $venda
     * @param
     *            $tipoNotaFiscal
     * @throws \Exception
     * @return NULL|\App\Entity\Fiscal\NotaFiscal
     */
    public function saveNotaFiscalVenda(Venda $venda, NotaFiscal $notaFiscal)
    {
        try {
            $this->doctrine->getEntityManager()->beginTransaction();

            if (!$notaFiscal->getPessoaDestinatario()->getDocumento()) {
                $notaFiscal->setPessoaDestinatario(null);
            }

            if ($notaFiscal->getPessoaDestinatario() and !$notaFiscal->getPessoaDestinatario()->getId()) {
                $cliente = $this->clienteBusiness->savePessoaClienteComEndereco($notaFiscal->getPessoaDestinatario());
                $notaFiscal->setPessoaCadastro('CLIENTE');
                $notaFiscal->setPessoaDestinatario($cliente->getPessoa());
            }

            $jaExiste = $this->doctrine->getRepository(NotaFiscalVenda::class)->findNotaFiscalByVenda($venda);
            if ($jaExiste) {
                $notaFiscal = $jaExiste;
                $novaNota = false;
            } else {
                $novaNota = true;
            }

            $notaFiscal->setEntrada(false);

            $emitente = $this->doctrine->getRepository(Pessoa::class)->findByDocumento('77498442000134');
            $notaFiscal->setPessoaEmitente($emitente);

            $notaFiscal->setNaturezaOperacao('VENDA');

            $dtEmissao = new \DateTime('now', new \DateTimeZone('America/Sao_Paulo'));
            $dtEmissao->modify('-4 minutes');
            $notaFiscal->setDtEmissao($dtEmissao);
            $notaFiscal->setDtSaiEnt($dtEmissao);

            $notaFiscal->setFinalidadeNf(FinalidadeNF::NORMAL['key']);

            $this->handleIdeFields($notaFiscal);

            // Aqui somente coisas que fazem sentido serem alteradas depois de já ter sido (provavelmente) tentado o faturamento da Notafiscal.
            $notaFiscal->setTranspModalidadeFrete('SEM_FRETE');

            // para ser usado depois como 'chave' nas comunicações com a SEFAZ

            $notaFiscal->setIndicadorFormaPagto($venda->getPlanoPagto()
                ->getCodigo() == '1.00' ? IndicadorFormaPagto::VISTA['codigo'] : IndicadorFormaPagto::PRAZO['codigo']);

            $notaFiscal->getItens()->clear();
            $this->doctrine->getEntityManager()->flush();

            // Atenção, aqui tem que verificar a questão do arredondamento
            if ($venda->getSubTotal() > 0.0) {
                $fatorDesconto = 1 - (round(bcdiv($venda->getValorTotal(), $venda->getSubTotal(), 4), 2));
            } else {
                $fatorDesconto = 1;
            }

            $somaDescontosItens = 0.0;
            $ordem = 1;
            foreach ($venda->getItens() as $vendaItem) {

                $nfItem = new NotaFiscalItem();
                $nfItem->setNotaFiscal($notaFiscal);

                if ($vendaItem->getNcm()) {
                    $nfItem->setNcm($vendaItem->getNcm());
                } else {
                    $nfItem->setNcm('62179000');
                }

                $existe = $this->doctrine->getRepository(NCM::class)->findByNCM($nfItem->getNcm());
                $nfItem->setNcmExistente($existe ? true : false);

                $nfItem->setOrdem($ordem++);

                $nfItem->setQtde(floatval($vendaItem->getQtde()));
                $nfItem->setValorUnit(floatval($vendaItem->getPrecoVenda()));
                $nfItem->setValorTotal(floatval($vendaItem->getTotalItem()));

                $vDesconto = round(bcmul($vendaItem->getTotalItem(), $fatorDesconto, 4), 2);
                $nfItem->setValorDesconto($vDesconto);

                // Somando aqui pra verificar depois se o total dos descontos dos itens bate com o desconto global da nota.
                $somaDescontosItens += $vDesconto;

                $nfItem->setSubTotal($vendaItem->getTotalItem());

                $nfItem->setIcmsAliquota(0.0);
                $nfItem->setCfop("5102");
                $nfItem->setUnidade($vendaItem->getGradeTamanho()
                    ->getTamanho() != null ? $vendaItem->getGradeTamanho()
                    ->getTamanho() : $vendaItem->getNcGradeTamanho());

                if ($vendaItem->getProduto() != null) {
                    $nfItem->setCodigo($vendaItem->getProduto()
                        ->getReduzido());
                    $nfItem->setDescricao($vendaItem->getProduto()
                            ->getDescricao() . " (" . $vendaItem->getGradeTamanho()
                            ->getTamanho() . ")");
                } else {
                    $nfItem->setCodigo($vendaItem->getNcReduzido());
                    $nfItem->setDescricao($vendaItem->getNcDescricao() . " (" . $vendaItem->getNcGradeTamanho() . ")");
                }

                $notaFiscal->addItem($nfItem);
            }

            $this->calcularTotais($notaFiscal);
            $totalDescontos = bcsub($notaFiscal->getSubTotal(), $notaFiscal->getValorTotal(), 2);

            if (bcsub(abs($totalDescontos), abs($somaDescontosItens), 2) != 0) {
                $diferenca = $totalDescontos - $somaDescontosItens;
                $notaFiscal->getItens()
                    ->get(0)
                    ->setValorDesconto($notaFiscal->getItens()
                            ->get(0)
                            ->getValorDesconto() + $diferenca);
                $notaFiscal->getItens()
                    ->get(0)
                    ->calculaTotais();
            }


            $notaFiscal = $this->notaFiscalEntityHandler->save($notaFiscal);
            $this->doctrine->getEntityManager()->flush();

            if ($novaNota) {
                $notaFiscalVenda = new NotaFiscalVenda();
                $notaFiscalVenda->setNotaFiscal($notaFiscal);
                $notaFiscalVenda->setVenda($venda);
                $this->notaFiscalVendaEntityHandler->save($notaFiscalVenda);
            }

            $this->doctrine->getEntityManager()->commit();
            return $notaFiscal;
        } catch (\Exception $e) {
            $this->doctrine->getEntityManager()->rollback();
            $erro = "Erro ao gerar registro da Nota Fiscal";
            throw new \Exception($erro, null, $e);
        }
    }

    /**
     * Salvar uma notaFiscal normal.
     *
     * @param
     *            $tipoNotaFiscal
     * @throws \Exception
     * @return NULL|\App\Entity\Fiscal\NotaFiscal
     */
    public function saveNotaFiscal(NotaFiscal $notaFiscal)
    {
        try {
            $this->doctrine->getEntityManager()->beginTransaction();

            if (!$notaFiscal->getPessoaEmitente()) {
                $emitente = $this->doctrine->getRepository(Pessoa::class)->findByDocumento('77498442000134');
                $notaFiscal->setPessoaEmitente($emitente);
            }

            if (!$notaFiscal->getUuid()) {
                $notaFiscal->setUuid(md5(uniqid(rand(), true)));
            }

            if (!$notaFiscal->getSerie()) {
                $chave = "BONSUCESSO_FISCAL_" . strtoupper($notaFiscal->getTipoNotaFiscal()) . "_SERIE";
                $serie = getenv($chave);
                if (!$serie) {
                    throw new \Exception("'" . $chave . "' não informado");
                }
                $notaFiscal->setSerie($serie);
            }

            if (!$notaFiscal->getCnf()) {
                $cNF = rand(10000000, 99999999);
                $notaFiscal->setCnf($cNF);
            }

            $this->calcularTotais($notaFiscal);

            $this->notaFiscalEntityHandler->save($notaFiscal);
            $this->doctrine->getEntityManager()->flush();

            $this->doctrine->getEntityManager()->commit();
            return $notaFiscal;
        } catch (\Exception $e) {
            $this->doctrine->getEntityManager()->rollback();
            $erro = "Erro ao salvar Nota Fiscal";
            throw new \Exception($erro, null, $e);
        }
    }

    /**
     * Calcula o total da nota e o total de descontos.
     *
     * @param
     *            nf
     */
    public function calcularTotais(NotaFiscal $notaFiscal)
    {
        $subTotal = 0.0;
        $descontos = 0.0;
        foreach ($notaFiscal->getItens() as $item) {
            $item->calculaTotais();
            $subTotal += $item->getSubTotal();
            $descontos += $item->getValorDesconto() ? $item->getValorDesconto() : 0.0;
        }
        $notaFiscal->setSubTotal($subTotal);
        $notaFiscal->setTotalDescontos($descontos);
        $notaFiscal->setValorTotal($subTotal - $descontos);
    }

    public function corrigirPessoaDestinatario(NotaFiscal $nf)
    {
        $documento = $nf->getPessoaDestinatario()->getDocumento();

        if ($nf->getPessoaCadastro() == null) {

            $cliente = $this->doctrine->getRepository(Cliente::class)->findByDocumento($documento);

            if ($cliente) {
                $nf->setPessoaCadastro('CLIENTE');
            } else {
                $fornecedor = $this->doctrine->getRepository(Fornecedor::class)->findByDocumento($documento);
                if ($fornecedor) {
                    $nf->setPessoaCadastro('FORNECEDOR');
                } else {
                    throw new \Exception("Destinatário não encontrado em Clientes ou Fornecedores.");
                }
            }
        } else {
            if ('CLIENTE' == $nf->getPessoaCadastro()) {
                $cliente = $this->doctrine->getRepository(Cliente::class)->findByDocumento($documento);
                if (!$cliente) {
                    $fornecedor = $this->doctrine->getRepository(Fornecedor::class)->findByDocumento($documento);
                    if (!$fornecedor) {
                        throw new \Exception("Destinatário não encontrado em Clientes ou Fornecedores.");
                    }
                }
            } else {
                $fornecedor = $this->doctrine->getRepository(Fornecedor::class)->findByDocumento($documento);
                if (!$fornecedor) {
                    $cliente = $this->doctrine->getRepository(Cliente::class)->findByDocumento($documento);
                    if (!$cliente) {
                        throw new \Exception("Destinatário não encontrado em Clientes ou Fornecedores.");
                    }
                }
            }
        }

        $entityManager = $this->doctrine->getEntityManager();
        $entityManager->persist($nf);
        $entityManager->flush();

        return $nf;
    }

    /**
     *
     * @param NotaFiscal $notaFiscal
     * @throws \Exception
     */
    public function checkNotaFiscal(NotaFiscal $notaFiscal)
    {
        if (!$notaFiscal) {
            throw new \Exception('Nota Fiscal null');
        }
        if ($notaFiscal->getPessoaDestinatario()) {
            $this->pessoaBusiness->fillTransients($notaFiscal->getPessoaDestinatario());
            if ($notaFiscal->getPessoaDestinatario()->getEndereco()) {
                $cidade = $notaFiscal->getPessoaDestinatario()->getEndereco()->getCidade();
                $estado = $notaFiscal->getPessoaDestinatario()->getEndereco()->getEstado();
                $bsMunicipio = $this->doctrine->getRepository(Municipio::class)->findOneBy(['municipioNome' => $cidade, 'ufSigla' => $estado]);
                if (!$bsMunicipio) {
                    throw new \Exception("Município inválido: [" . $cidade . "-" . $estado . "]");
                }
            }
        }
    }

    /**
     * @param NotaFiscal $notaFiscal
     * @return NotaFiscal
     * @throws \Exception
     */
    public function faturar(NotaFiscal $notaFiscal)
    {
        // Verifica algumas regras antes de mandar faturar na receita.
        $this->checkNotaFiscal($notaFiscal);

        $this->addHistorico($notaFiscal, -1, "INICIANDO FATURAMENTO");
        if ($this->permiteFaturamento($notaFiscal)) {
            $this->handleIdeFields($notaFiscal);
            $notaFiscal = $this->unimakeBusiness->faturar($notaFiscal);
            if ($notaFiscal) {
                $this->addHistorico($notaFiscal, $notaFiscal->getSpartacusStatus(), $notaFiscal->getSpartacusMensretornoReceita(), "FATURAMENTO PROCESSADO");
            } else {
                $this->addHistorico($notaFiscal, -2, "PROBLEMA AO FATURAR");
            }
        } else {
            $this->addHistorico($notaFiscal, 0, "NOTA FISCAL NÃO FATURÁVEL. STATUS = [" . $notaFiscal->getSpartacusStatus() . "]");
        }

        return $notaFiscal;
    }

    public function consultarStatus(NotaFiscal $notaFiscal)
    {
        $this->addHistorico($notaFiscal, -1, "INICIANDO CONSULTA DE STATUS");
        try {
            $notaFiscal = $this->unimakeBusiness->consultarStatus($notaFiscal);
            if ($notaFiscal) {
                $this->addHistorico($notaFiscal, $notaFiscal->getSpartacusStatus(), $notaFiscal->getSpartacusMensretornoReceita(), "CONSULTA DE STATUS PROCESSADA");
            } else {
                $this->addHistorico($notaFiscal, -2, "PROBLEMA AO CONSULTAR STATUS");
            }
        } catch (\Exception $e) {
            $this->addHistorico($notaFiscal, -2, "PROBLEMA AO CONSULTAR STATUS: [" . $e->getMessage() . "]");
        }
        return $notaFiscal;
    }

    /**
     * Só exibe o botão faturar se tiver nestas condições.
     * Lembrando que o botão "Faturar" serve tanto para faturar a primeira vez, como para tentar faturar novamente nos casos de erros.
     *
     * @param
     *            venda
     * @return
     */
    public function permiteFaturamento(NotaFiscal $notaFiscal)
    {
        // if ($notaFiscal->getSpartacusMensretorno() == "AGUARDANDO FATURAMENTO") {
        // return false;
        // }

        // // Se o status for 100, não precisa refaturar.
        // if ($notaFiscal->getSpartacusStatus()) {

        // // aprovada
        // if ("100" == $notaFiscal->getSpartacusStatus()) {
        // return false;
        // }
        // // cancelada
        // if ("101" == $notaFiscal->getSpartacusStatus()) {
        // return false;
        // }
        // if ("204" == $notaFiscal->getSpartacusStatus()) {
        // return false;
        // }

        // if ("0" == $notaFiscal->getSpartacusStatus()) {

        // if (strpos($notaFiscal->getSpartacusMensRetornoReceita(), "DUPLICIDADE DE NF") !== FALSE) {
        // return false;
        // }

        // if ("AGUARDANDO FATURAMENTO" == $notaFiscal->getSpartacusMensRetornoReceita()) {
        // if ($notaFiscal->getDtSpartacusStatus()) {
        // $dtStatus = $notaFiscal->getDtSpartacusStatus();

        // $agora = new \DateTime();
        // $diff = $agora->diff($dtStatus);

        // $minutos = $diff->i;

        // // Se já passou 3 minutos, então permite refaturar.
        // if ($minutos > 2) {
        // return true;
        // }
        // }
        // return false;
        // }
        // }
        // }
        if ($notaFiscal->getSpartacusStatus() == -100 or $notaFiscal->getSpartacusStatus() == 100 or $notaFiscal->getSpartacusStatus() == 101 or $notaFiscal->getSpartacusStatus() == 204) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Por enquanto o 'cancelar' segue a mesma regra do 'reimprimir'.
     *
     * @param NotaFiscal $notaFiscal
     */
    public function permiteCancelamento(NotaFiscal $notaFiscal)
    {
        if ($notaFiscal->getSpartacusStatus() == '100') {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Verifica se é possível reimprimir.
     *
     * @param NotaFiscal $notaFiscal
     * @return boolean
     */
    public function permiteReimpressao(NotaFiscal $notaFiscal)
    {
        if ($notaFiscal->getId()) {
            if ($notaFiscal->getSpartacusStatus() == 100 || $notaFiscal->getSpartacusStatus() == 204) {
                return true;
            } else {
                if ($notaFiscal->getSpartacusStatus() == 0 && strpos($notaFiscal->getSpartacusMensRetornoReceita(), "DUPLICIDADE DE NF") !== FALSE) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Verifica se é possível reimprimir o cancelamento.
     *
     * @param NotaFiscal $notaFiscal
     * @return boolean
     */
    public function permiteReimpressaoCancelamento(NotaFiscal $notaFiscal)
    {
        if ($notaFiscal->getId()) {
            if ($notaFiscal->getSpartacusStatus() == 101) {
                return true;
            }
        }
        return false;
    }

    /**
     * Verifica se é possível enviar carta de correção.
     *
     * @param NotaFiscal $notaFiscal
     * @return boolean
     */
    public function permiteCartaCorrecao(NotaFiscal $notaFiscal)
    {
        if ($notaFiscal->getId()) {
            if ($notaFiscal->getSpartacusStatus() == 100) {
                return true;
            }
        }
        return false;
    }

    /**
     * Verifica se é possível reimprimir uma carta de correção.
     *
     * @param NotaFiscal $notaFiscal
     * @return boolean
     */
    public function permiteReimpressaoCartaCorrecao(NotaFiscal $notaFiscal)
    {
        if ($notaFiscal->getId()) {
            if ($notaFiscal->getSpartacusStatus() == 100) {
                if ($notaFiscal->getCartaCorrecao()) {
                    if ($notaFiscal->getCartaCorrecaoSeq() > 0) {
                        return true;
                    }
                }
            }
        }
        return false;
    }

    public function imprimir(NotaFiscal $notaFiscal)
    {
        return $this->unimakeBusiness->imprimir($notaFiscal);
    }

    public function imprimirCancelamento(NotaFiscal $notaFiscal)
    {
        return $this->unimakeBusiness->imprimirCancelamento($notaFiscal);
    }

    public function imprimirCartaCorrecao(NotaFiscal $notaFiscal)
    {
        return $this->unimakeBusiness->imprimirCartaCorrecao($notaFiscal);
    }

    public function cancelar(NotaFiscal $notaFiscal)
    {
        $this->addHistorico($notaFiscal, -1, "INICIANDO CANCELAMENTO");
        $notaFiscal = $this->checkChaveAcesso($notaFiscal);
        try {
            $notaFiscal = $this->unimakeBusiness->cancelar($notaFiscal);
            if ($notaFiscal) {
                $this->addHistorico($notaFiscal, $notaFiscal->getSpartacusStatus(), $notaFiscal->getSpartacusMensretornoReceita(), "CANCELAMENTO PROCESSADO");
                $notaFiscal = $this->consultarStatus($notaFiscal);
                $this->unimakeBusiness->imprimirCancelamento($notaFiscal);
            } else {
                $this->addHistorico($notaFiscal, -2, "PROBLEMA AO CANCELAR");
            }
        } catch (\Exception $e) {
            $this->addHistorico($notaFiscal, -2, "PROBLEMA AO CANCELAR: [" . $e->getMessage() . "]");
        }
        return $notaFiscal;
    }

    public function cartaCorrecao(NotaFiscal $notaFiscal)
    {
        $this->addHistorico($notaFiscal, -1, "INICIANDO ENVIO DA CARTA DE CORREÇÃO");
        $notaFiscal = $this->checkChaveAcesso($notaFiscal);
        try {
            $notaFiscal = $this->unimakeBusiness->cartaCorrecao($notaFiscal);
            if ($notaFiscal) {
                $this->addHistorico($notaFiscal, $notaFiscal->getSpartacusStatus(), $notaFiscal->getSpartacusMensretornoReceita(), "ENVIO DA CARTA DE CORREÇÃO PROCESSADO");
                $notaFiscal = $this->consultarStatus($notaFiscal);
                $this->unimakeBusiness->imprimirCartaCorrecao($notaFiscal);
            } else {
                $this->addHistorico($notaFiscal, -2, "PROBLEMA AO ENVIAR CARTA DE CORREÇÃO");
            }
        } catch (\Exception $e) {
            $this->addHistorico($notaFiscal, -2, "PROBLEMA AO ENVIAR CARTA DE CORREÇÃO: [" . $e->getMessage() . "]");
        }
        return $notaFiscal;
    }

    public function checkChaveAcesso(NotaFiscal $notaFiscal)
    {
        if (!$notaFiscal->getChaveAcesso()) {
            $notaFiscal->setChaveAcesso($this->buildChaveAcesso($notaFiscal));

            $notaFiscal = $this->notaFiscalEntityHandler->save($notaFiscal);
            $this->doctrine->getEntityManager()->flush();
        }
        return $notaFiscal;
    }

    public function buildChaveAcesso(NotaFiscal $notaFiscal)
    {
        $cUF = "41";
        $cnpj = $notaFiscal->getPessoaEmitente()->getDocumento();
        $ano = $notaFiscal->getDtEmissao()->format('y');
        $mes = $notaFiscal->getDtEmissao()->format('m');
        $mod = TipoNotaFiscal::get($notaFiscal->getTipoNotaFiscal())['codigo'];
        $serie = $notaFiscal->getSerie();
        $nNF = $notaFiscal->getNumero();
        $cNF = $notaFiscal->getCnf();

        // Campo tpEmis
        // 1-Emissão Normal
        // 2-Contingência em Formulário de Segurança
        // 3-Contingência SCAN (desativado)
        // 4-Contingência EPEC
        // 5-Contingência em Formulário de Segurança FS-DA
        // 6-Contingência SVC-AN
        // 7-Contingência SVC-RS
        $tpEmis = 1;

        $chaveAcesso = NFeKeys::build($cUF, $ano, $mes, $cnpj, $mod, $serie, $nNF, $tpEmis, $cNF);
        return $chaveAcesso;
    }

    public function addHistorico(NotaFiscal $notaFiscal, $codigoStatus, $descricao, $obs = null)
    {
        $historico = new NotaFiscalHistorico();
        $dtHistorico = new \DateTime('now', new \DateTimeZone('America/Sao_Paulo'));
        $historico->setDtHistorico($dtHistorico);
        $historico->setCodigoStatus($codigoStatus);
        $historico->setDescricao($descricao ? $descricao : " ");
        $historico->setObs($obs);
        $historico->setNotaFiscal($notaFiscal);
        $this->notaFiscalHistoricoEntityHandler->save($historico);
        $this->doctrine->getEntityManager()->flush();
    }

    /**
     * Lida com os campos que são gerados programaticamente.
     *
     * @param $notaFiscal
     * @throws \Exception
     */
    public function handleIdeFields(NotaFiscal $notaFiscal): void
    {
        if (!$notaFiscal->getUuid()) {
            $notaFiscal->setUuid(md5(uniqid(rand(), true)));
        }

        if (!$notaFiscal->getCnf()) {
            $cNF = rand(10000000, 99999999);
            $notaFiscal->setCnf($cNF);
        }

        if (!$notaFiscal->getNumero()) {
            $ambiente = getenv("BONSUCESSO_FISCAL_AMBIENTE");
            if (!$ambiente) {
                throw new \Exception("'BONSUCESSO_FISCAL_AMBIENTE' não informado");
            }
            $notaFiscal->setAmbiente($ambiente);

            if (!$notaFiscal->getTipoNotaFiscal()) {
                throw new \Exception("Impossível gerar número sem saber o tipo da nota fiscal.");
            }
            $chave = "BONSUCESSO_FISCAL_" . strtoupper($notaFiscal->getTipoNotaFiscal()) . "_SERIE";
            $serie = getenv($chave);
            if (!$serie) {
                throw new \Exception("'" . $chave . "' não informado");
            }
            $notaFiscal->setSerie($serie);
            $nnf = $this->doctrine->getRepository(NotaFiscal::class)->findProxNumFiscal($ambiente == 'PROD', $notaFiscal->getSerie(), $notaFiscal->getTipoNotaFiscal());
            $notaFiscal->setNumero($nnf);
        }

        if (!$notaFiscal->getDtEmissao()) {
            $notaFiscal->setDtEmissao(new \DateTime('now'));
        }

//        if (!$notaFiscal->getChaveAcesso()) {
        $notaFiscal->setChaveAcesso($this->buildChaveAcesso($notaFiscal));
//        }
    }


    public function consultarCNPJ($cnpj)
    {
        return $this->unimakeBusiness->consultarCNPJ($cnpj);
    }


}