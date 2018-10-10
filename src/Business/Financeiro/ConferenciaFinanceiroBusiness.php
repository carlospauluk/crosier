<?php


namespace App\Business\Financeiro;

use App\Entity\Financeiro\Carteira;
use App\Entity\Financeiro\Categoria;
use App\Entity\Financeiro\GrupoItem;
use App\Entity\Financeiro\Modo;
use App\Entity\Financeiro\Movimentacao;
use App\Entity\Financeiro\RegistroConferencia;
use App\Entity\Vendas\Venda;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Class ConferenciaFinanceiroBusiness.
 * Agrega as funcionalidades para o ConferenciaFinanceiroController.
 *
 * @package App\Business\Financeiro
 * @author Carlos Eduardo Pauluk
 */
class ConferenciaFinanceiroBusiness
{

    private $doctrine;

    private $movimentacaoBusiness;

    /**
     * ConferenciaFinanceiroBusiness constructor.
     * @param RegistryInterface $doctrine
     */
    public function __construct(RegistryInterface $doctrine, MovimentacaoBusiness $movimentacaoBusiness)
    {
        $this->doctrine = $doctrine;
        $this->movimentacaoBusiness = $movimentacaoBusiness;
    }

    /**
     * @param \DateTime $dtIni
     * @param \DateTime $dtFim
     * @return array
     * @throws \Exception
     */
    public function buildLists(\DateTime $dtIni, \DateTime $dtFim)
    {
        $listCaixaVista = $this->buildListCaixaVista($dtIni, $dtFim);
        $listCaixaPrazo = $this->buildListCaixaPrazo($dtIni, $dtFim);

        $lists = [];
        $lists['caixaVista'] = ['titulo' => 'Caixa a vista', 'itens' => $listCaixaVista];
        $lists['caixaPrazo'] = ['titulo' => 'Caixa a prazo', 'itens' => $listCaixaPrazo];

        $lists['credito_cieloCTPL'] = ['titulo' => 'Cielo CTPL - Créditos', 'itens' => $this->buildListCartao($dtIni, $dtFim, 9, 30, 'TOTAL CIELO CTPL - CRÉDITOS')];
        $lists['credito_cieloMSP'] = ['titulo' => 'Cielo MSP - Créditos', 'itens' => $this->buildListCartao($dtIni, $dtFim, 9, 32, 'TOTAL CIELO MSP - CRÉDITOS')];
        $lists['credito_moderninha'] = ['titulo' => 'Moderninha - Créditos', 'itens' => $this->buildListCartao($dtIni, $dtFim, 9, 33, 'TOTAL PAGSEGURO MODERNINHA IPÊ - CRÉDITOS')];
        $lists['credito_stoneCTPL'] = ['titulo' => 'Stone CTPL - Créditos', 'itens' => $this->buildListCartao($dtIni, $dtFim, 9, 34, 'TOTAL STONE - CRÉDITOS')];
        $lists['credito_stoneMSP'] = ['titulo' => 'Stone MSP - Créditos', 'itens' => $this->buildListCartao($dtIni, $dtFim, 9, 35, 'TOTAL STONE MSP - CRÉDITOS')];
        $lists['credito_rdcard'] = ['titulo' => 'Redecard - Créditos', 'itens' => $this->buildListCartao($dtIni, $dtFim, 9, 31, 'TOTAL RDCARD - CRÉDITOS')];


        $lists['debito_cieloCTPL'] = ['titulo' => 'Cielo CTPL - Débitos', 'itens' => $this->buildListCartao($dtIni, $dtFim, 10, 30, 'TOTAL CIELO CTPL - DÉBITOS')];
        $lists['debito_cieloMSP'] = ['titulo' => 'Cielo MSP - Débitos', 'itens' => $this->buildListCartao($dtIni, $dtFim, 10, 32, 'TOTAL CIELO MSP - DÉBITOS')];
        $lists['debito_moderninha'] = ['titulo' => 'Moderninha - Débitos', 'itens' => $this->buildListCartao($dtIni, $dtFim, 10, 33, 'TOTAL PAGSEGURO MODERNINHA IPÊ - DÉBITOS')];
        $lists['debito_stoneCTPL'] = ['titulo' => 'Stone CTPL - Débitos', 'itens' => $this->buildListCartao($dtIni, $dtFim, 10, 34, 'TOTAL STONE - DÉBITOS')];
        $lists['debito_stoneMSP'] = ['titulo' => 'Stone MSP - Débitos', 'itens' => $this->buildListCartao($dtIni, $dtFim, 10, 35, 'TOTAL STONE MSP - DÉBITOS')];
        $lists['debito_rdcard'] = ['titulo' => 'Redecard - Débitos', 'itens' => $this->buildListCartao($dtIni, $dtFim, 10, 31, 'TOTAL RDCARD - DÉBITOS')];


        $lists['grupos'] = ['titulo' => 'Grupos de Movimentações', 'itens' => $this->buildListGrupos($dtFim)];

        $lists['transfs199e299'] = ['titulo' => 'Transferências entre Carteiras', 'itens' => $this->buildList199e299($dtIni, $dtFim)];


        return $lists;
    }

    /**
     * @param $rcDescricao
     * @param $totalComparado
     * @param \DateTime $dt
     * @return array
     */
    public function addLinhaRegistroConferencia($rcDescricao, $totalComparado, \DateTime $dt)
    {
        $totalComparado = $totalComparado ? $totalComparado : 0.0;
        $dt->setTime(0, 0, 0, 0);
        $registroConferencia = $this->doctrine->getRepository(RegistroConferencia::class)->findOneBy(['descricao' => $rcDescricao, 'dtRegistro' => $dt]);

        if (!$registroConferencia or is_nan($registroConferencia->getValor())) {
            return ['titulo' => $rcDescricao . ' (INFORMADO)',
                'valor' => 0,
                'icon' => $this->chooseIcon($totalComparado, null)];
        } else {
            $dif = $registroConferencia->getValor() - $totalComparado;
            return ['titulo' => $rcDescricao . ' (INFORMADO)',
                'valor' => $registroConferencia->getValor(),
                'icon' => $this->chooseIcon($totalComparado, $registroConferencia),
                'obs' => '(DIF: ' . $dif . ')'];
        }
    }


    /**
     * @param $valor
     * @param RegistroConferencia $rc
     * @return mixed
     */
    public function chooseIcon($valor, ?RegistroConferencia $rc)
    {
        $icone = null;
        if ($valor and $rc) {
            if ($valor == $rc->getValor()) {
                $icone = "checked";
            } else {
                if ($rc->getObs() != null) {
                    $icone = "attention";
                } else {
                    $icone = "cancel";
                }
            }
        } else {
            $icone = "checked";
        }

        return $icone;
    }

    /**
     * @param \DateTime $dtIni
     * @param \DateTime $dtFim
     * @return mixed
     */
    private function buildListCaixaVista(\DateTime $dtIni, \DateTime $dtFim)
    {
        $list = [];
        $c101 = $this->doctrine->getRepository(Categoria::class)->findOneBy(['codigo' => 101]);
        $c102 = $this->doctrine->getRepository(Categoria::class)->findOneBy(['codigo' => 102]);

        // ------- CAIXA A VISTA (BONSUCESSO) -------
        $caixaAVista = $this->doctrine->getRepository(Carteira::class)->findOneBy(['descricao' => 'CAIXA A VISTA']);

        $tCaixaAvista101 = $this->doctrine->getRepository(Movimentacao::class)->findTotal($dtIni, $dtFim, $caixaAVista, $c101);

        $list[] = ['titulo' => 'TOTAL ENTRADAS (1.01) - CAIXA A VISTA',
            'valor' => $tCaixaAvista101];

        $tCaixaAvista102 = $this->doctrine->getRepository(Movimentacao::class)->findTotal($dtIni, $dtFim, $caixaAVista, $c102);

        $list[] = ['titulo' => 'TOTAL ENTRADAS (1.02) - CAIXA A VISTA',
            'valor' => $tCaixaAvista102];

        // Linha para Registro de Conferência
        $list[] = $this->addLinhaRegistroConferencia('TOTAL CAIXA A VISTA (BONSUCESSO)', $tCaixaAvista101, $dtFim);

        $cAjustesDeCaixaPos = $this->doctrine->getRepository(Categoria::class)->findOneBy(['codigo' => 151]);
        $cAjustesDeCaixaNeg = $this->doctrine->getRepository(Categoria::class)->findOneBy(['codigo' => 251]);

        $tAjustesCaixaAvistaPos = $this->doctrine->getRepository(Movimentacao::class)->findTotal($dtIni, $dtFim, $caixaAVista, $cAjustesDeCaixaPos);
        $tAjustesCaixaAvistaNeg = $this->doctrine->getRepository(Movimentacao::class)->findTotal($dtIni, $dtFim, $caixaAVista, $cAjustesDeCaixaNeg);

        $obs = $tAjustesCaixaAvistaPos . '(+) . ' . $tAjustesCaixaAvistaNeg . '(-)';

        $tAjustesAvista = $tAjustesCaixaAvistaPos - $tAjustesCaixaAvistaNeg;
        $icone = $tAjustesAvista == 0 ? 'checked' : 'attention';

        $list[] = ['titulo' => 'TOTAL AJUSTES - CAIXA A VISTA',
            'valor' => $tAjustesAvista,
            'icone' => $icone,
            'obs' => $obs];

        $tVendasEKT = $this->doctrine->getRepository(Venda::class)->findTotalAVistaEKT($dtIni, $dtFim, true);

        if ($tVendasEKT) {
            $dif = $tCaixaAvista101 - $tVendasEKT;
            $icone = $tVendasEKT == $tCaixaAvista101 ? 'checked' : 'attention';
            $obs = "(DIF: " . $dif . ")";
            $list[] = ['titulo' => 'TOTAL EKT - CAIXA A VISTA',
                'valor' => $tVendasEKT,
                'icone' => $icone,
                'obs' => $obs];
        }

        return $list;
    }

    /**
     * @param \DateTime $dtIni
     * @param \DateTime $dtFim
     * @return mixed
     */
    private function buildListCaixaPrazo(\DateTime $dtIni, \DateTime $dtFim)
    {
        $list = [];
        $c101 = $this->doctrine->getRepository(Categoria::class)->findOneBy(['codigo' => 101]);
        $c102 = $this->doctrine->getRepository(Categoria::class)->findOneBy(['codigo' => 102]);

        // ------- CAIXA A PRAZO -------

        $caixaAPrazo = $this->doctrine->getRepository(Carteira::class)->findOneBy(['descricao' => 'CAIXA A PRAZO']);

        $tCaixaAprazo = $this->doctrine->getRepository(Movimentacao::class)->findTotal($dtIni, $dtFim, $caixaAPrazo, $c101);
        $tCaixaAprazoExternas = $this->doctrine->getRepository(Movimentacao::class)->findTotal($dtIni, $dtFim, $caixaAPrazo, $c102);

        $list[] = ['titulo' => 'TOTAL ENTRADAS (1.01) - CAIXA A PRAZO',
            'valor' => $tCaixaAprazo];

        $list[] = $this->addLinhaRegistroConferencia('TOTAL CAIXA A PRAZO - SERVIPA', $tCaixaAprazo, $dtFim);

        $list[] = ['titulo' => 'TOTAL ENTRADAS (1.02) - CAIXA A PRAZO',
            'valor' => $tCaixaAprazoExternas];

        $list[] = $this->addLinhaRegistroConferencia('TOTAL CAIXA A PRAZO - OUTROS RECEB', $tCaixaAprazoExternas, $dtFim);

        $cAjustesDeCaixaPos = $this->doctrine->getRepository(Categoria::class)->findOneBy(['codigo' => 151]);
        $cAjustesDeCaixaNeg = $this->doctrine->getRepository(Categoria::class)->findOneBy(['codigo' => 251]);

        $tAjustesCaixaAprazoPos = $this->doctrine->getRepository(Movimentacao::class)->findTotal($dtIni, $dtFim, $caixaAPrazo, $cAjustesDeCaixaPos);
        $tAjustesCaixaAprazoNeg = $this->doctrine->getRepository(Movimentacao::class)->findTotal($dtIni, $dtFim, $caixaAPrazo, $cAjustesDeCaixaNeg);

        $obsAjustesCaixaPrazo = $tAjustesCaixaAprazoPos . "(+) . " . $tAjustesCaixaAprazoNeg . "(-)";


        $tAjustesAprazo = $tAjustesCaixaAprazoPos - $tAjustesCaixaAprazoNeg;
        $icone = $tAjustesAprazo == 0.0 ? 'checked' : 'attention';

        $list[] = ['titulo' => 'TOTAL AJUSTES - CAIXA A PRAZO',
            'valor' => $tAjustesAprazo,
            'icone' => $icone,
            'obs' => $obsAjustesCaixaPrazo];

        return $list;

    }


    /**
     * @param \DateTime $dtIni
     * @param \DateTime $dtFim
     * @param $modoCodigo
     * @param $carteiraCodigo
     * @param $titulo
     * @return array
     * @throws \Exception
     */
    public function buildListCartao(\DateTime $dtIni, \DateTime $dtFim, $modoCodigo, $carteiraCodigo, $titulo)
    {
        $c101 = $this->doctrine->getRepository(Categoria::class)->findOneBy(['codigo' => 101]);
        $c102 = $this->doctrine->getRepository(Categoria::class)->findOneBy(['codigo' => 102]);
        $carteira = $this->doctrine->getRepository(Carteira::class)->findOneBy(['codigo' => $carteiraCodigo]);
        if (!$carteira) {
            throw new \Exception("Carteira não encontrada para código $carteiraCodigo");
        }

        $modo = $this->doctrine->getRepository(Modo::class)->findOneBy(['codigo' => $modoCodigo]);

        $t101 = $this->doctrine->getRepository(Movimentacao::class)->findTotal($dtIni, $dtFim, $carteira, $c101, $modo);
        $t102 = $this->doctrine->getRepository(Movimentacao::class)->findTotal($dtIni, $dtFim, $carteira, $c102, $modo);

        $total = $t101 + $t102;

        $list[] = ['titulo' => $titulo, 'valor' => $total];
        $list[] = $this->addLinhaRegistroConferencia($titulo, $total, $dtFim);

        $taxa = $this->movimentacaoBusiness->calcularTaxaCartao($carteira, false, $total, $dtIni, $dtFim);

        $icone = $taxa > 0.0001 ? "checked" : "cancel";

        $list[] = ['titulo' => 'TAXA',
            'valor' => $taxa,
            'icone' => $icone];

        return $list;
    }

    public function buildListGrupos($dtFim)
    {
        $gruposItens = $this->doctrine->getRepository(GrupoItem::class)->findByMesAno($dtFim);

        $list = [];

        foreach ($gruposItens as $gi) {
            if (!$gi) {
                continue;
            }
            $valorLanctos = $gi->getValorLanctos();
            $valorInformado = $gi->getValorInformado();

            $icone = $valorLanctos == $valorInformado ? "checked" : "cancel";

            $list[] = ['titulo' => "TOTAL LANÇADO - " . $gi->getPai()->getDescricao(), 'valor' => $valorLanctos, 'icone' => $icone];
            $list[] = ['titulo' => "TOTAL INFORMADO - " . $gi->getPai()->getDescricao(), 'valor' => $valorLanctos, 'icone' => $icone];
        }

        return $list;
    }

    /**
     * Resumo de TRANSFERÊNCIAS ENTRE CARTEIRAS.
     *
     * @return
     * @throws ViewException
     */
    public function buildList199e299(\DateTime $dtIni, \DateTime $dtFim)
    {
        $c199 = $this->doctrine->getRepository(Categoria::class)->findOneBy(['codigo' => 199]);
        $c299 = $this->doctrine->getRepository(Categoria::class)->findOneBy(['codigo' => 299]);

        $t299 = $this->doctrine->getRepository(Movimentacao::class)->findTotal($dtIni, $dtFim, null, $c299);
        $t199 = $this->doctrine->getRepository(Movimentacao::class)->findTotal($dtIni, $dtFim, null, $c199);

        $icone = $t199 == $t299 ? "checked" : "cancel";

        $list = [];
        $list[] = ['titulo' => "TOTAL - " . $c299->getPai()->getDescricao(), 'valor' => $t299, 'icone' => $icone];
        $list[] = ['titulo' => "TOTAL - " . $c199->getPai()->getDescricao(), 'valor' => $t199, 'icone' => $icone];

        return $list;
    }


}