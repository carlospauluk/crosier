<?php


namespace App\Business\Financeiro;

use App\Entity\Financeiro\Carteira;
use App\Entity\Financeiro\Categoria;
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


    /**
     * ConferenciaFinanceiroBusiness constructor.
     * @param RegistryInterface $doctrine
     */
    public function __construct(RegistryInterface $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * @param \DateTime $dtIni
     * @param \DateTime $dtFim
     * @return array
     */
    public function buildLists(\DateTime $dtIni, \DateTime $dtFim)
    {
        $listCaixas = $this->buildListCaixas($dtIni, $dtFim);
//        $listCartoesDebito = $this->buildListCartoesDebito($dtIni, $dtFim);
//        $listCartoesCredito = $this->buildListCartoesCredito($dtIni, $dtFim);
//        $listGruposMoviment = $this->buildListGruposMoviment($dtIni, $dtFim);
//        $listTransfsCarteiras = $this->buildListTransfsCarteiras($dtIni, $dtFim);

        $lists = [];
        $lists['caixas'] = $listCaixas;
//        $lists['cartoesDebito'] = $listCartoesDebito;
//        $lists['cartoesCredito'] = $listCartoesCredito;
//        $lists['gruposMoviment'] = $listGruposMoviment;
//        $lists['transfsCarteiras'] = $listTransfsCarteiras;

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
    private function buildListCaixas(\DateTime $dtIni, \DateTime $dtFim)
    {
        $list = [];
        $c101 = $this->doctrine->getRepository(Categoria::class)->findOneBy(['codigo' => 101]);
        $c102 = $this->doctrine->getRepository(Categoria::class)->findOneBy(['codigo' => 102]);

        // ------- CAIXA A VISTA (BONSUCESSO) -------
        $caixaAVista = $this->doctrine->getRepository(Carteira::class)->findOneBy(['descricao' => 'CAIXA A VISTA']);

        $tCaixaAvista101 = $this->doctrine->getRepository(Movimentacao::class)->findTotal($caixaAVista, $c101, $dtIni, $dtFim);

        $list[] = ['titulo' => 'TOTAL ENTRADAS (1.01) - CAIXA A VISTA',
            'valor' => $tCaixaAvista101];

        $tCaixaAvista102 = $this->doctrine->getRepository(Movimentacao::class)->findTotal($caixaAVista, $c102, $dtIni, $dtFim);

        $list[] = ['titulo' => 'TOTAL ENTRADAS (1.02) - CAIXA A VISTA',
            'valor' => $tCaixaAvista102];

        // Linha para Registro de Conferência
        $list[] = $this->addLinhaRegistroConferencia('TOTAL CAIXA A VISTA (BONSUCESSO)', $tCaixaAvista101, $dtFim);

        $cAjustesDeCaixaPos = $this->doctrine->getRepository(Categoria::class)->findOneBy(['codigo' => 151]);
        $cAjustesDeCaixaNeg = $this->doctrine->getRepository(Categoria::class)->findOneBy(['codigo' => 251]);

        $tAjustesCaixaAvistaPos = $this->doctrine->getRepository(Movimentacao::class)->findTotal($caixaAVista, $cAjustesDeCaixaPos, $dtIni, $dtFim);
        $tAjustesCaixaAvistaNeg = $this->doctrine->getRepository(Movimentacao::class)->findTotal($caixaAVista, $cAjustesDeCaixaNeg, $dtIni, $dtFim);

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


        // ------- CAIXA A PRAZO -------

        $caixaAPrazo = $this->doctrine->getRepository(Carteira::class)->findOneBy(['descricao' => 'CAIXA A PRAZO']);

        $tCaixaAprazo = $this->doctrine->getRepository(Movimentacao::class)->findTotal($caixaAPrazo, $c101, $dtIni, $dtFim);
        $tCaixaAprazoExternas = $this->doctrine->getRepository(Movimentacao::class)->findTotal($caixaAPrazo, $c102, $dtIni, $dtFim);

        $list[] = ['titulo' => 'TOTAL ENTRADAS (1.01) - CAIXA A PRAZO',
            'valor' => $tCaixaAprazo];

        $list[] = $this->addLinhaRegistroConferencia('TOTAL CAIXA A PRAZO - SERVIPA', $tCaixaAprazo, $dtFim);

        $list[] = ['titulo' => 'TOTAL ENTRADAS (1.02) - CAIXA A PRAZO',
            'valor' => $tCaixaAprazoExternas];

        $list[] = $this->addLinhaRegistroConferencia('TOTAL CAIXA A PRAZO - OUTROS RECEB', $tCaixaAprazoExternas, $dtFim);


        $tAjustesCaixaAprazoPos = $this->doctrine->getRepository(Movimentacao::class)->findTotal($caixaAPrazo, $cAjustesDeCaixaPos, $dtIni, $dtFim);
        $tAjustesCaixaAprazoNeg = $this->doctrine->getRepository(Movimentacao::class)->findTotal($caixaAPrazo, $cAjustesDeCaixaNeg, $dtIni, $dtFim);

        $obsAjustesCaixaPrazo = $tAjustesCaixaAprazoPos . "(+) . " . $tAjustesCaixaAprazoNeg . "(-)";


		$tAjustesAprazo = $tAjustesCaixaAprazoPos - $tAjustesCaixaAprazoNeg;
		$icone = $tAjustesAprazo == 0.0 ? 'checked' : 'attention';

		 $list[] = ['titulo' => 'TOTAL AJUSTES - CAIXA A PRAZO',
             'valor' => $tAjustesAprazo,
             'icone' => $icone,
             'obs' => $obsAjustesCaixaPrazo];

		return $list;

    }

    private function buildListCartoesDebito(\DateTime $dtIni, \DateTime $dtFim)
    {
    }

    private function buildListCartoesCredito(\DateTime $dtIni, \DateTime $dtFim)
    {
    }

    private function buildListGruposMoviment(\DateTime $dtIni, \DateTime $dtFim)
    {
    }

    private function buildListTransfsCarteiras(\DateTime $dtIni, \DateTime $dtFim)
    {
    }

}