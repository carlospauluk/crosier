<?php

namespace App\Business\Fiscal;

use App\Business\Base\PessoaBusiness;
use App\Entity\Base\Municipio;
use App\Entity\Fiscal\FinalidadeNF;
use App\Entity\Fiscal\ModalidadeFrete;
use App\Entity\Fiscal\NotaFiscal;
use App\Entity\Fiscal\TipoNotaFiscal;
use App\EntityHandler\Fiscal\NotaFiscalEntityHandler;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Classe que trata da integração com o Unimake (UniNfe).
 *
 * @author Carlos Eduardo Pauluk
 *
 */
class UnimakeBusiness
{

    private $pessoaBusiness;

    private $notaFiscalEntityHandler;

    private $doctrine;

    public function __construct(RegistryInterface $doctrine,
                                PessoaBusiness $pessoaBusiness,
                                NotaFiscalEntityHandler $notaFiscalEntityHandler)
    {
        $this->doctrine = $doctrine;
        $this->pessoaBusiness = $pessoaBusiness;
        $this->notaFiscalEntityHandler = $notaFiscalEntityHandler;
    }

    public function faturar(NotaFiscal $notaFiscal)
    {
        $pastaXMLExemplos = getenv('PASTAARQUIVOSXMLEXEMPLO');

        $exemploNFe = file_get_contents($pastaXMLExemplos . "/exemplo-nfe.xml");
        $nfe = simplexml_load_string($exemploNFe);

        $nfe->infNFe->ide->nNF = $notaFiscal->getNumero();

        $nfe->infNFe->ide->cNF = $notaFiscal->getCnf();

        $nfe->infNFe->ide->mod = TipoNotaFiscal::get($notaFiscal->getTipoNotaFiscal())['codigo'];
        $nfe->infNFe->ide->serie = $notaFiscal->getSerie();

        // Campo tpEmis
        // 1-Emissão Normal
        // 2-Contingência em Formulário de Segurança
        // 3-Contingência SCAN (desativado)
        // 4-Contingência EPEC
        // 5-Contingência em Formulário de Segurança FS-DA
        // 6-Contingência SVC-AN
        // 7-Contingência SVC-RS

        $tpEmis = 1;
        $nfe->infNFe->ide->tpEmis = $tpEmis;

        $nfe->infNFe['Id'] = "NFe" . $notaFiscal->getChaveAcesso();
        $nfe->infNFe->ide->cDV = NFeKeys::verifyingDigit(substr($notaFiscal->getChaveAcesso(), 0, -1));

        $nfe->infNFe->ide->natOp = $notaFiscal->getNaturezaOperacao();

        $nfe->infNFe->ide->dhEmi = $notaFiscal->getDtEmissao()->format('Y-m-d\TH:i:s\-03:00');

        $nfe->infNFe->ide->tpNF = $notaFiscal->getEntrada() ? '0' : '1';

        $finNFe = FinalidadeNF::get($notaFiscal->getFinalidadeNf())['codigo'];
        $nfe->infNFe->ide->finNFe = $finNFe;

        if ($notaFiscal->getTipoNotaFiscal() == 'NFE') {
            $nfe->infNFe->ide->dhSaiEnt = $notaFiscal->getDtSaiEnt()->format('Y-m-d\TH:i:s\-03:00');
        } else {
            unset($nfe->infNFe->ide->dhSaiEnt);
            $nfe->infNFe->ide->idDest = 1;
        }

        // 1=Operação interna;
        // 2=Operação interestadual;
        // 3=Operação com exterior.
        if ($notaFiscal->getPessoaDestinatario()) {

            if ($notaFiscal->getPessoaDestinatario()->getTipoPessoa() == 'PESSOA_JURIDICA') {
                $nfe->infNFe->dest->CNPJ = $notaFiscal->getPessoaDestinatario()->getDocumento();
            } else {
                $nfe->infNFe->dest->CPF = $notaFiscal->getPessoaDestinatario()->getDocumento();
            }

            if ($notaFiscal->getAmbiente() == 'HOM') {
                $nfe->infNFe->dest->xNome = "NF-E EMITIDA EM AMBIENTE DE HOMOLOGACAO - SEM VALOR FISCAL";
            } else {
                $nfe->infNFe->dest->xNome = trim($notaFiscal->getPessoaDestinatario()->getNome());
            }

            $this->pessoaBusiness->fillTransients($notaFiscal->getPessoaDestinatario());

            if ($notaFiscal->getTipoNotaFiscal() == 'NFE' and $notaFiscal->getPessoaDestinatario()->getEndereco()) {
                $ufDestinatario = $notaFiscal->getPessoaDestinatario()
                    ->getEndereco()
                    ->getEstado();
                if ($ufDestinatario and $ufDestinatario == 'PR') {
                    $idDest = 1;
                } else {
                    $idDest = 2;
                }
                $nfe->infNFe->ide->idDest = $idDest;

                $nfe->infNFe->dest->enderDest->xLgr = trim($notaFiscal->getPessoaDestinatario()
                    ->getEndereco()
                    ->getLogradouro());
                $nfe->infNFe->dest->enderDest->nro = $notaFiscal->getPessoaDestinatario()
                    ->getEndereco()
                    ->getNumero();
                $nfe->infNFe->dest->enderDest->xBairro = $notaFiscal->getPessoaDestinatario()
                    ->getEndereco()
                    ->getBairro();
                $municipio = $this->doctrine->getRepository(Municipio::class)->findByNomeAndUf($notaFiscal->getPessoaDestinatario()
                    ->getEndereco()
                    ->getCidade(), $notaFiscal->getPessoaDestinatario()
                    ->getEndereco()
                    ->getEstado());
                $nfe->infNFe->dest->enderDest->cMun = $municipio->getMunicipioCodigo();
                $nfe->infNFe->dest->enderDest->xMun = $notaFiscal->getPessoaDestinatario()
                    ->getEndereco()
                    ->getCidade();
                $nfe->infNFe->dest->enderDest->UF = $notaFiscal->getPessoaDestinatario()
                    ->getEndereco()
                    ->getEstado();
                $nfe->infNFe->dest->enderDest->CEP = preg_replace("/[^0-9]/", "", $notaFiscal->getPessoaDestinatario()
                    ->getEndereco()
                    ->getCep());
                $nfe->infNFe->dest->enderDest->cPais = 1058;
                $nfe->infNFe->dest->enderDest->xPais = 'BRASIL';
                $nfe->infNFe->dest->enderDest->fone = preg_replace("/[^0-9]/", "", $notaFiscal->getPessoaDestinatario()->getFone1());
            }


            // 1=Contribuinte ICMS (informar a IE do destinatário);
            // 2=Contribuinte isento de Inscrição no cadastro de Contribuintes do ICMS;
            // 9=Não Contribuinte, que pode ou não possuir Inscrição Estadual no Cadastro de Contribuintes do ICMS.
            // Nota 1: No caso de NFC-e informar indIEDest=9 e não informar a tag IE do destinatário;
            // Nota 2: No caso de operação com o Exterior informar indIEDest=9 e não informar a tag IE do destinatário;
            // Nota 3: No caso de Contribuinte Isento de Inscrição (indIEDest=2), não informar a tag IE do destinatário.

            if ($notaFiscal->getTipoNotaFiscal() == 'NFCE') {
                $nfe->infNFe->dest->indIEDest = 9;
                unset($nfe->infNFe->transp);
                unset($nfe->infNFe->dest->IE);
            } else {
                if ($notaFiscal->getPessoaDestinatario() and $notaFiscal->getPessoaDestinatario()->getInscricaoEstadual() == 'ISENTO' or !$notaFiscal->getPessoaDestinatario()->getInscricaoEstadual()) {
                    unset($nfe->infNFe->dest->IE);
                    $nfe->infNFe->dest->indIEDest = 2;
                } else {
                    $nfe->infNFe->dest->indIEDest = 1;
                    if ($notaFiscal->getPessoaDestinatario()->getInscricaoEstadual()) {
                        $nfe->infNFe->dest->IE = $notaFiscal->getPessoaDestinatario()->getInscricaoEstadual();
                    } else {
                        unset($nfe->infNFe->dest->IE);
                    }
                }
            }
        } else {
            unset($nfe->infNFe->dest);
        }

        // 0=Sem geração de DANFE;
        // 1=DANFE normal, Retrato;
        // 2=DANFE normal, Paisagem;
        // 3=DANFE Simplificado;
        // 4=DANFE NFC-e;
        // 5=DANFE NFC-e em mensagem eletrônica (o envio de mensagem eletrônica pode ser feita de forma simultânea com a impressão do DANFE; usar o tpImp=5 quando esta for a única forma de disponibilização do DANFE).

        if ($notaFiscal->getTipoNotaFiscal() == 'NFCE') {
            $nfe->infNFe->ide->tpImp = 4;
        } else {
            $nfe->infNFe->ide->tpImp = 1;
        }

        // 1=Produção
        // 2=Homologação
        if ($notaFiscal->getAmbiente() == 'PROD') {
            $nfe->infNFe->ide->tpAmb = 1;
        } else {
            $nfe->infNFe->ide->tpAmb = 2;
        }

        unset($nfe->infNFe->det);
        $i = 1;
        foreach ($notaFiscal->getItens() as $nfItem) {
            $itemXML = $nfe->infNFe->addChild('det');
            $itemXML['nItem'] = $nfItem->getOrdem();
            $itemXML->prod->cProd = $nfItem->getCodigo();
            $itemXML->prod->cEAN = 'SEM GTIN';

            if ($notaFiscal->getAmbiente() == 'HOM' and $i == 1) {
                $xProd = 'NOTA FISCAL EMITIDA EM AMBIENTE DE HOMOLOGACAO - SEM VALOR FISCAL';
            } else {
                $xProd = $nfItem->getDescricao();
            }

            $itemXML->prod->xProd = $xProd;
            $itemXML->prod->NCM = $nfItem->getNcm();
            $itemXML->prod->CFOP = $nfItem->getCfop();
            $itemXML->prod->uCom = $nfItem->getUnidade();
            $itemXML->prod->qCom = $nfItem->getQtde();
            $itemXML->prod->vUnCom = $nfItem->getValorUnit();
            $itemXML->prod->vProd = number_format($nfItem->getValorTotal(), 2, '.', '');
            $itemXML->prod->cEANTrib = 'SEM GTIN';
            $itemXML->prod->uTrib = $nfItem->getUnidade();
            $itemXML->prod->qTrib = $nfItem->getQtde();
            $itemXML->prod->vUnTrib = number_format($nfItem->getValorUnit(), 2, '.', '');
            if (bccomp($nfItem->getValorDesconto(), 0.00, 2)) {
                $itemXML->prod->vDesc = number_format(abs($nfItem->getValorDesconto()), 2, '.', '');
            }
            $itemXML->prod->indTot = '1';

            $itemXML->imposto->ICMS->ICMSSN102->orig = '0';
            $itemXML->imposto->ICMS->ICMSSN102->CSOSN = 103;
            $itemXML->imposto->PIS->PISNT->CST = '07';
            $itemXML->imposto->COFINS->COFINSNT->CST = '07';
            $i++;
        }
        $nfe->infNFe->addChild('total');
        $nfe->infNFe->total->ICMSTot->vBC = '0.00';
        $nfe->infNFe->total->ICMSTot->vICMS = '0.00';
        $nfe->infNFe->total->ICMSTot->vICMSDeson = '0.00';
        $nfe->infNFe->total->ICMSTot->vFCP = '0.00';
        $nfe->infNFe->total->ICMSTot->vBCST = '0.00';
        $nfe->infNFe->total->ICMSTot->vST = '0.00';
        $nfe->infNFe->total->ICMSTot->vFCPST = '0.00';
        $nfe->infNFe->total->ICMSTot->vFCPSTRet = '0.00';
        $nfe->infNFe->total->ICMSTot->vProd = number_format($notaFiscal->getSubtotal(), 2, '.', '');
        $nfe->infNFe->total->ICMSTot->vFrete = '0.00';
        $nfe->infNFe->total->ICMSTot->vSeg = '0.00';
        // if (bccomp($notaFiscal->getTotalDescontos(), 0.00, 2)) {
        $nfe->infNFe->total->ICMSTot->vDesc = number_format(abs($notaFiscal->getTotalDescontos()), 2, '.', '');
        // }
        $nfe->infNFe->total->ICMSTot->vII = '0.00';
        $nfe->infNFe->total->ICMSTot->vIPI = '0.00';
        $nfe->infNFe->total->ICMSTot->vIPIDevol = '0.00';
        $nfe->infNFe->total->ICMSTot->vPIS = '0.00';
        $nfe->infNFe->total->ICMSTot->vCOFINS = '0.00';
        $nfe->infNFe->total->ICMSTot->vOutro = '0.00';
        $nfe->infNFe->total->ICMSTot->vNF = number_format($notaFiscal->getValorTotal(), 2, '.', '');
        $nfe->infNFe->total->ICMSTot->vTotTrib = '0.00';


        if ($notaFiscal->getTipoNotaFiscal() == 'NFCE') {
            $nfe->infNFe->transp->modFrete = 9;

        } else {
            $nfe->infNFe->transp->modFrete = ModalidadeFrete::get($notaFiscal->getTranspModalidadeFrete())['codigo'];

            if ($notaFiscal->getTranspFornecedor()) {
                $nfe->infNFe->transp->transporta->CNPJ = $notaFiscal->getTranspFornecedor()->getPessoa()->getDocumento();
                $nfe->infNFe->transp->transporta->xNome = trim($notaFiscal->getTranspFornecedor()->getPessoa()->getNome());
                if ($notaFiscal->getTranspFornecedor()->getPessoa()->getInscricaoEstadual()) {
                    $nfe->infNFe->transp->transporta->IE = $notaFiscal->getTranspFornecedor()->getPessoa()->getInscricaoEstadual();
                }

                // FIXME: depois que arrumar a bagunça com 'bon_pessoa', trocar aqui
                $this->pessoaBusiness->fillTransients($notaFiscal->getTranspFornecedor()->getPessoa());

                $nfe->infNFe->transp->transporta->xEnder = substr($notaFiscal->getTranspFornecedor()->getPessoa()->getEndereco()->getEnderecoCompleto(), 0, 60);
                $nfe->infNFe->transp->transporta->xMun = $notaFiscal->getTranspFornecedor()->getPessoa()->getEndereco()->getCidade();
                $nfe->infNFe->transp->transporta->UF = $notaFiscal->getTranspFornecedor()->getPessoa()->getEndereco()->getEstado();

                $nfe->infNFe->transp->vol->qVol = number_format($notaFiscal->getTranspQtdeVolumes(), 0);
                $nfe->infNFe->transp->vol->esp = $notaFiscal->getTranspEspecieVolumes();
                if ($notaFiscal->getTranspMarcaVolumes()) {
                    $nfe->infNFe->transp->vol->marca = $notaFiscal->getTranspMarcaVolumes();
                }
                if ($notaFiscal->getTranspNumeracaoVolumes()) {
                    $nfe->infNFe->transp->vol->nVol = $notaFiscal->getTranspNumeracaoVolumes();
                }

                $nfe->infNFe->transp->vol->pesoL = number_format($notaFiscal->getTranspPesoLiquido(), 3, '.', '');
                $nfe->infNFe->transp->vol->pesoB = number_format($notaFiscal->getTranspPesoBruto(), 3, '.', '');

            }
        }

        if ($finNFe == 3 or $finNFe == 4) {
            $nfe->infNFe->pag->detPag->tPag = '90';
            $nfe->infNFe->pag->detPag->vPag = '0.00';
        } else {
            $nfe->infNFe->pag->detPag->tPag = '01';
            $nfe->infNFe->pag->detPag->vPag = number_format($notaFiscal->getValorTotal(), 2, '.', '');
        }


        if ($notaFiscal->getInfoCompl()) {
            $infoCompl = preg_replace("/\r/", "", $notaFiscal->getInfoCompl());
            $infoCompl = preg_replace("/\n/", ";", $infoCompl);
            $nfe->infNFe->infAdic->infCpl = $infoCompl;
        }


        $pastaUnimake = getenv('FISCAL_UNIMAKE_PASTAROOT');

        // Número randômico para que não aconteça de pegar XML de retorno de tentativas de faturamento anteriores
        $rand = rand(10000000, 99999999);
        $notaFiscal->setRandFaturam($rand);

        // file_put_contents("d:/NFE/" . $notaFiscal->getUuid() . "-nfe.xml", $nfe->asXML());
        file_put_contents($pastaUnimake . "/envio/" . $notaFiscal->getUuid() . "-" . $rand . "-nfe.xml", $nfe->asXML());

        $notaFiscal->setSpartacusStatus(-100);
        $notaFiscal->setSpartacusMensretornoReceita("AGUARDANDO FATURAMENTO");

        $this->notaFiscalEntityHandler->persist($notaFiscal);
        $this->doctrine->getManager()->flush();


        $notaFiscal = $this->consultarRetorno($notaFiscal, $rand);
        if ($notaFiscal->getSpartacusStatus() == 100) {
            $this->imprimir($notaFiscal);
        }
        return $notaFiscal;
    }

    /**
     * Verifica nos arquivos de retorno quais os status.
     *
     * @param NotaFiscal $notaFiscal
     * @param $rand
     * @return \App\Entity\Fiscal\NotaFiscal
     * @throws \Exception
     */
    public function consultarRetorno(NotaFiscal $notaFiscal, $rand)
    {
        $id = $notaFiscal->getId();
        if (!$id)
            return;

        $uuid = $notaFiscal->getUuid();

        $pastaUnimake = getenv('FISCAL_UNIMAKE_PASTAROOT');

        $pastaRetorno = $pastaUnimake . '/retorno/';

        // pega o número do lote do arquivo $notaFiscal->getUuid() . "-num-lot.xml"
        $arquivoNumLot = $pastaRetorno . $uuid . '-' . $rand . '-num-lot.xml';

        $arquivoErr = $pastaRetorno . $uuid . '-' . $rand . '-nfe.err';

        $count = 20;


        while (true) {
            if (!file_exists($arquivoNumLot) and !file_exists($arquivoErr)) {
                sleep(1);
                $count--;
                if ($count <= 0) {
                    break;
                }
            } else {

                if (file_exists($arquivoNumLot)) {
                    // pega o arquivo com lote: 000000000[lote]-rec.xml
                    $xmlNumLot = simplexml_load_string(file_get_contents($arquivoNumLot));
                    $numLot = str_pad($xmlNumLot->NumeroLoteGerado, 15, '0', STR_PAD_LEFT);

                    // retEnviNFe->infRec->nRec
                    $arquivoRec = $pastaRetorno . $numLot . '-rec.xml';
                    if (!file_exists($arquivoRec)) {
                        sleep(1);
                        $count--;
                        if ($count <= 0) {
                            break;
                        }
                    } else {
                        $xmlRec = simplexml_load_string(file_get_contents($arquivoRec));

                        if ($xmlRec->cStat == 103) {
                            $nRec = $xmlRec->infRec->nRec;
                            // pega o arquivo com [nRec]-pro-rec.xml
                            $arquivoProRec = $pastaRetorno . $nRec . "-pro-rec.xml";

                            if (!file_exists($arquivoProRec)) {
                                sleep(1);
                                $count--;
                                if ($count <= 0) {
                                    break;
                                }
                            } else {
                                $xmlProRec = simplexml_load_string(file_get_contents($arquivoProRec));

                                $cStat = $xmlProRec->protNFe->infProt->cStat->__toString();
                                $xMotivo = $xmlProRec->protNFe->infProt->xMotivo->__toString();
                                $nProt = $xmlProRec->protNFe->infProt->nProt->__toString();

                                $notaFiscal->setSpartacusStatus($cStat);
                                $notaFiscal->setSpartacusMensretornoReceita($xMotivo);
                                $notaFiscal->setProtocoloAutorizacao($nProt);
                                $notaFiscal->setDtSpartacusStatus(new \DateTime());
                                $this->notaFiscalEntityHandler->persist($notaFiscal);
                                $this->doctrine->getManager()->flush();
                                break;
                            }
                        }
                    }
                } else if (file_exists($arquivoErr)) {
                    $err = file($arquivoErr);
                    $message = explode('|', $err[2])[1];

                    $notaFiscal->setSpartacusStatus(0);
                    $notaFiscal->setSpartacusMensretornoReceita($message);
                    $this->notaFiscalEntityHandler->persist($notaFiscal);
                    $this->doctrine->getManager()->flush();
                    return $notaFiscal;
                } else {
                    throw new \Exception("Nem o arquivo de sucesso, nem o de erro, foram encontrados.");
                }

            }
        }
        return $notaFiscal;
    }

    /**
     * Faz uma consulta ao status da NotaFiscal na receita.
     *
     * @param NotaFiscal $notaFiscal
     * @return NotaFiscal
     * @throws \Exception
     */
    public function consultarStatus(NotaFiscal $notaFiscal)
    {
        $pastaXMLExemplos = getenv('PASTAARQUIVOSXMLEXEMPLO');

        $exemplo = file_get_contents($pastaXMLExemplos . "/-ped-sit4.xml");
        $pedSit = simplexml_load_string($exemplo);

        $pedSit->tpAmb = $notaFiscal->getAmbiente() == 'PROD' ? '1' : '2';
        $pedSit->chNFe = $notaFiscal->getChaveAcesso();

        // número randômico para casos onde várias consultas possam ser feitas
        $rand = rand(10000000, 99999999);

        $pastaUnimake = getenv('FISCAL_UNIMAKE_PASTAROOT');
        file_put_contents($pastaUnimake . "/envio/" . $notaFiscal->getUuid() . "-CONS-SIT-" . $rand . "-nfe.xml", $pedSit->asXML());

        $count = 20;
        $arqRetorno = $pastaUnimake . "/retorno/" . $notaFiscal->getUuid() . "-CONS-SIT-" . $rand . "-sit.xml";
        while (true) {
            if (!file_exists($arqRetorno)) {
                sleep(1);
                $count--;
                if ($count <= 0) {
                    throw new \Exception('Erro ao consultar status da Nota Fiscal. (id = [' . $notaFiscal->getId() . ']');
                }
            } else {
                $retorno = simplexml_load_string(file_get_contents($arqRetorno));

                $notaFiscal->setSpartacusStatus($retorno->cStat->__toString());
                $notaFiscal->setSpartacusMensretornoReceita($retorno->xMotivo->__toString());

                if ($retorno->protNFe and $retorno->protNFe->infProt and $retorno->protNFe->infProt->nProt) {
                    $notaFiscal->setProtocoloAutorizacao($retorno->protNFe->infProt->nProt->__toString());
                }

                $this->notaFiscalEntityHandler->persist($notaFiscal);
                $this->doctrine->getManager()->flush();
                break;
            }
        }

        return $notaFiscal;
    }

    public function imprimir(NotaFiscal $notaFiscal)
    {
        // Z:\enviado\Autorizados\201808
        $id = $notaFiscal->getId();
        if (!$id)
            return;

        $uuid = $notaFiscal->getUuid();

        $pastaUnimake = getenv('FISCAL_UNIMAKE_PASTAROOT');

        $pastaAutorizados = $pastaUnimake . '/enviado/Autorizados/' . $notaFiscal->getDtEmissao()->format('Ym') . '/';
        $pastaReimpressao = $pastaUnimake . '/reimpressao/';

        $arquivoProcNFe = $pastaAutorizados . $uuid . '-' . $notaFiscal->getRandFaturam() . '-procNFe.xml';
        $arquivoReimpressao = $pastaReimpressao . $uuid . '-' . $notaFiscal->getRandFaturam() . '-procNFe.xml';
        if (file_exists($arquivoProcNFe)) {
            copy($arquivoProcNFe, $arquivoReimpressao);
        } else {
            throw new \Exception("Arquivo não encontrado para reimpressão: " . $arquivoProcNFe);
        }
    }

    public function imprimirCancelamento(NotaFiscal $notaFiscal)
    {
        // \enviado\Autorizados\201808
        $id = $notaFiscal->getId();
        if (!$id)
            return;

        // 41180877498442000134650040000000701344865736_110111_01-procEventoNFe
        $chaveNota = $notaFiscal->getChaveAcesso();
        $tpEvento = '110111';
        $nSeqEvento = '01';

        $nomeArquivo = $chaveNota . "_" . $tpEvento . "_" . $nSeqEvento . "-procEventoNFe.xml";

        $pastaUnimake = getenv('FISCAL_UNIMAKE_PASTAROOT');

        $pastaAutorizados = $pastaUnimake . '/enviado/Autorizados/' . $notaFiscal->getDtEmissao()->format('Ym') . '/';

        if (file_exists($pastaAutorizados . $nomeArquivo)) {
            copy($pastaAutorizados . $nomeArquivo, $pastaUnimake . '/reimpressao/' . $nomeArquivo);
        }
    }

    public function imprimirCartaCorrecao(NotaFiscal $notaFiscal)
    {
        // \enviado\Autorizados\201808
        $id = $notaFiscal->getId();
        if (!$id)
            return;

        // Estranho que aqui o Unimake não coloca o tpEvento no nome do arquivo. No cancelamento ele coloca.

        $chaveNota = $notaFiscal->getChaveAcesso();
        // $tpEvento = '110110';
        $nSeqEvento = $notaFiscal->getCartaCorrecaoSeq();

//        $nomeArquivo = $chaveNota . "_" . $tpEvento . "_" . str_pad($nSeqEvento,2,'0',STR_PAD_LEFT) . "-procEventoNFe.xml";
        $nomeArquivo = $chaveNota . "_" . str_pad($nSeqEvento, 2, '0', STR_PAD_LEFT) . "-procEventoNFe.xml";

        $pastaUnimake = getenv('FISCAL_UNIMAKE_PASTAROOT');

        $pastaAutorizados = $pastaUnimake . '/enviado/Autorizados/' . $notaFiscal->getDtEmissao()->format('Ym') . '/';

        if (file_exists($pastaAutorizados . $nomeArquivo)) {
            copy($pastaAutorizados . $nomeArquivo, $pastaUnimake . '/reimpressao/' . $nomeArquivo);
        }
    }

    public function cancelar(NotaFiscal $notaFiscal)
    {
        $notaFiscal = $this->verificaSePrecisaConsultarStatus($notaFiscal);

        if ($notaFiscal->getSpartacusStatus() != "100" and $notaFiscal->getSpartacusStatus() != "204") {
            throw new \Exception("Nota Fiscal com status diferente de '100' ou de '204' não pode ser cancelada. (id: " . $notaFiscal->getId() . ")");
        }

        $pastaXMLExemplos = getenv('PASTAARQUIVOSXMLEXEMPLO');

        $exemploNFe = file_get_contents($pastaXMLExemplos . "/-ped-canc4.xml");
        $pedCanc = simplexml_load_string($exemploNFe);

        // Identificador da TAG a ser assinada, a regra de formação do Id é: “ID” + tpEvento + chave da NF-e + nSeqEvento
        // ID1101113511031029073900013955001000000001105112804102

        $tpEvento = '110111';
        $chaveNota = $notaFiscal->getChaveAcesso();
        $nSeqEvento = '01';

        $id = "ID" . $tpEvento . $chaveNota . $nSeqEvento;

        // número randômico para casos onde várias consultas possam ser feitas
        $rand = rand(10000000, 99999999);

        $pedCanc->idLote = $rand;
        $pedCanc->evento->infEvento['Id'] = $id;
        $pedCanc->evento->infEvento->cOrgao = '41'; // TODO: substituir aqui pela busca do pessoaEmitente->estado->getCodigoIBGE()
        $pedCanc->evento->infEvento->tpAmb = $notaFiscal->getAmbiente() == 'PROD' ? '1' : '2';
        $pedCanc->evento->infEvento->CNPJ = $notaFiscal->getPessoaEmitente()->getDocumento();
        $pedCanc->evento->infEvento->chNFe = $chaveNota;
        $pedCanc->evento->infEvento->dhEvento = (new \DateTime('now', new \DateTimeZone('America/Sao_Paulo')))->format('Y-m-d\TH:i:s\-03:00');
        $pedCanc->evento->infEvento->tpEvento = '110111';
        $pedCanc->evento->infEvento->detEvento->xJust = $notaFiscal->getMotivoCancelamento();
        $pedCanc->evento->infEvento->detEvento->nProt = $notaFiscal->getProtocoloAutorizacao();

        $pastaUnimake = getenv('FISCAL_UNIMAKE_PASTAROOT');
        file_put_contents($pastaUnimake . "/envio/" . $notaFiscal->getUuid() . "-CANCELAR-" . $rand . "-nfe.xml", $pedCanc->asXML());

        $count = 20;
        $arqRetornoSucesso = $pastaUnimake . "/retorno/" . $notaFiscal->getUuid() . "-CANCELAR-" . $rand . "-ret-env-canc.xml";
        $arqRetornoErro = $pastaUnimake . "/retorno/" . $notaFiscal->getUuid() . "-CANCELAR-" . $rand . "-ret-env-canc.err";
        while (true) {
            if (!file_exists($arqRetornoSucesso) and !file_exists($arqRetornoErro)) {
                sleep(1);
                $count--;
                if ($count <= 0) {
                    throw new \Exception('Erro ao cancelar a Nota Fiscal. (id = [' . $notaFiscal->getId() . ']');
                }
            } else {
                if (file_exists($arqRetornoSucesso)) {
                    $retorno = simplexml_load_string(file_get_contents($arqRetornoSucesso));

                    $notaFiscal->setSpartacusStatus($retorno->retEvento->infEvento->cStat->__toString());
                    $notaFiscal->setSpartacusMensretornoReceita($retorno->retEvento->infEvento->xMotivo->__toString());

                    $this->notaFiscalEntityHandler->persist($notaFiscal);
                    $this->doctrine->getManager()->flush();
                    break;
                } else if (file_exists($arqRetornoErro)) {
                    $err = file($arqRetornoErro);
                    $message = explode('|', $err[2])[1];

                    $notaFiscal->setSpartacusStatus(0);
                    $notaFiscal->setSpartacusMensretornoReceita($message);
                    $this->notaFiscalEntityHandler->persist($notaFiscal);
                    $this->doctrine->getManager()->flush();
                    return $notaFiscal;
                }
            }
        }

        return $notaFiscal;
    }

    public function cartaCorrecao(NotaFiscal $notaFiscal)
    {
        $notaFiscal = $this->verificaSePrecisaConsultarStatus($notaFiscal);


        $pastaXMLExemplos = getenv('PASTAARQUIVOSXMLEXEMPLO');

        $exemploNFe = file_get_contents($pastaXMLExemplos . "/-cce4.xml");
        $cartaCorr = simplexml_load_string($exemploNFe);

        // Identificador da TAG a ser assinada, a regra de formação do Id é: “ID” + tpEvento + chave da NF-e + nSeqEvento
        // ID1101113511031029073900013955001000000001105112804102

        $tpEvento = '110110';
        $chaveNota = $notaFiscal->getChaveAcesso();
        // FIXME: não é a melhor forma, mas por enquanto vai assim mesmo.
        $nSeqEvento = $notaFiscal->getCartaCorrecaoSeq() ? $notaFiscal->getCartaCorrecaoSeq() : 0;
        $nSeqEvento++;
        $notaFiscal->setCartaCorrecaoSeq($nSeqEvento);


        $id = "ID" . $tpEvento . $chaveNota . str_pad($nSeqEvento, 2, "0", STR_PAD_LEFT);

        // número randômico para casos onde várias consultas possam ser feitas
        $rand = rand(100000000000000, 999999999999999);


        $cartaCorr->idLote = $rand;
        $cartaCorr->evento->infEvento['Id'] = $id;
        $cartaCorr->evento->infEvento->cOrgao = '41'; // TODO: substituir aqui pela busca do pessoaEmitente->estado->getCodigoIBGE()
        $cartaCorr->evento->infEvento->tpAmb = $notaFiscal->getAmbiente() == 'PROD' ? '1' : '2';
        $cartaCorr->evento->infEvento->CNPJ = $notaFiscal->getPessoaEmitente()->getDocumento();
        $cartaCorr->evento->infEvento->chNFe = $chaveNota;
        $cartaCorr->evento->infEvento->dhEvento = (new \DateTime('now', new \DateTimeZone('America/Sao_Paulo')))->format('Y-m-d\TH:i:s\-03:00');
        $cartaCorr->evento->infEvento->tpEvento = '110110';
        $cartaCorr->evento->infEvento->nSeqEvento = $nSeqEvento;

        $cartaCorr->evento->infEvento->detEvento->xCorrecao = $notaFiscal->getCartaCorrecao();

        $pastaUnimake = getenv('FISCAL_UNIMAKE_PASTAROOT');
        file_put_contents($pastaUnimake . "/envio/" . $notaFiscal->getUuid() . "-CARTACORR-" . $rand . "-nfe.xml", $cartaCorr->asXML());

        $count = 20;
        $arqRetornoSucesso = $pastaUnimake . "/retorno/" . $notaFiscal->getUuid() . "-CARTACORR-" . $rand . "-ret-env-cce.xml";
        $arqRetornoErro = $pastaUnimake . "/retorno/" . $notaFiscal->getUuid() . "-CARTACORR-" . $rand . "-ret-env-cce.err";
        while (true) {
            if (!file_exists($arqRetornoSucesso) and !file_exists($arqRetornoErro)) {
                sleep(1);
                $count--;
                if ($count <= 0) {
                    throw new \Exception('Erro ao enviar CARTA DE CORREÇÃO para a Nota Fiscal. (id = [' . $notaFiscal->getId() . ']');
                }
            } else {
                if (file_exists($arqRetornoSucesso)) {
                    $retorno = simplexml_load_string(file_get_contents($arqRetornoSucesso));

                    $notaFiscal->setSpartacusStatus($retorno->retEvento->infEvento->cStat->__toString());
                    $notaFiscal->setSpartacusMensretornoReceita($retorno->retEvento->infEvento->xMotivo->__toString());

                    $this->notaFiscalEntityHandler->persist($notaFiscal);
                    $this->doctrine->getManager()->flush();
                    break;
                } else if (file_exists($arqRetornoErro)) {
                    $err = file($arqRetornoErro);
                    $message = explode('|', $err[2])[1];

                    $notaFiscal->setSpartacusStatus(0);
                    $notaFiscal->setSpartacusMensretornoReceita($message);
                    $this->notaFiscalEntityHandler->persist($notaFiscal);
                    $this->doctrine->getManager()->flush();
                    return $notaFiscal;
                }
            }
        }

        return $notaFiscal;
    }

    public function verificaSePrecisaConsultarStatus(NotaFiscal $notaFiscal)
    {
        if (!$notaFiscal->getSpartacusStatus() or !$notaFiscal->getSpartacusMensretornoReceita() or !$notaFiscal->getProtocoloAutorizacao()) {
            $notaFiscal = $this->consultarStatus($notaFiscal);
        }

        return $notaFiscal;
    }

    public function consultarCNPJ($cnpj) {
        $pastaXMLExemplos = getenv('PASTAARQUIVOSXMLEXEMPLO');

        $exemplo = file_get_contents($pastaXMLExemplos . "/-cons-cad.xml");
        $consCad = simplexml_load_string($exemplo);
        $consCad->infCons->CNPJ = $cnpj;

        $pastaUnimake = getenv('FISCAL_UNIMAKE_PASTAROOT');
        $rand = rand(100000000000000, 999999999999999);
        file_put_contents($pastaUnimake . "/envio/" . $cnpj . "-" . $rand . "-consCad.xml", $consCad->asXML());


        $count = 20;
        $arqRetornoSucesso = $pastaUnimake . "/retorno/" . $cnpj . "-" . $rand . "-consCad.xml-ret-cons-cad.xml";
        while (true) {
            if (!file_exists($arqRetornoSucesso)) {
                sleep(1);
                $count--;
                if ($count <= 0) {
                    throw new \Exception('Erro ao consultar CNPJ (' . $cnpj . ')');
                }
            } else {
                if (file_exists($arqRetornoSucesso)) {
                    $retorno = simplexml_load_string(file_get_contents($arqRetornoSucesso));

                    if ($retorno and isset($retorno->infCons)) {
                        $dados['CNPJ'] = $retorno->infCons->CNPJ->__toString();
                        $dados['IE'] = $retorno->infCons->infCad->IE ? $retorno->infCons->infCad->IE->__toString() : null;
                        $dados['UF'] = $retorno->infCons->infCad->UF ? $retorno->infCons->infCad->UF->__toString() : null;
                        $dados['razaoSocial'] = $retorno->infCons->infCad->xNome ? $retorno->infCons->infCad->xNome->__toString() : null;
                        $dados['nomeFantasia'] = $retorno->infCons->infCad->xFant ? $retorno->infCons->infCad->xFant->__toString() : null;
                        if (isset($retorno->infCons->infCad->ender)) {
                            $dados['endereco']['logradouro'] = $retorno->infCons->infCad->ender->xLgr ? $retorno->infCons->infCad->ender->xLgr->__toString() : null;
                            $dados['endereco']['numero'] = $retorno->infCons->infCad->ender->nro ? $retorno->infCons->infCad->ender->nro->__toString() : null;
                            $dados['endereco']['bairro'] = $retorno->infCons->infCad->ender->xBairro ? $retorno->infCons->infCad->ender->xBairro->__toString() : null;
                            $dados['endereco']['cidade'] = $retorno->infCons->infCad->ender->xMun ? $retorno->infCons->infCad->ender->xMun->__toString() : null;
                            $dados['endereco']['cep'] = $retorno->infCons->infCad->ender->CEP ? $retorno->infCons->infCad->ender->CEP->__toString() : null;
                        }
                        return $dados;
                    }


                    break;
                }
            }
        }
    }
}
    