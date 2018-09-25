<?php

namespace App\EntityHandler\Financeiro;

use App\Business\Financeiro\MovimentacaoBusiness;
use App\Entity\Base\DiaUtil;
use App\Entity\Financeiro\Cadeia;
use App\Entity\Financeiro\Carteira;
use App\Entity\Financeiro\Categoria;
use App\Entity\Financeiro\CentroCusto;
use App\Entity\Financeiro\Movimentacao;
use App\EntityHandler\EntityHandler;

class MovimentacaoEntityHandler extends EntityHandler
{

    private $movimentacaoBusiness;

    private $cadeiaEntityHandler;

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

    public function beforePersist($movimentacao)
    {
        if (!$movimentacao->getUnqControle()) {
            $movimentacao->setUnqControle(md5(uniqid(rand(), true)));
        }

        // Salvando movimentação agrupada
        if ($movimentacao->getTipoLancto() == 'MOVIMENTACAO_AGRUPADA') {
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
        } else if ($movimentacao->getCategoria()->getCentroCustoDif() == FALSE) {
            $movimentacao->setCentroCusto($this->getEntityManager()->getRepository(CentroCusto::class)->find(1));
        }

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
            if ($movimentacao->getModo()->getDescricao()->contains("CHEQUE")) {
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
            $movimentacao->getRecorrDia(null);
            $movimentacao->getRecorrFrequencia('NENHUMA');
            $movimentacao->getRecorrTipoRepet('NENHUMA');
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
        $movimentacao->setDescontos($movimentacao->getDescontos() ? (-1 * abs($movimentacao->getValor())) : 0);
        $movimentacao->setDescontos($movimentacao->getDescontos() ? abs($movimentacao->getValor()) : 0);
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

        if (($movimentacao->getStatus() == 'ABERTA' or $movimentacao->getStatus() == 'A_COMPENSAR')
            and ($movimentacao->getCarteira()->getAbertas() == 'FALSE')) {
            throw new \Exception("Esta carteira não pode conter movimentações abertas.");
        }
        return $movimentacao;
    }

    public function persistAll($movs)
    {
        try {
            $this->getEntityManager()->beginTransaction();
            foreach ($movs as $mov) {
                $this->getMovimentacaoBusiness()->mergeAll($mov);
                $this->persist($mov);
            }
            $this->getEntityManager()->commit();
        } catch (\Exception $e) {
            $this->getEntityManager()->rollback();
            throw new \Exception('Erro ao salvar movimentações importadas: (' . $e->getMessage() . ')');
        }
    }

    /**
     * Salva uma transferência entre carteiras.
     * Pode ser chamado a partir da saveTransfCaixa (que gera, então, 3 movimentações).
     * @param Movimentacao $movimentacao
     * @return \App\Entity\Base\EntityId|Movimentacao|null
     */
    public function saveTransfPropria(Movimentacao $movimentacao)
    {
        $cadeiaOrdem = !$movimentacao->getCadeiaOrdem() ? 1 : $movimentacao->getCadeiaOrdem();

        $moviment299 = null;

        // Se NÃO estiver passando uma 299 então é uma TRANSF_CAIXA (101 + 299 + 199).
        if ($movimentacao->getCategoria()->getCodigo() != 299) {
            $cadeia = $movimentacao->getCadeia();
            $moviment299 = new Movimentacao();
            $cadeiaOrdem = 2; // é a segunda movimentação das 3
            $moviment299->setCadeiaOrdem($cadeiaOrdem);
            $moviment299->setDescricao($movimentacao->getDescricao());
            $moviment299->setCategoria($this->getEntityManager()->getRepository(Categoria::class)->find(299));
            $moviment299->setModo($movimentacao->getModo());
            $moviment299->setCarteira($movimentacao->getCarteira());
            $moviment299->setStatus('REALIZADA');
            $moviment299->setValor($movimentacao->getValor());
            $moviment299->setValorTotal($movimentacao->getValorTotal());
            $moviment299->setCentroCusto($movimentacao->getCentroCusto());
            $moviment299->setDtMoviment($movimentacao->getDtMoviment());
            $moviment299->setDtVencto($movimentacao->getDtVencto());
            $moviment299->setDtVenctoEfetiva($movimentacao->getDtVenctoEfetiva());
            $moviment299->setDtPagto($movimentacao->getDtPagto());
            $moviment299->setTipoLancto($movimentacao->getTipoLancto());

            $moviment299->setBandeiraCartao($movimentacao->getBandeiraCartao());

        } else {
            // caso contrário é só uma TRANSF_PROPRIA mesmo
            $cadeia = new Cadeia();
            $moviment299 = $movimentacao;
            $cadeiaOrdem = 1; // é a primeira movimentação das 2
            $moviment299->setCadeiaOrdem($cadeiaOrdem);
        }

        $cadeia->setVinculante(false);

        $cadeia = $this->getCadeiaEntityHandler()->persist($cadeia);

        $moviment299->setCadeia($cadeia);

        // Salvar a 199
        $moviment199 = new Movimentacao();
        $moviment199->setCadeiaOrdem(++$cadeiaOrdem); // aqui incremento, pois não sei se é a 299 foi a 1 ou a 2.
        $moviment199->setCadeia($cadeia);
        $moviment199->setDescricao($movimentacao->getDescricao());
        $moviment199->setStatus('REALIZADA');
        $moviment199->setCategoria($this->getEntityManager()->getRepository(Categoria::class)->find(199));
        $moviment199->setCentroCusto($this->getEntityManager()->getRepository(CentroCusto::class)->find(1));
        $moviment199->setCarteira($movimentacao->getCarteiraDestino());
        $moviment199->setCheque($moviment299->getCheque());
        $moviment199->setDtMoviment($moviment299->getDtMoviment());
        $moviment199->setDtVencto($moviment299->getDtVencto());
        $moviment199->setDtVenctoEfetiva($moviment299->getDtVenctoEfetiva());
        $moviment199->setDtPagto($moviment299->getDtPagto());
        $moviment199->setModo($moviment299->getModo());
        $moviment199->setValor($moviment299->getValor());
        $moviment199->setValorTotal($moviment299->getValorTotal());
        $moviment199->setTipoLancto($moviment299->getTipoLancto());
        $moviment199->setBandeiraCartao($moviment299->getBandeiraCartao());

        $moviment299 = $this->persist($moviment299);
        // agora que já salvou a primeira, pode fechar a cadeia
        $cadeia = $moviment299->getCadeia();
        $cadeia->setFechada(true);
        $moviment199->setCadeia($cadeia);
        $moviment199 = $this->save($moviment199);

        // Tem que salvar a cadeia, pois foi removido os Cascades devido a outros problemas...
        $cadeia->setVinculante(true);
        $cadeia = $this->getCadeiaEntityHandler()->persist($cadeia);

        return $moviment299;
    }

    /**
     * Salva uma TRANSFERÊNCIA DE ENTRADA DE CAIXA.
     * São geradas 3 movimentações: a original do lançamento + 299 + 199.
     */
    public function saveTransfCaixa(Movimentacao $movimentacao)
    {

        try {
            // cria a cadeia destas movimentações
            $cadeia = new Cadeia();
            $cadeia->setVinculante(true);
            $movimentacao->setStatus('REALIZADA');
            $movimentacao->setCentroCusto($this->getEntityManager()->getRepository(CentroCusto::class)->find(1));
            $movimentacao->setDtVencto($movimentacao->getDtMoviment());
            $movimentacao->setDtVenctoEfetiva($movimentacao->getDtMoviment());
            $movimentacao->setDtPagto($movimentacao->getDtMoviment());
            $movimentacao->setCadeia($cadeia);
            $movimentacao->setCadeiaOrdem(1);
            $movimentacao = $this->persist($movimentacao);
            $movimentacao = $this->saveTransfPropria($movimentacao);
            $this->getEntityManager()->flush();
            return $movimentacao;
        } catch (\Exception $e) {
            throw new \Exception("Erro ao salvar a transferência de caixa.");
        }
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


}