<?php
namespace App\Business\Fiscal;

use App\Business\Base\PessoaBusiness;
use App\Entity\Base\Municipio;
use App\Entity\Fiscal\NotaFiscal;
use App\Entity\Fiscal\TipoNotaFiscal;
use Symfony\Bridge\Doctrine\RegistryInterface;
use App\Business\Base\EntityIdBusiness;

/**
 * Classe que trata da integração com o Unimake (UniNfe).
 *
 * @author Carlos Eduardo Pauluk
 *        
 */
class UnimakeBusiness
{

    private $pessoaBusiness;
    
    private $entityIdBusiness;

    private $doctrine;

    public function __construct(RegistryInterface $doctrine, PessoaBusiness $pessoaBusiness, EntityIdBusiness $entityIdBusiness)
    {
        $this->doctrine = $doctrine;
        $this->pessoaBusiness = $pessoaBusiness;
        $this->entityIdBusiness = $entityIdBusiness;
    }

    public function genNFeXML(NotaFiscal $notaFiscal)
    {
        $pastaXMLExemplos = getenv('PASTAARQUIVOSXMLEXEMPLO');
        
        $exemploNFe = file_get_contents($pastaXMLExemplos . "/exemplo-nfe.xml");
        $nfe = simplexml_load_string($exemploNFe);
        
        $nfe->infNFe->ide->nNF = $notaFiscal->getNumero();
        
        $nfe->infNFe->ide->cNF = $notaFiscal->getCnf();
        
        $cUF = "41";
        
        $cnpj = $notaFiscal->getPessoaEmitente()->getDocumento();
        
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
        
        $ano = $notaFiscal->getDtEmissao()->format('y');
        $mes = $notaFiscal->getDtEmissao()->format('m');
        
        $chave = NFeKeys::build($cUF, $ano, $mes, $cnpj, $nfe->infNFe->ide->mod, $nfe->infNFe->ide->serie, $nfe->infNFe->ide->nNF, $nfe->infNFe->ide->tpEmis, $nfe->infNFe->ide->cNF);
        
        $nfe->infNFe['Id'] = "NFe" . $chave;
        $nfe->infNFe->ide->cDV = NFeKeys::verifyingDigit(substr($chave, 0, - 1));
        
        $nfe->infNFe->ide->natOp = $notaFiscal->getNaturezaOperacao();
        
        $nfe->infNFe->ide->dhEmi = $notaFiscal->getDtEmissao()->format('Y-m-d\TH:i:s\-03:00');
        
        if ($notaFiscal->getTipoNotaFiscal() == 55) {
            $nfe->infNFe->ide->dhSaiEnt = $notaFiscal->getDtSaiEnt()->format('Y-m-d\TH:i:s\-03:00');
        } else {
            unset($nfe->infNFe->ide->dhSaiEnt);
            $nfe->infNFe->ide->idDest = 1;
        }
        
        // 1=Operação interna;
        // 2=Operação interestadual;
        // 3=Operação com exterior.
        if ($notaFiscal->getPessoaDestinatario()) {
            
            $this->pessoaBusiness->fillTransients($notaFiscal->getPessoaDestinatario());
            
            $ufDestinatario = $notaFiscal->getPessoaDestinatario()
                ->getEndereco()
                ->getEstado();
            
            if ($ufDestinatario and $ufDestinatario == 'PR') {
                $idDest = 1;
            } else {
                $idDest = 2;
            }
            $nfe->infNFe->ide->idDest = $idDest;
            
            if ($notaFiscal->getPessoaDestinatario()->getTipoPessoa() == 'PESSOA_JURIDICA') {
                $nfe->infNFe->dest->CNPJ = $notaFiscal->getPessoaDestinatario()->getDocumento();
            }
            $nfe->infNFe->dest->xNome = $notaFiscal->getPessoaDestinatario()->getNome();
            
            $nfe->infNFe->dest->enderDest->CEP = $notaFiscal->getPessoaDestinatario()
                ->getEndereco()
                ->getCep();
            $nfe->infNFe->dest->enderDest->fone = $notaFiscal->getPessoaDestinatario()->getFone1();
            $nfe->infNFe->dest->enderDest->nro = $notaFiscal->getPessoaDestinatario()
                ->getEndereco()
                ->getNumero();
            $nfe->infNFe->dest->enderDest->UF = $notaFiscal->getPessoaDestinatario()
                ->getEndereco()
                ->getEstado();
            $nfe->infNFe->dest->enderDest->xBairro = $notaFiscal->getPessoaDestinatario()
                ->getEndereco()
                ->getBairro();
            $nfe->infNFe->dest->enderDest->xLgr = $notaFiscal->getPessoaDestinatario()
                ->getEndereco()
                ->getLogradouro();
            $nfe->infNFe->dest->enderDest->xMun = $notaFiscal->getPessoaDestinatario()
                ->getEndereco()
                ->getCidade();
            
            $municipio = $this->doctrine->getRepository(Municipio::class)->findByNomeAndUf($notaFiscal->getPessoaDestinatario()
                ->getEndereco()
                ->getCidade(), $notaFiscal->getPessoaDestinatario()
                ->getEndereco()
                ->getEstado());
            $nfe->infNFe->dest->enderDest->cMun = $municipio->municipioCodigo;
            
            // 1=Contribuinte ICMS (informar a IE do destinatário);
            // 2=Contribuinte isento de Inscrição no cadastro de Contribuintes do ICMS;
            // 9=Não Contribuinte, que pode ou não possuir Inscrição Estadual no Cadastro de Contribuintes do ICMS.
            // Nota 1: No caso de NFC-e informar indIEDest=9 e não informar a tag IE do destinatário;
            // Nota 2: No caso de operação com o Exterior informar indIEDest=9 e não informar a tag IE do destinatário;
            // Nota 3: No caso de Contribuinte Isento de Inscrição (indIEDest=2), não informar a tag IE do destinatário.
            
            if ($notaFiscal->getTipoNotaFiscal() == 65) {
                $nfe->infNFe->dest->indIEDest = 9;
                unset($nfe->infNFe->transp);
            } else {
                if ($notaFiscal->getPessoaDestinatario() and $notaFiscal->getPessoaDestinatario()->getInscricaoEstadual() == 'ISENTO') {
                    $nfe->infNFe->dest->indIEDest = 2;
                } else {
                    $nfe->infNFe->dest->indIEDest = 1;
                    $nfe->infNFe->dest->IE = $notaFiscal->getPessoaDestinatario()->getInscricaoEstadual();
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
        foreach ($notaFiscal->getItens() as $nfItem) {
            $itemXML = $nfe->infNFe->addChild('det');
            $itemXML['nItem'] = $nfItem->getOrdem();
            $itemXML->prod->cProd = $nfItem->getCodigo();
            $itemXML->prod->cEAN = 'SEM GTIN';
            
            if ($notaFiscal->getAmbiente() == 'HOM') {
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
            if ($nfItem->getValorDesconto()) {
                $itemXML->prod->vDesc = number_format($nfItem->getValorDesconto(), 2, '.', '');
            }
            $itemXML->prod->indTot = '1';
            
            $itemXML->imposto->ICMS->ICMSSN102->orig = '0';
            $itemXML->imposto->ICMS->ICMSSN102->CSOSN = 103;
            $itemXML->imposto->PIS->PISNT->CST = '07';
            $itemXML->imposto->COFINS->COFINSNT->CST = '07';
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
        $nfe->infNFe->total->ICMSTot->vDesc = number_format($notaFiscal->getTotalDescontos(), 2, '.', '');
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
            
            $nfe->infNFe->pag->detPag->tPag = '01';
            $nfe->infNFe->pag->detPag->vPag = number_format($notaFiscal->getValorTotal(), 2, '.', '');
        } else {
            $nfe->infNFe->addChild('pag');
            $nfe->infNFe->addChild('infAdic');
        }
        
        $pastaUnimake = getenv('FISCAL_UNIMAKE_PASTAROOT');
        file_put_contents("d:/NFE/" . $notaFiscal->getUuid() . "-nfe.xml", $nfe->asXML());
        file_put_contents($pastaUnimake . "/envio/" . $notaFiscal->getUuid() . "-nfe.xml", $nfe->asXML());
        
        $notaFiscal->setXmlNota($nfe->asXML());
        
        $this->entityIdBusiness->handlePersist($notaFiscal);
        $this->doctrine->getManager()->persist($notaFiscal);
        $this->doctrine->getManager()->flush();
    }

    /**
     * Verifica nos arquivos de retorno quais os status.
     * 
     * @param NotaFiscal $notaFiscal
     * @return \App\Entity\Fiscal\NotaFiscal
     */
    public function consultarStatus(NotaFiscal $notaFiscal)
    {
        $id = $notaFiscal->getId();
        if (!$id) return;
        
        $uuid = $notaFiscal->getUuid();
        
        $pastaUnimake = getenv('FISCAL_UNIMAKE_PASTAROOT');
        
        $pastaRetorno = $pastaUnimake . '/retorno/';
        
        if (file_exists($pastaRetorno . $uuid . '-nfe.err')) {
            $err = file($pastaRetorno . $uuid . '-nfe.err');
            $message = explode('|', $err[2])[1];
            
            // 'spartacus_mensretorno'
            $notaFiscal->setSpartacusMensretorno($message);
            $this->entityIdBusiness->handlePersist($notaFiscal);
            $this->doctrine->getManager()->persist($notaFiscal);
            $this->doctrine->getManager()->flush();
        } else {
            
            // pega o número do lote do arquivo $notaFiscal->getUuid() . "-num-lot.xml"
            $arquivoNumLot = $pastaRetorno . $uuid . '-num-lot.xml';
            if (file_exists($arquivoNumLot)) {
                // pega o arquivo com lote: 000000000[lote]-rec.xml
                $xmlNumLot = simplexml_load_string(file_get_contents($arquivoNumLot));
                $numLot = str_pad($xmlNumLot->NumeroLoteGerado, 15, '0', STR_PAD_LEFT);
                
                // retEnviNFe->infRec->nRec
                $arquivoRec = $pastaRetorno . $numLot . '-rec.xml';
                if (file_exists($arquivoRec)) {
                    $xmlRec = simplexml_load_string(file_get_contents($arquivoRec));
                    
                    if ($xmlRec->cStat == 103) {
                        $nRec = $xmlRec->infRec->nRec;
                        // pega o arquivo com [nRec]-pro-rec.xml
                        $arquivoProRec = $pastaRetorno . $nRec . "-pro-rec.xml";
                        
                        if (file_exists($arquivoProRec)) {
                            $xmlProRec = simplexml_load_string(file_get_contents($arquivoProRec));
                            
                            $cStat = $xmlProRec->protNFe->infProt->cStat->__toString();
                            $xMotivo = $xmlProRec->protNFe->infProt->xMotivo->__toString();
                            
                            $notaFiscal->setSpartacusStatus($cStat);
                            $notaFiscal->setSpartacusMensretorno($xMotivo);
                            $notaFiscal->setDtSpartacusStatus(new \DateTime());
                            $this->entityIdBusiness->handlePersist($notaFiscal);
                            $this->doctrine->getManager()->persist($notaFiscal);
                            $this->doctrine->getManager()->flush();
                        }
                    }
                }
            }
        }
        return $notaFiscal;
    }
}
    