<?php
namespace App\Business\Vendas;

use App\Entity\Estoque\Grade;
use App\Entity\Estoque\Produto;
use App\Entity\Estoque\ProdutoPreco;
use App\Entity\Fiscal\NCM;
use App\Entity\RH\Funcionario;
use App\Entity\Vendas\PlanoPagto;
use App\Entity\Vendas\TipoVenda;
use App\Entity\Vendas\Venda;
use App\Entity\Vendas\VendaItem;
use Symfony\Bridge\Doctrine\RegistryInterface;
use App\Entity\Fiscal\NotaFiscal;
use App\Entity\Base\Config;
use App\Entity\Fiscal\IndicadorFormaPagto;
use App\Entity\Base\Pessoa;
use App\Entity\Fiscal\NotaFiscalItem;
use App\Entity\Fiscal\NotaFiscalVenda;
use App\Business\Base\EntityIdBusiness;

class VendaBusiness
{

    private $doctrine;
    
    private $entityIdBusiness;

    public function __construct(RegistryInterface $doctrine, EntityIdBusiness $entityIdBusiness)
    {
        $this->doctrine = $doctrine;
        $this->entityIdBusiness = $entityIdBusiness;
    }

    /**
     * Percorre a pasta onde tem os arquivos do EKT e gera as ven_venda correspondentes.
     * Depois renomeia os arquivos para não serem reprocessados.
     */
    public function processarTXTsEKTeApagarArquivos()
    {
        $this->processarTXTsEKT();
        // Depois de processar todos os arquivos, renomeia-os para evitar que seja processados novamente
        $dir = getenv('PASTAARQUIVOSEKTFISCAL');
        $files = scandir($dir);
        foreach ($files as $file) {
            
            if (! (substr($file, 0, 2) == 'pv' and substr($file, 0, - 3) == 'txt')) {
                continue;
            }
            
            rename($file, $file . '.processado');
        }
    }

    /**
     * Transforma o arquivo EKT em uma $venda.
     *
     * @return \App\Entity\Vendas\Venda
     */
    private function processarTXTsEKT()
    {
        $dir = getenv('PASTAARQUIVOSEKTFISCAL');
        $files = scandir($dir);
        
        foreach ($files as $file) {
            
            if (! (substr($file, 0, 2) == 'pv' and substr($file, - 3) == 'txt')) {
                continue;
            }
            
            // return name.startsWith("pv") && name.endsWith("txt");
            
            $linhas = file($dir . '/' . $file, FILE_IGNORE_NEW_LINES);
            
            $cabecalho = explode("|", $linhas[0]);
            
            $pv = $cabecalho[1];
            
            $dtVenda = \DateTime::createFromFormat('d/m/Y', $cabecalho[7]);
            
            $vendaRepo = $this->doctrine->getRepository(Venda::class);
            $venda = $vendaRepo->findByDtVendaAndPV($dtVenda, $pv);
            
            if (! $venda) {
                $venda = new Venda();
                $venda->setPv($pv);
                $venda->setDtVenda($dtVenda);
                $venda->setTipoVenda($this->doctrine->getRepository(TipoVenda::class)
                    ->find(1));
                $planoPagto = $this->doctrine->getRepository(PlanoPagto::class)->findByDescricao($cabecalho[3]);
                if (! $planoPagto) {
                    $planoPagto = $this->doctrine->getRepository(PlanoPagto::class)->find(1);
                }
                $venda->setPlanoPagto($planoPagto);
                
                $venda->setVendedor($this->doctrine->getRepository(Funcionario::class)
                    ->findByCodigo($cabecalho[2]));
                
                $venda->setDeletado(false);
                
                $venda->setDescontoPlano($cabecalho[6]);
                $venda->setValorTotal($cabecalho[5]);
                $venda->setSubTotal(abs($venda->getValorTotal()) - abs($venda->getDescontoPlano()));
                $venda->setDescontoEspecial(0.0);
                
                $gradeTamanhoUN = $this->doctrine->getRepository(Grade::class)->findByGradeCodigoAndTamanho(11, 'UN');
                
                for ($i = 1; $i < count($linhas); $i ++) {
                    $linhaItem = explode('|', $linhas[$i]);
                    $ordem = $linhaItem[1];
                    $reduzido = $linhaItem[2];
                    $descricao = $linhaItem[3];
                    
                    $gradeCodigo = $linhaItem[8];
                    $tamanho = trim($linhaItem[7]);
                    
                    $ncm = $linhaItem[4];
                    
                    $vendaItem = new VendaItem();
                    $venda->addItem($vendaItem);
                    $vendaItem->setVenda($venda);
                    $vendaItem->setNcm($ncm);
                    
                    $vendaItem->setNcmExistente($this->doctrine->getRepository(NCM::class)
                        ->findByNCM($ncm));
                    
                    $vendaItem->setOrdem($ordem);
                    
                    $vendaItem->setQtde($linhaItem[5]);
                    $vendaItem->setPrecoVenda($linhaItem[6]);
                    
                    if ($reduzido == '88888') {
                        $vendaItem->setGradeTamanho(null);
                        $vendaItem->setNcReduzido($reduzido);
                        $vendaItem->setNcDescricao(trim($descricao) == '' ? '88888' : $descricao);
                        
                        $vendaItem->setNcGradeTamanho($tamanho);
                        $vendaItem->setNcm("62179000"); // Vestuário e seus acessórios, exceto de malha
                    } else {
                        
                        $produto = $this->doctrine->getRepository(Produto::class)->findByReduzidoEktAndDtVenda($reduzido, $dtVenda);
                        if (! $produto) {
                            throw new \Exception("Produto não encontrado para [" . $reduzido . "] [" . $dtVenda->format('Ym') . "]");
                        }
                        $vendaItem->setProduto($produto);
                        
                        $gradeTamanho = $this->doctrine->getRepository(Grade::class)->findByGradeCodigoAndTamanho($gradeCodigo, $tamanho);
                        
                        $vendaItem->setGradeTamanho($gradeTamanho ? $gradeTamanho : $gradeTamanhoUN);
                        
                        $precoAtual = $this->doctrine->getRepository(ProdutoPreco::class)->findPrecoEmDataVenda($produto, $dtVenda);
                        
                        $vendaItem->setAlteracaoPreco($precoAtual->getPrecoPrazo() != $vendaItem->getPrecoVenda());
                    }
                }
            }
            
            $venda->setStatus('PREVENDA');
            
            $entityManager = $this->doctrine->getManager();
            $entityManager->persist($venda);
            $entityManager->flush();
            
            return $venda;
        }
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
        $this->doctrine->getManager()->beginTransaction();
        
        $notaFiscal = $this->doctrine->getRepository(NotaFiscalVenda::class)->findNotaFiscalByVenda($venda);
        
        if ($notaFiscal) {
            $editando = true;
        } else {
            $notaFiscal = new NotaFiscal();
        }
        
        if (! $editando) {
            // Aqui somente coisas que fazem sentido serem alteradas depois de já ter sido (provavelmente) tentado o faturamento da Notafiscal.
            $notaFiscal->setTranspModalidadeFrete('SEM_FRETE');
            
            $notaFiscal->setIndicadorFormaPagto($venda->getPlanoPagto()
                ->getCodigo() == '1.00' ? IndicadorFormaPagto::VISTA : IndicadorFormaPagto::PRAZO);
            $ambiente = $this->doctrine->getRepository(Config::class)->findByChave("bonsucesso.fiscal.ambiente");
            if (! $ambiente) {
                throw new \Exception("'bonsucesso.fiscal.ambiente' não informado");
            }
            $notaFiscal->setAmbiente($ambiente);
            
            $chave = "bonsucesso.fiscal." . strtolower($dataNotaFiscal['tipo']) . ".serie";
            $serie = $this->doctrine->getRepository(Config::class)
                ->findByChave($chave)
                ->getValor();
            if (! $serie) {
                throw new \Exception("'" . $chave . "' não informado");
            }
            $notaFiscal->setSerie($serie);
            $nnf = $this->doctrine->getRepository(NotaFiscal::class)->findProxNumFiscal($ambiente == 'PROD', $notaFiscal->getSerie(), $dataNotaFiscal['tipo']);
            $notaFiscal->setNumero($nnf);
            $notaFiscal->setEntrada(false);
            $notaFiscal->setTipoNotaFiscal($dataNotaFiscal['tipo']);
            $emitente = $this->doctrine->getRepository(Pessoa::class)->findByDocumento('77498442000134');
            $notaFiscal->setPessoaEmitente($emitente);
        }
        
        $dtEmissao = new \DateTime();
        $dtEmissao->modify('-4 minutes');
        $notaFiscal->setDtEmissao($dtEmissao);
        
        if ($dataNotaFiscal['pessoa_id']) {
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
            
            $nfItem->setOrdem($ordem ++);
            
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
                $nfItem->setDescricao($vendaItem->getNcDescricao() . " (" + $vendaItem->getNcGradeTamanho() . ")");
            }
            
            $this->entityIdBusiness->handlePersist($nfItem);
            
            $notaFiscal->addItem($nfItem);
        }
        
        $totalDescontos = $venda->getSubTotal() - $venda->getValorTotal();
        $notaFiscal->setSubtotal($venda->getSubTotal());
        $notaFiscal->setValorTotal($venda->getValorTotal());
        $notaFiscal->setTotalDescontos($totalDescontos);
        
        if (abs($totalDescontos) - abs($somaDescontosItens) != 0) {
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
        
        
        $this->doctrine->getManager()->commit();
        return $notaFiscal;
    }

    /**
     *
     * @param Venda $venda
     * @return \App\Entity\Vendas\Venda
     */
    public function finalizarVenda(Venda $venda)
    {
        $venda->setStatus('FINALIZADA');
        $this->doctrine->getManager()->persist($venda);
        $this->doctrine->getManager()->flush();
        return $venda;
    }

    /**
     *
     * @param Venda $venda
     * @return \App\Entity\Vendas\Venda
     */
    public function recalcularTotais(Venda $venda)
    {
        $bdTotalItens = 0.0;
        foreach ($venda->getItens() as $item) {
            $bdTotalItens += $item->getTotalItem();
            $item->setTotalItem($item->getQtde() * $item->getPrecoVenda());
        }
        $totalVenda = $bdTotalItens - abs($venda->getDescontoPlano()) - abs($venda->getDescontoEspecial());
        $venda->setSubTotal($bdTotalItens);
        $venda->setValorTotal($totalVenda);
        
        $this->doctrine->getManager()->persist($venda);
        $this->doctrine->getManager()->flush();
        return $venda;
    }

    /**
     *
     * @param Venda $venda
     * @return boolean
     */
    public function permiteReimpressao(Venda $venda)
    {
        $notaFiscal = $this->doctrine->getRepository(NotaFiscalVenda::class)->findNotaFiscalByVenda($venda);
        
        if ($venda != null and $notaFiscal != null and $notaFiscal->getId() != null) {
            if ($notaFiscal->getSpartacusStatus() == 100 or $notaFiscal->getSpartacusStatus() == 204) {
                return true;
            } else {
                if ($notaFiscal->getSpartacusStatus() == 0 and strpos($notaFiscal->getSpartacusMensRetornoReceita(), 'DUPLICIDADE DE NF') !== FALSE) {
                    return true;
                }
            }
        }
        return false;
    }
}