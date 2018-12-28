<?php /** @noinspection PhpUnhandledExceptionInspection */

namespace App\EntityHandler\Financeiro;

use App\Business\Financeiro\MovimentacaoBusiness;
use App\Entity\Base\DiaUtil;
use App\Entity\Financeiro\Cadeia;
use App\Entity\Financeiro\Carteira;
use App\Entity\Financeiro\Categoria;
use App\Entity\Financeiro\CentroCusto;
use App\Entity\Financeiro\Modo;
use App\Entity\Financeiro\Movimentacao;
use App\EntityHandler\EntityHandler;
use Psr\Log\LoggerInterface;

class MovimentacaoEntityHandler extends EntityHandler
{

    private $movimentacaoBusiness;

    private $cadeiaEntityHandler;

    private $logger;

    /**
     * @return mixed
     */
    public function getMovimentacaoBusiness(): ?MovimentacaoBusiness
    {
        return $this->movimentacaoBusiness;
    }

    /**
     * @required
     * @param mixed $movimentacaoBusiness
     */
    public function setMovimentacaoBusiness(MovimentacaoBusiness $movimentacaoBusiness): void
    {
        $this->movimentacaoBusiness = $movimentacaoBusiness;
    }

    /**
     * @return mixed
     */
    public function getLogger(): LoggerInterface
    {
        return $this->logger;
    }

    /**
     * @required
     * @param mixed $logger
     */
    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }


    /**
     * @return mixed
     */
    public function getCadeiaEntityHandler(): ?CadeiaEntityHandler
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


    public function getEntityClass()
    {
        return Movimentacao::class;
    }

    public function beforeSave($movimentacao)
    {
        if ($movimentacao->getTipoLancto() == 'GRUPO') {
            $modo50 = $this->getEntityManager()->getRepository(Modo::class)->findOneBy(['codigo' => 50]);
            $movimentacao->setModo($modo50);
        }

        if (!$movimentacao->getModo()) {
            throw new \Exception("Modo deve ser informado para a movimentação '" . $movimentacao->getDescricao() . "''");
        }

        $movimentacao->setDescricao(trim($movimentacao->getDescricao()));

        if (!$movimentacao->getUnqControle()) {
            $movimentacao->setUnqControle(md5(uniqid(rand(), true)));
        }

        // Salvando movimentação agrupada
        if ($movimentacao->getTipoLancto() == 'MOVIMENTACAO_AGRUPADA') {
            $movimentacao->setTipoLancto('GRUPO');
        }
        if ($movimentacao->getTipoLancto() == 'GRUPO') {
            if (!$movimentacao->getGrupoItem()) {
                throw new \Exception("GrupoItem deve ser informado.");
            } else {
                $movimentacao->setDtVencto($movimentacao->getGrupoItem()->getDtVencto());
                $movimentacao->setDtVenctoEfetiva($movimentacao->getGrupoItem()->getDtVencto());
                $movimentacao->setDtPagto($movimentacao->getGrupoItem()->getDtVencto());

                $carteiraMovsAgrupadas = $this->getEntityManager()->getRepository(Carteira::class)->find(7);
                $movimentacao->setCarteira($carteiraMovsAgrupadas);
            }
        }

        if (!$movimentacao->getCarteira()) {
            throw new \Exception("Campo 'Carteira' precisa ser informado (bs).");
        }

        if ($movimentacao->getPlanoPagtoCartao() == null) {
            $movimentacao->setPlanoPagtoCartao('N_A');
        }

        if (!$movimentacao->getCentroCusto()) {
            $movimentacao->setCentroCusto($this->getEntityManager()->getRepository(CentroCusto::class)->find(1));
        }

        if (!$movimentacao->getCategoria()) {
            throw new \Exception("É necessário informar a categoria da movimentação.");
        }
//        else if ($movimentacao->getCategoria()->getCentroCustoDif() == FALSE) {
//            $movimentacao->setCentroCusto($this->getEntityManager()->getRepository(CentroCusto::class)->find(1));
//        }

        // Se não passar descrição, tenta montá-la a partir da bandeira do cartão.
        if (!trim($movimentacao->getDescricao())) {
            if ($movimentacao->getBandeiraCartao() != null) {
                $movimentacao->setDescricao($movimentacao->getBandeiraCartao()->getDescricao());
            }
        }

        // Transferências próprias e recolhimentos de carteiras só passam a Dt Moviment na view
        if ($movimentacao->getTipoLancto() == 'TRANSF_CAIXA' or $movimentacao->getTipoLancto() == 'TRANSF_PROPRIA') {
            if (!$movimentacao->getDtVencto()) {
                $movimentacao->setDtVencto($movimentacao->getDtMoviment());
            }
            if (!$movimentacao->getDtPagto()) {
                $movimentacao->setDtPagto($movimentacao->getDtMoviment());
            }
        }

        // se não é do tipo cheque, então marca como null a entidade
        if (FALSE == $movimentacao->getModo()->getModoDeCheque()) {
            $movimentacao->setChequeNumCheque(null);
            $movimentacao->setChequeAgencia(null);
            $movimentacao->setChequeBanco(null);
            $movimentacao->setChequeConta(null);
        } else {
            // se for com cheque mas estiver como ABERTA, muda para A_COMPENSAR (pq pode ter sido erro de importação).
            if ($movimentacao->getStatus() == 'ABERTA') {
                $movimentacao->setStatus('A_COMPENSAR');
            }
        }

        if (FALSE == $movimentacao->getModo()->getModoDeCartao()) {
            $movimentacao->setBandeiraCartao(null);
            $movimentacao->setOperadoraCartao(null);
        }

        if (!$movimentacao->getDtPagto()) {
            if (strpos($movimentacao->getModo()->getDescricao(), 'CHEQUE') !== FALSE) {
                $movimentacao->setStatus('A_COMPENSAR');
            } else {
                $movimentacao->setStatus('ABERTA');
            }
        } else if ($movimentacao->getDtPagto() != null) {
            $movimentacao->setStatus('REALIZADA');
            if ($movimentacao->getDtVencto() == null) {
                $movimentacao->setDtVencto($movimentacao->getDtPagto());
            }
            if ($movimentacao->getDtMoviment() == null) {
                $movimentacao->setDtMoviment($movimentacao->getDtPagto());
            }
        }

        if (!$movimentacao->getRecorrente()) {
            $movimentacao->setRecorrente(false);
        }

        if (!$movimentacao->getRecorrFrequencia()) {
            $movimentacao->setRecorrFrequencia('NENHUMA');
        }

        if (!$movimentacao->getRecorrTipoRepet()) {
            $movimentacao->setRecorrTipoRepet('NENHUMA');
        }

        if ($movimentacao->getStatus() == 'REALIZADA') {
            if (FALSE == $movimentacao->getCarteira()->getConcreta()) {
                throw new \Exception("Somente carteiras concretas podem conter movimentações realizadas.");
            }
            if ($movimentacao->getModo()->getCodigo() == 99) {
                throw new \Exception("Não é possível salvar uma movimentação realizada em modo 99 - INDEFINIDO");
            }
        }

        // Calcula a dtVenctoEfetiva (considera-se sempre o próximo dia útil)
        if (($movimentacao->getDtVenctoEfetiva() == null) and ($movimentacao->getDtVencto() != null)) {
            $proxDiaUtilFinanceiro = $this->getEntityManager()->getRepository(DiaUtil::class)->findProximoDiaUtilFinanceiro($movimentacao->getDtVencto());
            $movimentacao->setDtVenctoEfetiva($proxDiaUtilFinanceiro);
        }

        if ((!$movimentacao->getValor()) and (!$movimentacao->getValorTotal())) {
            $movimentacao->setValor($movimentacao->getValorTotal());
        }

        // O valor total é read-only
        $movimentacao->setValor($movimentacao->getValor() ? abs($movimentacao->getValor()) : 0);
        $movimentacao->setDescontos($movimentacao->getDescontos() ? (-1 * abs($movimentacao->getDescontos())) : 0);
        $movimentacao->setAcrescimos($movimentacao->getAcrescimos() ? abs($movimentacao->getAcrescimos()) : 0);
        $movimentacao->calcValorTotal();

        if ($movimentacao->getParcelamento() != null) {
            $movimentacao->getParcelamento()->recalcularParcelas();
            $movimentacao->setQtdeParcelas($movimentacao->getParcelamento()->getQtdeParcelas());
        }

        if (($movimentacao->getPessoa() != null) && ($movimentacao->getPessoa()->getId() == null)) {
            $movimentacao->setPessoa(null);
        }

        // ajusta a dt útil
        $movimentacao->setDtUtil(!$movimentacao->getDtPagto() ? $movimentacao->getDtVenctoEfetiva() : $movimentacao->getDtPagto());

        if ($movimentacao->getCarteira()->getOperadoraCartao()) {
            $this->getEntityManager()->refresh($movimentacao->getCarteira()->getOperadoraCartao());
            $movimentacao->setOperadoraCartao($movimentacao->getCarteira()->getOperadoraCartao());
        }

        if (($movimentacao->getStatus() == 'ABERTA' or $movimentacao->getStatus() == 'A_COMPENSAR')
            and ($movimentacao->getCarteira()->getAbertas() == FALSE)) {
            throw new \Exception("Esta carteira não pode conter movimentações abertas.");
        }
        return $movimentacao;
    }

    /**
     * Tratamento diferenciado para cada tipoLancto.
     *
     * @param \App\Entity\Base\EntityId $movimentacao
     * @return \App\Entity\Base\EntityId|Movimentacao|null
     * @throws \Exception
     */
    public function save($movimentacao)
    {
        if (!$movimentacao->getTipoLancto()) {
            throw new \Exception("Tipo Lancto não informado para " . $movimentacao->getDescricaoMontada());
        }

        if ($movimentacao->getTipoLancto() == 'TRANSF_PROPRIA') {
            return $this->saveTransfPropria($movimentacao);
        } else {
            $movimentacao = parent::save($movimentacao);
        }
        return $movimentacao;

    }

    /**
     * @param $movs
     * @throws \Exception
     */
    public function persistAll($movs)
    {
        try {
            $this->getEntityManager()->beginTransaction();
            foreach ($movs as $mov) {
                $this->getMovimentacaoBusiness()->mergeAll($mov);
                $this->save($mov);
                $this->getEntityManager()->clear();
            }
            $this->getEntityManager()->commit();
        } catch (\Exception $e) {
            $this->getEntityManager()->rollback();
            $err = 'Erro ao salvar movimentações importadas. ';
            if ($mov) {
                $err .= '(' . $mov->getDescricao() . ')';
            }
            throw new \Exception($err);
        }
    }

    /**
     * Salva uma transferência entre carteiras.
     * Pode ser chamado a partir da saveTransfCaixa (que gera, então, 3 movimentações).
     * @param Movimentacao $movimentacao
     * @return \App\Entity\Base\EntityId|Movimentacao|null
     * @throws \Exception
     */
    public function saveTransfPropria(Movimentacao $moviment299)
    {
        $categ299 = $this->getEntityManager()->getRepository(Categoria::class)->findOneBy(['codigo' => 299]);
        $categ199 = $this->getEntityManager()->getRepository(Categoria::class)->findOneBy(['codigo' => 199]);
        $moviment299->setCategoria($categ299);


        if ($moviment299->getId()) {
            $cadeia = $moviment299->getCadeia();
            $moviment199 = $this->getEntityManager()->getRepository(Movimentacao::class)->findOneBy(['cadeia' => $cadeia, 'categoria' => $categ199]);
            if (!$moviment199) {
                throw new \Exception('Cadeia de transferência própria já existe, porém sem a 1.99');
            }
        } else {
            $cadeia = new Cadeia();
            $moviment199 = new Movimentacao();
        }

        $moviment299->setCadeiaOrdem(1);
        $moviment299->setCadeia($cadeia);
        $moviment299->calcValorTotal();

        // Salvar a 199
        $moviment199->setDescricao($moviment299->getDescricao());
        $moviment199->setStatus('REALIZADA');
        $moviment199->setCategoria($categ199);
        $moviment199->setCentroCusto($this->getEntityManager()->getRepository(CentroCusto::class)->find(1));
        $moviment199->setCarteira($moviment299->getCarteiraDestino());
        $moviment199->setChequeNumCheque($moviment299->getChequeNumCheque());
        $moviment199->setChequeConta($moviment299->getChequeConta());
        $moviment199->setChequeBanco($moviment299->getChequeBanco());
        $moviment199->setChequeAgencia($moviment299->getChequeAgencia());
        $moviment199->setDtMoviment($moviment299->getDtMoviment());
        $moviment199->setDtVencto($moviment299->getDtVencto());
        $moviment199->setDtVenctoEfetiva($moviment299->getDtVenctoEfetiva());
        $moviment199->setDtPagto($moviment299->getDtPagto());
        $moviment199->setModo($moviment299->getModo());
        $moviment199->setValor($moviment299->getValor());
        $moviment199->setDescontos($moviment299->getDescontos());
        $moviment199->setAcrescimos($moviment299->getAcrescimos());
        $moviment199->calcValorTotal();
        $moviment199->setTipoLancto($moviment299->getTipoLancto());
        $moviment199->setBandeiraCartao($moviment299->getBandeiraCartao());
        $moviment199->setCadeiaOrdem(2); // aqui incremento, pois não sei se é a 299 foi a 1 ou a 2.
        $moviment199->setCadeia($cadeia);

        $cadeia->setVinculante(false);
        $cadeia->setFechada(false);
        $cadeia = $this->getCadeiaEntityHandler()->save($cadeia);

        $moviment299 = parent::save($moviment299);
        $moviment199 = parent::save($moviment199);


        // agora que já salvou a primeira, pode fechar a cadeia
        $cadeia->setVinculante(true);
        $cadeia->setFechada (true);
        $cadeia = $this->getCadeiaEntityHandler()->save($cadeia);

        return $moviment299;
    }

    /**
     * Processa uma lista de movimentações recorrentes para gerar as movimentações posteriores.
     * @param $movimentacoes
     * @return array
     */
    public function processarRecorrentes($movimentacoes)
    {
        // variável utilizada para marcar se deve ser salva a original (não mando salvar antes por causa dos problemas de flush).
        $results = [];
        foreach ($movimentacoes as $originante) {
            $results[] = $this->processarRecorrente($originante);
        }
        return $results;
    }

    /**
     * Processa uma movimentação para gerar/atualizar sua recorrente posterior.
     *
     * @param Movimentacao $originante
     * @return string
     */
    public function processarRecorrente(Movimentacao $originante)
    {
        try {


            $salvarOriginal = false;

            $result = "";

            if ($originante->getRecorrencia()->getRecorrente() == FALSE) {
                // Tem que ter sido passada uma List com movimentações que sejam recorrentes
                throw new \Exception("Movimentação não recorrente não pode ser processada.");
            }
            if ($originante->getRecorrencia()->getFrequencia() == 'NENHUMA') {
                throw new \Exception("Recorrência com frequência = 'NENHUMA'.");
            }
            if ($originante->getRecorrencia()->getTipoRepeticao() == 'NENHUMA') {
                throw new \Exception("Recorrência com tipo de repetição = 'NENHUMA'.");
            }

            // verifico se já existe a movimentação posterior
            if ($originante->getCadeia() != null) {
                $posterior = $this->getEntityManager()->getRepository(Cadeia::class)->findBy(['cadeia' => $originante->getCadeia(), 'cadeiaOrdem' => $originante->getCadeiaOrdem() + 1]);
                if ($posterior) {

                    // verifico se teve alterações na originante
                    if ($originante->getIudt()->getUpdated()->getTime() > $posterior->getIudt()->getUpdated()->getTime()) {

                        $posterior->setCarteira($originante->getCarteira());
                        $posterior->setCategoria($originante->getCategoria());
                        $posterior->setCentroCusto($originante->getCentroCusto());

                        $posterior->setModo($originante->getModo());
                        $posterior->setPessoa($originante->getPessoa());
                        $posterior->setRecorrencia($originante->getRecorrencia());
                        $posterior->setDescricao($originante->getDescricao());

                        $posterior->setValor($originante->getValor());
                        $posterior->setAcrescimos($originante->getAcrescimos());
                        $posterior->setDescontos($originante->getDescontos());
                        $posterior->setValorTotal(null); // null para recalcular no beforeSave

                        $this->calcularNovaDtVencto($originante, $posterior);

                        try {
                            $posterior = $this->save($posterior);
                            $result = "<<SUCESSO>> ao atualizar movimentação: " . $originante->getDescricao();
                        } catch (\Exception $e) {
                            $result = "<<ERRO>> ao atualizar movimentação: " . $originante->getDescricao() . ". (" . $e->getMessage() . ")";
                        }
                    } else {
                        $result .= "<<SUCESSO>> movimentação posterior já existente: " . $originante->getDescricao() . "\r\n";
                    }

                    return $result;
                }
            }

            $nova = clone $originante;
            $nova->setUnqControle(null); // vai re-setar no beforePersist()
            $nova->setId(null);
            $nova->setDtPagto(null);

            $cadeia = $originante->getCadeia();
            // Se ainda não possui uma $cadeia->..
            if ($cadeia != null) {
                $nova->setCadeiaOrdem($originante->getCadeiaOrdem() + 1);
            } else {
                $cadeia = new Cadeia();
                // Como está sendo gerada uma cadeia nova, tenho que atualizar a movimentação original e mandar salva-la também.
                $originante->setCadeiaOrdem(1);
                $originante->setCadeia($cadeia);
                $salvarOriginal = true; // tem que salvar a originante porque ela foi incluída na cadeia
                $nova->setCadeiaOrdem(2);
            }
            $cadeia->setVinculante(false);
            $cadeia->setFechada(false);
            $nova->setCadeia($cadeia);

            $this->calcularNovaDtVencto($originante, $nova);

            $nova->setStatus('ABERTA'); // posso setar como ABERTA pois no beforeSave(), se for CHEQUE, ele altera para A_COMPENSAR.
            $nova->setIdSistemaAntigo(null);
            $nova->setTipoLancto('A_PAGAR_RECEBER');

            // seto o número do cheque para ????, para que seja informado posteriormente.
            if ($nova->getCheque() != null) {
                $nova->getCheque()->setNumCheque("????");
            }

            // Tem que salvar a cadeia, pois foi removido os Cascades devido a outros problemas...

            $cadeia = $this->getCadeiaEntityHandler()->save($cadeia);

            $originante->setCadeia($cadeia);

            if ($salvarOriginal) {
                try {
                    $originante = $this->save($originante);
                    $result .= "<<SUCESSO>> ao salvar movimentação originante: " . $originante->getDescricao();
                } catch (\Exception $e) {
                    $result .= "<<ERRO>> ao salvar movimentação originante: " . $originante->getDescricao() . ". (" . $e->getMessage() . ")";
                }
                $nova->setCadeia($originante->getCadeia());
            }

            try {
                $nova = $this->save($nova);
                $result .= "<<SUCESSO>> ao gerar movimentação: " . $nova->getDescricao();
            } catch (\Exception $e) {
                $result .= "<<ERRO>> ao atualizar movimentação: " . $originante->getDescricao() . ". (" . $e->getMessage() . ")";
            }

            return $result;

        } catch (\Exception $e) {
            return "<<ERRO>> ao processar movimentação: " . $originante->getDescricao() . ". (" . $e->getMessage() . ")";
        }
    }


    /**
     * @param Movimentacao $originante
     * @param Movimentacao $nova
     */
    private function calcularNovaDtVencto(Movimentacao $originante, Movimentacao $nova)
    {
        $novaDtVencto = clone $originante->getDtVencto();

        if ($nova->getRecorrencia()->getFrequencia() == 'ANUAL') {
            $novaDtVencto->setDate($novaDtVencto->format('Y') + 1, $novaDtVencto->format('m'), $novaDtVencto->format('d'));
        } else
            if ($nova->getRecorrencia()->getFrequencia() == 'MENSAL') {
                $novaDtVencto->setDate($novaDtVencto->format('Y'), $novaDtVencto->format('m') + 1, $novaDtVencto->format('d'));
            }

        if ($nova->getRecorrencia()->getTipoRepeticao() == 'DIA_FIXO') {
            // se foi marcado com dia da recorrência maior ou igual a 31
            // ou se estiver processando fevereiro e a data de vencimento for maior ou igual a 29...
            // então sempre setará para o último dia do mês
            if (($nova->getRecorrDia() >= 31) OR (($nova->getRecorrDia() >= 29) && ($novaDtVencto->format('m') == 2))) {

                // como já tinha adicionado +1 mês ali em cima, adicionada mais um, e volta pra trás com dia-1
                $novaDtVencto->setDate($novaDtVencto->format('Y'), $novaDtVencto->format('m') + 1, 0);
            } else {
                $novaDtVencto->setDate($novaDtVencto->format('Y'), $novaDtVencto->format('m') + 1, $nova->getRecorrDia());
            }
            $nova->setDtVencto($novaDtVencto);


            $nova->setDtVenctoEfetiva($this->getEntityManager()->getRepository(DiaUtil::class)->findProximoDiaUtilFinanceiro($nova->getDtVencto()));
        } else if ($nova->getRecorrencia()->getTipoRepeticao() == 'DIA_UTIL') {
            // Procuro o dia útil ordinalmente...

            $dias = $this->getEntityManager()->getRepository(DiaUtil::class)->findDiasUteisFinanceirosByMesAno($novaDtVencto);
            $nova->setDtVencto($dias[$nova->getRecorrDia()]->getDia());
            $nova->setDtVenctoEfetiva(null);
        }
        return;
    }

    /**
     * Tratamento para casos de movimentação em cadeia.
     * @param $movimentacao
     */
    public function delete($movimentacao)
    {
        // Se a movimentação faz parte de uma cadeia vinculante, exclui todas as movimentações da cadeia também.
        if (($movimentacao->getCadeia() != null) and $movimentacao->getCadeia()->getVinculante()) {
            $cadeia = $movimentacao->getCadeia();
            foreach ($cadeia->getMovimentacoes() as $m) {
                parent::delete($m);
            }
        } else {
            parent::delete($movimentacao);
        }
    }

    public function beforeDelete($movimentacao)
    {
        if (($movimentacao->getParcelamento() != null) && ($movimentacao->getNumParcela() != null)) {
            if ($movimentacao->getNumParcela() < $movimentacao->getParcelamento()->getQtdeParcelas()) {
                throw new \Exception("Só é possível excluir a última parcela do parcelamento.");
            } else {
                $movimentacao->getParcelamento()->getParcelas()->removeElement($movimentacao);
                $movimentacao->setParcelamento(null);
            }
        }
        return $movimentacao;
    }

}