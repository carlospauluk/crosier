<?php

namespace App\Business\Fiscal;

use App\Business\Base\EntityIdBusiness;
use App\Business\Base\PessoaBusiness;
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
use Symfony\Bridge\Doctrine\RegistryInterface;

class NotaFiscalBusiness
{

    private $doctrine;

    private $unimakeBusiness;

    private $entityIdBusiness;

    private $pessoaBusiness;

    public function __construct(RegistryInterface $doctrine, UnimakeBusiness $unimakeBusiness, EntityIdBusiness $entityIdBusiness, PessoaBusiness $pessoaBusiness)
    {
        $this->doctrine = $doctrine;
        $this->unimakeBusiness = $unimakeBusiness;
        $this->entityIdBusiness = $entityIdBusiness;
        $this->pessoaBusiness = $pessoaBusiness;
    }

    /**
     * Transforma um objeto NotaFiscal em um array com os dados para o EmissaoFiscalType.
     *
     * @param NotaFiscal $notaFiscal
     * @return NULL[]|string[]|\App\Entity\Financeiro\TipoPessoa[]
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
                $formData['razao_social'] = $notaFiscal->getPessoaDestinatario()->getNome();
                $formData['nome_fantasia'] = $notaFiscal->getPessoaDestinatario()->getNomeFantasia();
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
                $formData['cidade'] = $notaFiscal->getPessoaDestinatario()
                    ->getEndereco()
                    ->getCidade();
                $formData['estado'] = $notaFiscal->getPessoaDestinatario()
                    ->getEndereco()
                    ->getEstado();
                $formData['cep'] = $notaFiscal->getPessoaDestinatario()
                    ->getEndereco()
                    ->getCep();
            }
        }

        $formData['transp_modalidade_frete'] = $notaFiscal->getTranspModalidadeFrete();
        $formData['indicador_forma_pagto'] = $notaFiscal->getIndicadorFormaPagto();
        $formData['natureza_operacao'] = $notaFiscal->getNaturezaOperacao();
        $formData['finalidade_nf'] = $notaFiscal->getFinalidadeNf();
        $formData['entrada_saida'] = $notaFiscal->getEntrada();

        $formData['info_compl'] = $notaFiscal->getInfoCompl();

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
        } else {
            $notaFiscal = new NotaFiscal();
        }

        if (isset($formData['pessoa_id'])) {
            $pessoaDestinatario = $this->doctrine->getRepository(Pessoa::class)->find($formData['pessoa_id']);
            $notaFiscal->setPessoaDestinatario($pessoaDestinatario);
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

        $notaFiscal->setTipoNotaFiscal(isset($formData['tipo']) ? $formData['tipo'] : null);

        $notaFiscal->setTranspModalidadeFrete(isset($formData['transp_modalidade_frete']) ? $formData['transp_modalidade_frete'] : null);
        $notaFiscal->setIndicadorFormaPagto(isset($formData['indicador_forma_pagto']) ? $formData['indicador_forma_pagto'] : null);
        $notaFiscal->setFinalidadeNf(isset($formData['finalidade_nf']) ? $formData['finalidade_nf'] : null);
        $notaFiscal->setNaturezaOperacao(isset($formData['natureza_operacao']) ? $formData['natureza_operacao'] : null);
        $notaFiscal->setEntrada(isset($formData['entrada_saida']) ? $formData['entrada_saida'] : null);

        $notaFiscal->setInfoCompl(isset($formData['info_compl']) ? $formData['info_compl'] : null);

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
    public function saveNotaFiscalVenda(Venda $venda, $dataNotaFiscal)
    {
        try {
            $this->doctrine->getManager()->beginTransaction();

            $notaFiscal = $this->doctrine->getRepository(NotaFiscalVenda::class)->findNotaFiscalByVenda($venda);
            $novaNota = false;
            if (!$notaFiscal) {
                $notaFiscal = new NotaFiscal();
                $novaNota = true;
            }

            $notaFiscal->setTipoNotaFiscal($dataNotaFiscal['tipo']);

            $notaFiscal->setEntrada(false);

            $emitente = $this->doctrine->getRepository(Pessoa::class)->findByDocumento('77498442000134');
            $notaFiscal->setPessoaEmitente($emitente);

            $notaFiscal->setNaturezaOperacao('VENDA');
            $notaFiscal->setFinalidadeNf(FinalidadeNF::NORMAL['codigo']);

            $dtEmissao = new \DateTime('now', new \DateTimeZone('America/Sao_Paulo'));
            $dtEmissao->modify('-4 minutes');
            $notaFiscal->setDtEmissao($dtEmissao);
            $notaFiscal->setDtSaiEnt($dtEmissao);


            $this->handleIdeFields($notaFiscal);

            // Aqui somente coisas que fazem sentido serem alteradas depois de já ter sido (provavelmente) tentado o faturamento da Notafiscal.
            $notaFiscal->setTranspModalidadeFrete('SEM_FRETE');

            // para ser usado depois como 'chave' nas comunicações com a SEFAZ

            $notaFiscal->setIndicadorFormaPagto($venda->getPlanoPagto()
                ->getCodigo() == '1.00' ? IndicadorFormaPagto::VISTA['codigo'] : IndicadorFormaPagto::PRAZO['codigo']);

            if (isset($dataNotaFiscal['pessoa_id']) and $dataNotaFiscal['pessoa_id']) {
                $pessoa = $this->doctrine->getRepository(Pessoa::class)->find($dataNotaFiscal['pessoa_id']);
                $notaFiscal->setPessoaDestinatario($pessoa);
            }

            $notaFiscal->getItens()->clear();
            $this->doctrine->getManager()->flush();

            // Atenção, aqui tem que verificar a questão do arredondamento
            if ($venda->getSubTotal() > 0.0) {
                $fatorDesconto = 1 - ($venda->getValorTotal() / $venda->getSubTotal());
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

                $nfItem->setQtde($vendaItem->getQtde());
                $nfItem->setValorUnit($vendaItem->getPrecoVenda());
                $nfItem->setValorTotal($vendaItem->getTotalItem());

                $vDesconto = $vendaItem->getPrecoVenda() * $vendaItem->getQtde() * $fatorDesconto;
                $nfItem->setValorDesconto($vDesconto);

                $somaDescontosItens += $vDesconto;

                $nfItem->setSubTotal($vendaItem->getQtde() * $vendaItem->getPrecoVenda());

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

                $this->entityIdBusiness->handlePersist($nfItem);

                $notaFiscal->addItem($nfItem);
            }

            $totalDescontos = bcsub($venda->getSubTotal(), $venda->getValorTotal(), 2);
            $notaFiscal->setSubtotal($venda->getSubTotal());
            $notaFiscal->setValorTotal($venda->getValorTotal());
            $notaFiscal->setTotalDescontos($totalDescontos);

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


            $this->entityIdBusiness->handlePersist($notaFiscal);
            $this->doctrine->getManager()->persist($notaFiscal);
            $this->doctrine->getManager()->flush();

            if ($novaNota) {
                $notaFiscalVenda = new NotaFiscalVenda();
                $notaFiscalVenda->setNotaFiscal($notaFiscal);
                $notaFiscalVenda->setVenda($venda);
                $this->entityIdBusiness->handlePersist($notaFiscalVenda);
                $this->doctrine->getManager()->persist($notaFiscalVenda);
            }

            $this->doctrine->getManager()->commit();
            return $notaFiscal;
        } catch (\Exception $e) {
            $this->doctrine->getManager()->rollback();
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
            $this->doctrine->getManager()->beginTransaction();

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

            $this->entityIdBusiness->handlePersist($notaFiscal);
            $this->doctrine->getManager()->persist($notaFiscal);
            $this->doctrine->getManager()->flush();

            $this->doctrine->getManager()->commit();
            return $notaFiscal;
        } catch (\Exception $e) {
            $this->doctrine->getManager()->rollback();
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
    public function calcularTotais(NotaFiscal $nf)
    {
        $bdTotal = 0.0;
        $bdSubTotal = 0.0;
        $bdDescontos = 0.0;
        foreach ($nf->getItens() as $item) {
            $bdTotal += $item->getValorTotal();
            $bdSubTotal += $item->getSubTotal();
            $bdDescontos += $item->getValorDesconto() ? $item->getValorDesconto() : 0.0;
        }

        $nf->setSubTotal($bdSubTotal);
        $nf->setValorTotal($bdTotal);
        $nf->setTotalDescontos($bdDescontos);
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

        $entityManager = $this->doctrine->getManager();
        $entityManager->persist($nf);
        $entityManager->flush();

        return $nf;
    }

    /**
     * @param NotaFiscal $notaFiscal
     * @return NotaFiscal
     * @throws \Exception
     */
    public function faturar(NotaFiscal $notaFiscal)
    {
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
                $this->unimakeBusiness->imprimirCancelamento($notaFiscal);
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

            $this->entityIdBusiness->handlePersist($notaFiscal);
            $this->doctrine->getManager()->persist($notaFiscal);
            $this->doctrine->getManager()->flush();
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
        $this->entityIdBusiness->handlePersist($historico);
        $this->doctrine->getManager()->persist($historico);
        $this->doctrine->getManager()->flush();
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

        if (!$notaFiscal->getChaveAcesso()) {
            $notaFiscal->setChaveAcesso($this->buildChaveAcesso($notaFiscal));
        }


    }
}