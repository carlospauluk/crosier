<?php

namespace App\Business\Vendas;

use App\Entity\Estoque\Grade;
use App\Entity\Estoque\Produto;
use App\Entity\Estoque\ProdutoPreco;
use App\Entity\Fiscal\NCM;
use App\Entity\Fiscal\NotaFiscalVenda;
use App\Entity\RH\Funcionario;
use App\Entity\Vendas\PlanoPagto;
use App\Entity\Vendas\TipoVenda;
use App\Entity\Vendas\Venda;
use App\Entity\Vendas\VendaItem;
use App\EntityHandler\Vendas\VendaEntityHandler;
use App\EntityHandler\Vendas\VendaItemEntityHandler;
use Symfony\Bridge\Doctrine\RegistryInterface;

class VendaBusiness
{

    private $doctrine;

    private $vendaEntityHandler;

    private $vendaItemEntityHandler;

    public function __construct(RegistryInterface $doctrine,
                                VendaEntityHandler $vendaEntityHandler,
                                VendaItemEntityHandler $vendaItemEntityHandler)
    {
        $this->doctrine = $doctrine;
        $this->vendaEntityHandler = $vendaEntityHandler;
        $this->vendaItemEntityHandler = $vendaItemEntityHandler;
    }

    public function checkAcessoPVs()
    {
        $dir = getenv('PASTAARQUIVOSEKTFISCAL');
        $files = scandir($dir);
        return array_search('controle.txt', $files) ? true : false;
    }

    /**
     * Percorre a pasta onde tem os arquivos do EKT e gera as ven_venda correspondentes.
     * Depois renomeia os arquivos para não serem reprocessados.
     */
    public
    function processarTXTsEKTeApagarArquivos()
    {
        $this->processarTXTsEKT();
        // Depois de processar todos os arquivos, renomeia-os para evitar que seja processados novamente
        $dir = getenv('PASTAARQUIVOSEKTFISCAL');
        $files = scandir($dir);
        foreach ($files as $file) {

            if (!(substr($file, 0, 2) == 'pv' and substr($file, -3) == 'txt')) {
                continue;
            }

            rename($dir . '/' . $file, $dir . '/' . $file . '.processado');
        }
    }

    /**
     * Transforma o arquivo EKT em uma $venda.
     *
     * @return \App\Entity\Vendas\Venda
     * @return \App\Entity\Vendas\Venda
     * @throws \Exception
     */
    private function processarTXTsEKT()
    {
        $dir = getenv('PASTAARQUIVOSEKTFISCAL');
        $files = scandir($dir);

        foreach ($files as $file) {

            if (!(substr($file, 0, 2) == 'pv' and substr($file, -3) == 'txt')) {
                continue;
            }

            // return name.startsWith("pv") && name.endsWith("txt");

            $linhas = file($dir . '/' . $file, FILE_IGNORE_NEW_LINES);

            $cabecalho = explode("|", $linhas[0]);

            $pv = $cabecalho[1];

            $dtVenda = \DateTime::createFromFormat('d/m/Y', $cabecalho[7]);
            $dtVenda->setTime(0, 0, 0, 0);

            $vendaRepo = $this->doctrine->getRepository(Venda::class);
            $venda = $vendaRepo->findByDtVendaAndPV($dtVenda, $pv);

            if (!$venda) {
                $venda = new Venda();
                $venda->setPv($pv);
                $venda->setDtVenda($dtVenda);

                $venda->setMesano($dtVenda->format('Ym'));

                $venda->setTipoVenda($this->doctrine->getRepository(TipoVenda::class)
                    ->find(1));
                $planoPagto = $this->doctrine->getRepository(PlanoPagto::class)->findByDescricao($cabecalho[3]);
                if (!$planoPagto) {
                    $planoPagto = $this->doctrine->getRepository(PlanoPagto::class)->find(1);
                }
                $venda->setPlanoPagto($planoPagto);

                if (!$cabecalho[2]) {
                    // Algum bug no EKT permite gerar venda com vendedor = 0. Neste caso, ignora.
                    continue;
                }
                $venda->setVendedor($this->doctrine->getRepository(Funcionario::class)
                    ->findByCodigo($cabecalho[2]));

                $venda->setDeletado(false);

                $descontoPlano = (new \NumberFormatter(\Locale::getDefault(), \NumberFormatter::DECIMAL))->parse($cabecalho[6]);
                $venda->setDescontoPlano($descontoPlano);

                $valorTotal = (new \NumberFormatter(\Locale::getDefault(), \NumberFormatter::DECIMAL))->parse($cabecalho[5]);
                $venda->setValorTotal($valorTotal);

                $venda->setSubTotal(abs($venda->getValorTotal()) - abs($venda->getDescontoPlano()));
                $venda->setDescontoEspecial(0.0);

                $gradeTamanhoUN = $this->doctrine->getRepository(Grade::class)->findByGradeCodigoAndTamanho(11, 'UN');

                for ($i = 1; $i < count($linhas); $i++) {
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

                    $qtde = (new \NumberFormatter(\Locale::getDefault(), \NumberFormatter::DECIMAL))->parse($linhaItem[5]);
                    $vendaItem->setQtde($qtde);

                    $precoVenda = (new \NumberFormatter(\Locale::getDefault(), \NumberFormatter::DECIMAL))->parse($linhaItem[6]);
                    $vendaItem->setPrecoVenda($precoVenda);


                    $vendaItem->setNcReduzido($reduzido);
                    $vendaItem->setNcDescricao(trim($descricao) == '' ? '88888' : $descricao);
                    $vendaItem->setNcGradeTamanho($tamanho);

                    if ($reduzido == '88888') {
                        $vendaItem->setGradeTamanho($gradeTamanhoUN);

                        $vendaItem->setNcm("62179000"); // Vestuário e seus acessórios, exceto de malha
                        $vendaItem->setAlteracaoPreco(false);
                    } else {

                        $produto = $this->doctrine->getRepository(Produto::class)->findByReduzidoEktAndDtVenda($reduzido, $dtVenda);
                        if (!$produto) {
                            throw new \Exception("Produto não encontrado para [" . $reduzido . "] [" . $dtVenda->format('Ym') . "]");
                        }
                        $vendaItem->setProduto($produto);

                        $gradeTamanho = $this->doctrine->getRepository(Grade::class)->findByGradeCodigoAndTamanho($gradeCodigo, $tamanho);

                        $vendaItem->setGradeTamanho($gradeTamanho ? $gradeTamanho : $gradeTamanhoUN);

                        $precoAtual = $this->doctrine->getRepository(ProdutoPreco::class)->findPrecoEmDataVenda($produto, $dtVenda);

                        if ($precoAtual and $precoAtual->getPrecoPrazo() != $vendaItem->getPrecoVenda()) {
                            $vendaItem->setAlteracaoPreco(true);
                        } else {
                            $vendaItem->setAlteracaoPreco(false);
                        }
                    }
                }
            }

            $venda->setStatus('PREVENDA');

            $entityManager = $this->doctrine->getEntityManager();
            $this->vendaEntityHandler->persist($venda);
            $entityManager->flush();

        }
    }


    /**
     *
     * @param Venda $venda
     * @return \App\Entity\Vendas\Venda
     */
    public
    function finalizarVenda(Venda $venda)
    {
        $venda->setStatus('FINALIZADA');
        $this->doctrine->getEntityManager()->persist($venda);
        $this->doctrine->getEntityManager()->flush();
        return $venda;
    }

    /**
     *
     * @param Venda $venda
     * @return \App\Entity\Vendas\Venda
     */
    public
    function recalcularTotais(Venda $venda)
    {
        $bdTotalItens = 0.0;
        foreach ($venda->getItens() as $item) {
            $bdTotalItens += $item->getTotalItem();
            $item->setTotalItem($item->getQtde() * $item->getPrecoVenda());
        }
        $totalVenda = $bdTotalItens - abs($venda->getDescontoPlano()) - abs($venda->getDescontoEspecial());
        $venda->setSubTotal($bdTotalItens);
        $venda->setValorTotal($totalVenda);

        $this->doctrine->getEntityManager()->persist($venda);
        $this->doctrine->getEntityManager()->flush();
        return $venda;
    }

    /**
     *
     * @param Venda $venda
     * @return boolean
     * @throws \Exception
     */
    public
    function permiteReimpressao(Venda $venda)
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