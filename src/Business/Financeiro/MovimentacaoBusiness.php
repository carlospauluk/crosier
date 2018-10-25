<?php

namespace App\Business\Financeiro;

use App\Entity\Base\DiaUtil;
use App\Entity\Financeiro\Cadeia;
use App\Entity\Financeiro\Carteira;
use App\Entity\Financeiro\Categoria;
use App\Entity\Financeiro\Grupo;
use App\Entity\Financeiro\Modo;
use App\Entity\Financeiro\Movimentacao;
use App\Entity\Financeiro\OperadoraCartao;
use App\Entity\Financeiro\Parcelamento;
use App\EntityHandler\Financeiro\CadeiaEntityHandler;
use App\EntityHandler\Financeiro\GrupoEntityHandler;
use App\EntityHandler\Financeiro\MovimentacaoEntityHandler;
use App\Utils\ExceptionUtils;
use NumberFormatter;
use Symfony\Bridge\Doctrine\RegistryInterface;

class MovimentacaoBusiness
{

    private $doctrine;

    private $grupoEntityHandler;

    private $movimentacaoEntityHandler;

    private $cadeiaEntityHandler;

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
     * Salva um parcelamento.
     *
     * @param Movimentacao $primeiraParcela
     * @param $parcelas
     * @return Parcelamento
     * @throws \Exception
     */
    public function salvarParcelas(Movimentacao $primeiraParcela, $parcelas)
    {
        $this->doctrine->getEntityManager()->beginTransaction();

        $parcelamento = new Parcelamento();
        $this->doctrine->getEntityManager()->persist($parcelamento);


        $i = 1;
        $valorTotal = 0.0;
        foreach ($parcelas as $parcela) {
            $movimentacao = clone $primeiraParcela;
            $movimentacao->setParcelamento($parcelamento);
            $movimentacao->setNumParcela($i++);
            $movimentacao->setQtdeParcelas(count($parcelas));

            $valor = (new NumberFormatter('pt_BR', NumberFormatter::DECIMAL))->parse($parcela['valor']);
            $movimentacao->setValor($valor);
            $valorTotal = bcadd($valor, $valorTotal);

            $dtVencto = \DateTime::createFromFormat('d/m/Y', $parcela['dtVencto']);
            $movimentacao->setDtVencto($dtVencto);

            $dtVenctoEfetiva = \DateTime::createFromFormat('d/m/Y', $parcela['dtVenctoEfetiva']);
            $movimentacao->setDtVenctoEfetiva($dtVenctoEfetiva);

            $documentoNum = $parcela['documentoNum'];
            $movimentacao->setDocumentoNum($documentoNum);

            // Em casos de grupos de itens...
            $giAtual = $movimentacao->getGrupoItem();
            if ($giAtual) {
                if ($giAtual->getProximo() != null) {
                    $proximoId = $giAtual->getProximo()->getId();
                    $giAtual = $this->doctrine->getRepository(Grupo::class)->find($proximoId);
                } else {
                    $giAtual = $this->getGrupoEntityHandler()->gerarProximo($giAtual->getPai());
                }
                $movimentacao->setGrupoItem($giAtual);
            }

            try {
                $this->getMovimentacaoEntityHandler()->save($movimentacao);
            } catch (\Exception $e) {
                $msg = ExceptionUtils::treatException($e);
                $this->doctrine->getEntityManager()->rollback();
                throw new \Exception('Erro ao salvar parcelas (' . $msg . ')', 0);
            }
        }
        $parcelamento->setValorTotal($valorTotal);
        $this->doctrine->getEntityManager()->flush();

        $this->doctrine->getEntityManager()->commit();

        return $parcelamento;


    }

    /**
     * Corrige os valores de OperadoraCartao.
     *
     * @param \DateTime $dtPagto
     * @param Carteira $carteira
     * @return array|string
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
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
                throw new \Exception("Movimentação sem $cadeia.");
            } else {
                try {

                    $m299 = $this->doctrine->getRepository(Movimentacao::class)->findOneBy(['$cadeia' => $cadeia,
                        'categoria' => $c299
                    ]);

                    $m199 = $this->doctrine->getRepository(Movimentacao::class)->findOneBy(['$cadeia' => $cadeia,
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

        // Encontra a m299 que faça parte de uma $cadeia com apenas 2 movimentações: 199 e 299 (para evitar de incluir 2 vezes uma 101 na mesma $cadeia).
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

// Incluir na $cadeia
        $m101->setCadeia($m299->getCadeia());
        $m101->setCadeiaOrdem(3);
        $m101 = $this->getMovimentacaoEntityHandler()->save($m101);

// flush para o BD já...
        $this->getMovimentacaoEntityHandler()->getEntityManager()->flush();
// ...para poder atualizar a m299 no entityManager, e dessa forma saber que ela já está em uma $cadeia com 3 movimentações, pulando o if no for acima.

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
    public static function getTiposLancto()
    {
        $tipos = [];
        $tipos['MOVIMENTACAO'] = ['title' => 'Movimentação', 'route' => 'fin_movimentacao_form'];
        $tipos['PARCELAMENTO'] = ['title' => 'Parcelamento', 'route' => 'fin_parcelamento_movimentacaoForm'];
        $tipos['TRANSF_PROPRIA'] = ['title' => 'Transferência Própria', 'route' => 'fin_movimentacao_formTransfPropria'];
        $tipos['GRUPO_ITEM'] = ['title' => 'Movimentação de Grupo', 'route' => 'fin_movimentacao_formGrupoItem'];
        return $tipos;
    }

    /**
     * Verifica se pode exibir os campos para setar/alterar a recorrência da movimentação.
     * Regras: somente se...
     *  - É um registro novo.
     *  - Ainda não for recorrente.
     *  - É recorrente, mas é a última da $cadeia.
     * @param Movimentacao $movimentacao
     * @return bool
     */
    public function exibirRecorrente(?Movimentacao $movimentacao)
    {

        if (!$movimentacao or !$movimentacao->getId() or $movimentacao->getRecorrente() == false) {
            return true;
        } else {
            $cadeia = $movimentacao->getCadeia();
            return !$cadeia or $cadeia->getMovimentacoes()->last()->getId() == $movimentacao->getId();
        }
    }

    /**
     * Processa um conjunto de movimentações e gera suas recorrentes.
     *
     * @param $movs
     * @return string
     * @throws \Exception
     */
    public function processarRecorrentes($movs)
    {
        $this->doctrine->getEntityManager()->beginTransaction();
        try {
            $results = "";
            foreach ($movs as $mov) {
                $results .= $this->processarRecorrente($mov) . '<br />';
            }
            $this->doctrine->getEntityManager()->commit();
            return $results;
        } catch (\Exception $e) {
            $this->doctrine->getEntityManager()->rollback();
            throw new \Exception('Erro ao processar recorrentes', 0, $e);
        }
    }

    private function calcularNovaDtVencto(Movimentacao $originante, Movimentacao $nova)
    {
        $novaDtVencto = clone $originante->getDtVencto();
        if ($nova->getRecorrFrequencia() == 'ANUAL') {
            $novaDtVencto = $novaDtVencto->setDate($novaDtVencto->format('Y') + 1, $novaDtVencto->format('m'), $novaDtVencto->format('d'));
        } else {
            $novaDtVencto = $novaDtVencto->setDate($novaDtVencto->format('Y'), $novaDtVencto->format('m') + 1, $novaDtVencto->format('d'));
        }

        if ($nova->getRecorrTipoRepet() == 'DIA_FIXO') {
            // se foi marcado com dia da recorrência maior ou igual a 31
            // ou se estiver processando fevereiro e a data de vencimento for maior ou igual a 29...
            // então sempre setará para o último dia do mês
            if (($nova->getRecorrDia() >= 31) or ($nova->getRecorrDia() >= 29 and $novaDtVencto->format('m') == 2)) {
                // como já tinha adicionado +1 mês ali em cima, só pega o último dia do mês
                $novaDtVencto = \DateTime::createFromFormat('Y-m-d', $novaDtVencto->format('Y-m-t'));
            } else {
                $novaDtVencto->setDate($novaDtVencto->format('Y'), $novaDtVencto->format('m'), $nova->getRecorrDia());
            }
            $nova->setDtVencto($novaDtVencto);
            $nova->setDtVenctoEfetiva(null);
        } else if ($nova->getRecorrTipoRepet() == 'DIA_UTIL') {
            // Procuro o dia útil ordinalmente...
            $diasUteisMes = $this->doctrine->getRepository(DiaUtil::class)->findDiasUteisFinanceirosByMesAno($novaDtVencto);
            $nova->setDtVencto($diasUteisMes[$nova->getRecorrDia()]);
        }
    }

    /**
     * @param Movimentacao $originante
     * @return mixed
     * @throws \Exception
     */
    private function processarRecorrente(Movimentacao $originante)
    {
        $result = "";

        if (!$originante->getRecorrente()) {
            // Tem que ter sido passada uma List com movimentações que sejam recorrentes
            throw new \Exception("Movimentação não recorrente não pode ser processada.");
        }

        if (!$originante->getRecorrFrequencia() or $originante->getRecorrFrequencia() == 'NENHUMA') {
            throw new \Exception("Recorrência com frequência = 'NENHUMA'.");
        }
        if (!$originante->getRecorrTipoRepet() or $originante->getRecorrTipoRepet() == 'NENHUMA') {
            throw new \Exception("Recorrência com tipo de repetição = 'NENHUMA'.");
        }


        // verifico se já existe a movimentação $posterior
        if ($originante->getCadeia() != null) {
            $posterior = $this->doctrine->getRepository(Movimentacao::class)
                ->findOneBy(['cadeia' => $originante->getCadeia(),
                    'cadeiaOrdem' => $originante->getCadeiaOrdem() + 1]);
            if ($posterior) {

                // verifico se teve alterações na originante
                if ($originante->getUpdated()->getTimestamp() > $posterior->getUpdated()->getTimestamp()) {

                    $posterior->setRecorrente(true);
                    $posterior->setRecorrDia($originante->getRecorrDia());
                    $posterior->setRecorrVariacao($originante->getRecorrVariacao());
                    $posterior->setRecorrFrequencia($originante->getRecorrFrequencia());
                    $posterior->setRecorrTipoRepet($originante->getRecorrTipoRepet());

                    $posterior->setDescricao($originante->getDescricao());

                    $posterior->setValor($originante->getValor());
                    $posterior->setAcrescimos($originante->getAcrescimos());
                    $posterior->setDescontos($originante->getDescontos());
                    $posterior->setValorTotal(null); // null para recalcular no beforeSave

                    $posterior->setPessoa($originante->getPessoa());

                    $posterior->setCarteira($originante->getCarteira());
                    $posterior->setCategoria($originante->getCategoria());
                    $posterior->setCentroCusto($originante->getCentroCusto());

                    $posterior->setModo($originante->getModo());

                    $this->calcularNovaDtVencto($originante, $posterior);

                    try {
                        $posterior = $this->getMovimentacaoEntityHandler()->save($posterior);
                        $result = "SUCESSO ao atualizar movimentação: " . $originante->getDescricao();
                    } catch (\Exception $e) {
                        $result = "ERRO ao atualizar movimentação: " . $originante->getDescricao() . ". (" . $e->getMessage() . ")";
                    }
                } else {
                    $result = "SUCESSO movimentação posterior já existente: " . $originante->getDescricao() . "\r\n";
                }

                return $result;
            }
        }

        $salvarOriginal = false;

        $nova = clone $originante;
        $nova->setUnqControle(null);

        $nova->setId(null);
        $nova->setDtPagto(null);

        $cadeia = $originante->getCadeia();

        // Se ainda não possui uma $cadeia...
        if ($cadeia != null) {
            $nova->setCadeiaOrdem($originante->getCadeiaOrdem() + 1);
        } else {
            $cadeia = new $cadeia();

            // Como está sendo gerada uma $cadeia nova, tenho que atualizar a movimentação original e mandar salva-la também.
            $originante->setCadeiaOrdem(1);
            $originante->setCadeia($cadeia);
            $salvarOriginal = true; // tem que salvar a originante porque ela foi incluída na $cadeia

            $nova->setCadeiaOrdem(2);
        }

        $cadeia->setVinculante(false);
        $cadeia->setFechada(false);

        $nova->setCadeia($cadeia);

        $this->calcularNovaDtVencto($originante, $nova);

        $nova->setStatus('ABERTA'); // posso setar como ABERTA pois no beforeSave(), se for CHEQUE, ele altera para A_COMPENSAR.
        $nova->setIdSistemaAntigo(null);
        $nova->setTipoLancto('A_PAGAR_RECEBER');

        // seto o número do cheque para ????, para que seja informado $posteriormente.
        if ($nova->getChequeNumCheque() != null) {
            $nova->setChequeNumCheque("????");
        }

        // Tem que salvar a $cadeia, pois foi removido os Cascades devido a outros problemas...

        $this->getCadeiaEntityHandler()->save($cadeia);

        if ($salvarOriginal) {
            try {
                $this->getMovimentacaoEntityHandler()->save($originante);
                $result .= "SUCESSO ao salvar movimentação originante: " . $originante->getDescricao();
            } catch (\Exception $e) {
                $result .= "ERRO ao salvar movimentação originante: " . $originante->getDescricao() . ". (" . $e->getMessage() . ")";
            }
            $nova->setCadeia($originante->getCadeia());
            $salvarOriginal = false;
        }

        try {
            $this->getMovimentacaoEntityHandler()->save($nova);
            $result .= "SUCESSO ao gerar movimentação: " . $nova->getDescricao();
        } catch (\Exception $e) {
            $result .= "ERRO ao atualizar movimentação: " . $originante->getDescricao() . ". (" . $e->getMessage() . ")";
        }

        return $result;
    }


    /**
     * @return mixed
     */
    public function getCadeiaEntityHandler(): CadeiaEntityHandler
    {
        return $this->cadeiaEntityHandler;
    }

    /**
     * @required
     * @param mixed $cadeiaEntityHandler
     */
    public function setCadeiaEntityHandler(CadeiaEntityHandler $cadeiaEntityHandler): void
    {
        $this->cadeiaEntityHandler = $cadeiaEntityHandler;
    }


}