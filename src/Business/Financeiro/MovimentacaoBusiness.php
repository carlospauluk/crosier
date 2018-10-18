<?php

namespace App\Business\Financeiro;

use App\Entity\Base\DiaUtil;
use App\Entity\Financeiro\Carteira;
use App\Entity\Financeiro\Categoria;
use App\Entity\Financeiro\Grupo;
use App\Entity\Financeiro\Modo;
use App\Entity\Financeiro\Movimentacao;
use App\Entity\Financeiro\OperadoraCartao;
use App\EntityHandler\Financeiro\GrupoEntityHandler;
use App\EntityHandler\Financeiro\MovimentacaoEntityHandler;
use Symfony\Bridge\Doctrine\RegistryInterface;

class MovimentacaoBusiness
{

    private $doctrine;

    private $grupoEntityHandler;

    private $movimentacaoEntityHandler;

    public function __construct(RegistryInterface $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * @return mixed
     */
    public function getGrupoEntityHandler(): GrupoEntityHandler
    {
        return $this->grupoEntityHandler;
    }

    /**
     * @required
     * @param mixed $grupoEntityHandler
     */
    public function setGrupoEntityHandler(GrupoEntityHandler $grupoEntityHandler): void
    {
        $this->grupoEntityHandler = $grupoEntityHandler;
    }

    /**
     * @return mixed
     */
    public function getMovimentacaoEntityHandler(): MovimentacaoEntityHandler
    {
        return $this->movimentacaoEntityHandler;
    }

    /**
     * @required
     * @param mixed $movimentacaoEntityHandler
     */
    public function setMovimentacaoEntityHandler(MovimentacaoEntityHandler $movimentacaoEntityHandler): void
    {
        $this->movimentacaoEntityHandler = $movimentacaoEntityHandler;
    }


    public function somarMovimentacoes($movs)
    {
        $total = 0.0;
        foreach ($movs as $m) {
            $total = $m->getCategoria()->getCodigoSuper() == 1 ? $total + $m->getValorTotal() : $total - $m->getValorTotal();
        }
        return $total;
    }

    /**
     * @param Movimentacao $movimentacao
     * @throws \Doctrine\ORM\ORMException
     */
    public function mergeAll(Movimentacao $movimentacao)
    {
        $em = $this->doctrine->getManager();
        if ($movimentacao->getCategoria() and $movimentacao->getCategoria()->getId()) {
            $movimentacao->setCategoria($em->merge($movimentacao->getCategoria()));
        }
        if ($movimentacao->getCarteira() and $movimentacao->getCarteira()->getId()) {
            $movimentacao->setCarteira($em->merge($movimentacao->getCarteira()));
        }
        if ($movimentacao->getCarteiraDestino() and $movimentacao->getCarteiraDestino()->getId()) {
            $movimentacao->setCarteiraDestino($em->merge($movimentacao->getCarteiraDestino()));
        }
        if ($movimentacao->getCentroCusto() and $movimentacao->getCentroCusto()->getId()) {
            $movimentacao->setCentroCusto($em->merge($movimentacao->getCentroCusto()));
        }
        if ($movimentacao->getModo() and $movimentacao->getModo()->getId()) {
            $movimentacao->setModo($em->merge($movimentacao->getModo()));
        }
        if ($movimentacao->getGrupoItem() and $movimentacao->getGrupoItem()->getId()) {
            $movimentacao->setGrupoItem($em->merge($movimentacao->getGrupoItem()));
        }
        if ($movimentacao->getOperadoraCartao() and $movimentacao->getOperadoraCartao()->getId()) {
            $movimentacao->setOperadoraCartao($em->merge($movimentacao->getOperadoraCartao()));
        }
        if ($movimentacao->getBandeiraCartao() and $movimentacao->getBandeiraCartao()->getId()) {
            $movimentacao->setBandeiraCartao($em->merge($movimentacao->getBandeiraCartao()));
        }
        if ($movimentacao->getPessoa() and $movimentacao->getPessoa()->getId()) {
            $movimentacao->setPessoa($em->merge($movimentacao->getPessoa()));
        }
        if ($movimentacao->getCadeia() and $movimentacao->getCadeia()->getId()) {
            $movimentacao->setCadeia($em->merge($movimentacao->getCadeia()));
        }
        if ($movimentacao->getParcelamento() and $movimentacao->getParcelamento()->getId()) {
            $movimentacao->setParcelamento($em->merge($movimentacao->getParcelamento()));
        }
        if ($movimentacao->getDocumentoBanco() and $movimentacao->getDocumentoBanco()->getId()) {
            $movimentacao->setDocumentoBanco($em->merge($movimentacao->getDocumentoBanco()));
        }
        if ($movimentacao->getChequeBanco() and $movimentacao->getChequeBanco()->getId()) {
            $movimentacao->setChequeBanco($em->merge($movimentacao->getChequeBanco()));
        }
    }

    /**
     * Gera parcelamentos de movimentações.
     */
    public function gerarParcelas(Movimentacao $primeiraParcela, $qtdeParcelas, $diaFixo)
    {
        $parcelamento = $primeiraParcela->getParcelamento();

        $valorTotal = $primeiraParcela->getParcelamento()->getValorTotal();

        if ($valorTotal > 0) {
            $valorParcela = (round(bcdiv($valorTotal, $qtdeParcelas, 4), 2));
        } else {
            $valorParcela = $primeiraParcela->getValorTotal();
        }

        $primeiraParcela->setNumParcela(1);
        $primeiraParcela->setValor($valorParcela);
        $primeiraParcela->setValorTotal($valorParcela);

        $parcelamento->getParcelas()->add($primeiraParcela);

        $dt = $primeiraParcela->getDtVencto();

        $numCheque = null;
        if ($primeiraParcela->getChequeNumCheque() != null) {
            $numCheque = intval($primeiraParcela->getChequeNumCheque());
        }

        // Em casos de grupos de itens...
        $giAtual = $primeiraParcela->getGrupoItem();
        if ($giAtual) {
            if ($giAtual->getProximo() != null) {
                $proximoId = $giAtual->getProximo()->getId();

                $giAtual = $this->doctrine->getRepository(Grupo::class)->find($proximoId); // já "incrementa" pois o for abaixo já começa na segunda parcela...
            } else {
                $giAtual = $this->getGrupoEntityHandler()->gerarProximo($giAtual->getPai());
            }
        }

        $bdValorTotalSoma = $valorParcela;

        for ($i = 1; $i < $qtdeParcelas; $i++) {

            if ($diaFixo) {
                $dt->setDate($dt->format('Y'), $dt->format('m') + 1, $dt->format('d'));
            } else {
                $dt->setDate($dt->format('Y'), $dt->format('m'), $dt->format('d') + 30);
            }

            $p = clone $primeiraParcela;
            $p->setUnqControle(null);

            if ($giAtual) {
                $p->setGrupoItem($giAtual);

                if ($giAtual->getProximo() != null) {
                    $giAtual = $giAtual->getProximo();
                } else {
                    $giAtual = $this->getGrupoEntityHandler()->gerarProximo($giAtual->getPai());
                }
            }

            $p->setParcelamento($parcelamento);
            $p->setNumParcela($i + 1);
            $p->setId(null);
            $p->setDtVencto($dt);
            $p->setDtVenctoEfetiva($this->getEntityManager()->getRepository(DiaUtil::class)->findProximoDiaUtilFinanceiro($dt));
            $p->setValor($valorParcela);
            $p->setValorTotal(null);

            if (is_int($numCheque)) {
                $p->setChequeNumCheque(++$numCheque);
            }

            $bdValorTotalSoma += $valorParcela;

            $parcelamento->getParcelas()->add($p);
        }

        $parcelamento->setValorTotal($bdValorTotalSoma);
    }

    /**
     * Corrige os valores de OperadoraCartao.
     */
    public function corrigirOperadoraCartaoMovimentacoesCartoesDebito(\DateTime $dtPagto, Carteira $carteira)
    {

        $modo = $this->doctrine->getRepository(Modo::class)->findBy(['descricao' => "%RECEB. CARTÃO DÉBITO%"]);

        $c101 = $this->doctrine->getRepository(Categoria::class)->findBy(['codigo' => 101]);
        $c299 = $this->doctrine->getRepository(Categoria::class)->findBy(['codigo' => 299]);
        $c199 = $this->doctrine->getRepository(Categoria::class)->findBy(['codigo' => 199]);


        $m101s = $this->doctrine->getRepository(Movimentacao::class)->findBy(['carteira' => $carteira,
            'dtPagto' => $dtPagto->format('Y-m-d'),
            'modo' => $modo,
            'categoria' => $c101
        ]);

        $results = "";

        foreach ($m101s as $m101) {

            $cadeia = $m101->getCadeia();

            if (!$cadeia) {
                throw new \Exception("Movimentação sem cadeia.");
            } else {
                try {

                    $m299 = $this->doctrine->getRepository(Movimentacao::class)->findOneBy(['cadeia' => $cadeia,
                        'categoria' => $c299
                    ]);

                    $m199 = $this->doctrine->getRepository(Movimentacao::class)->findOneBy(['cadeia' => $cadeia,
                        'categoria' => $c199
                    ]);

                    $operadoraCartao = null;


                    if ($m199->getOperadoraCartao() == null) {
                        $operadoraCartao = $this->doctrine->getRepository(OperadoraCartao::class)->findOneBy(['carteira' => $m199->getCarteira()]);
                        $m199->setOperadoraCartao($operadoraCartao);

                        $m199 = $this->getMovimentacaoEntityHandler()->save($m199);
                        $results[] = "Operadora corrigida para '" . $m199->getDescricao() . "' - R$ " . $m199->getValor() . " (1.99): " . $operadoraCartao->getDescricao();
                    } else {
                        $operadoraCartao = $m199->getOperadoraCartao();
                    }

                    if ($m299->getOperadoraCartao() == null) {
                        // provavelmente TAMBÉM isso não deveria ser necessário, visto que na importação isto já deve ter sido acertado.
                        $m299->setOperadoraCartao($operadoraCartao);
                        $m299 = $this->getMovimentacaoEntityHandler()->save($m299);
                        $results[] = "Operadora corrigida para '" . $m299->getDescricao() . "' - R$ " . $m299->getValor() . " (2.99): " . $operadoraCartao->getDescricao();
                    } else {
//                        getMovimentacaoDataMapper() . detach(m299);
                    }

                    if ($m101->getOperadoraCartao() == null) {
                        // provavelmente isso não deveria ser necessário, visto que na importação isto já deve ter sido acertado.
                        $m101->setOperadoraCartao($operadoraCartao);
                        $m101 = $this->getMovimentacaoEntityHandler()->save($m101);
                        $results[] = "Operadora corrigida para '" . $m101->getDescricao() . "' - R$ " . $m101->getValor() . " (1.01): " . $operadoraCartao->getDescricao();
                    } else {
//                        getMovimentacaoDataMapper() . detach(m101);
                    }

                } catch (\Exception $e) {
                    $results[] = "ERRO: Não foi possível consolidar " . $m101->getDescricao() . " - R$ " . $m101->getValor() . " (" . $e->getMessage() . ")";
                }

            }
        }

        $this->getMovimentacaoEntityHandler()->getEntityManager()->flush();

        return $results;

    }


    /**
     * Consolida as movimentações 101 lançadas manualmente com as 199/299 importadas pelo extrato.
     */
    public function consolidarMovimentacoesCartoesDebito(\DateTime $dtPagto, Carteira $carteira)
    {
        $modo = $this->doctrine->getRepository(Modo::class)->findBy(['descricao' => "%RECEB. CARTÃO DÉBITO%"]);
        $c101 = $this->doctrine->getRepository(Categoria::class)->findBy(['codigo' => 101]);
        $m101s = $this->doctrine->getRepository(Movimentacao::class)->findBy(['carteira' => $carteira,
            'dtPagto' => $dtPagto->format('Y-m-d'),
            'modo' => $modo,
            'categoria' => $c101
        ]);

        $results = [];

        foreach ($m101s as $m101) {
            try {
                if ($m101->getCadeia() == null) {
                    $results[] = $this->consolidarMovimentacaoDebito($m101, $dtPagto, $carteira);
                }
            } catch (\Exception $e) {
                $results[] = "ERRO: Não foi possível consolidar " . $m101->getDescricao() . " - R$ " . $m101->getValor() . " (" . $e->getMessage() . ")";
            }
        }

        return $results;
    }


    /**
     * Faz a consolidação das movimentações de cartão de débito (após as correspondentes terem sido importadas).
     *
     * @param Movimentacao $m101
     * @param \DateTime $dtMoviment
     * @param Carteira $carteira
     * @return string
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function consolidarMovimentacaoDebito(Movimentacao $m101, \DateTime $dtMoviment, Carteira $carteira)
    {

        $result = "";

        $c299 = $this->doctrine->getRepository(Categoria::class)->findBy(['codigo' => 299]);

        // pesquisa movimentação 299 nesta
        // retorna uma lista pois pode encontrar mais de 1

        $m299s = $this->doctrine->getRepository(Movimentacao::class)->findBy([
            'dtMoviment' => $dtMoviment->format('Y-m-d'),
            'valorTotal' => $m101->getValo(),
            'carteira' => $carteira,
            'bandeiraCartao' => $m101->getBandeiraCartao(),
            'categoria' => $c299
        ]);

        // Encontra a m299 que faça parte de uma cadeia com apenas 2 movimentações: 199 e 299 (para evitar de incluir 2 vezes uma 101 na mesma cadeia).
        $m299 = null;
        foreach ($m299s as $_m299) {
            if ($_m299->getCadeia()->getMovimentacoes()->count() == 2) {
                $m299 = $_m299;
                break;
            }
        }

        if ($m299 == null) {
            $result = "ERRO: Nenhuma movimentação 2.99 encontrada para '" . $m101->getDescricao() . "' - R$ " . $m101->getValor();
            return $result;
        }

// Incluir na cadeia
        $m101->setCadeia($m299->getCadeia());
        $m101->setCadeiaOrdem(3);
        $m101 = $this->getMovimentacaoEntityHandler()->save($m101);

// flush para o BD já...
        $this->getMovimentacaoEntityHandler()->getEntityManager()->flush();
// ...para poder atualizar a m299 no entityManager, e dessa forma saber que ela já está em uma cadeia com 3 movimentações, pulando o if no for acima.

        $result = "SUCESSO: Movimentação consolidada >> '" . $m101->getDescricao() . "' - R$ " . $m101->getValor();

        return $result;
    }


    /**
     * Cálcula a taxa do cartão com base no valor lançado do custo financeiro mensal.
     * @param Carteira $carteira
     * @param $debito
     * @param $totalVendas
     * @param \DateTime $dtIni
     * @param \DateTime $dtFim
     * @return float
     */
    public function calcularTaxaCartao(Carteira $carteira, $debito, $totalVendas, \DateTime $dtIni, \DateTime $dtFim)
    {
        if ($debito) {
            $cCustoOperacionalCartao = $this->doctrine->getRepository(Categoria::class)->findOneBy(['codigo' => 202005002]);
        } else {
            $cCustoOperacionalCartao = $this->doctrine->getRepository(Categoria::class)->findOneBy(['codigo' => 202005001]);
        }
        $tCustoOperacionalCartao = $this->doctrine->getRepository(Movimentacao::class)->findTotal($dtIni, $dtFim, $carteira, $cCustoOperacionalCartao);

        $taxaCartao = 0.0;

        if (($tCustoOperacionalCartao != null) and ($totalVendas != null) and ($tCustoOperacionalCartao > 0)
            and ($totalVendas > 0)) {
            $taxaCartao = bcmul(bcdiv($tCustoOperacionalCartao, $totalVendas, 6), 100, 2);
        }
        return $taxaCartao;
    }


    /**
     * Possíveis tipos de lançamentos.
     * FIXME: mais tarde criar uma tabela para isto.
     * @return array
     */
    public function getTiposLancto() {
        $tipos = [];
        $tipos['MOVIMENTACAO'] = ['title' => 'Movimentação', 'route' => 'fin_movimentacao_form'];
        $tipos['PARCELAMENTO'] = ['title' => 'Parcelamento', 'route' => 'fin_parcelamento_movimentacaoForm'];
        $tipos['CHEQUE_PROPRIO'] = ['title' => 'Cheque Próprio', 'route' => 'fin_movimentacao_formChequeProprio'];
        $tipos['CHEQUE_TERCEIROS'] = ['title' => 'Cheque de Terceiros', 'route' => 'fin_movimentacao_formChequeTerceiros'];
        $tipos['TRANSF_PROPRIA'] = ['title' => 'Transf Própria', 'route' => 'fin_movimentacao_formTransfPropria'];
        return $tipos;
    }

}