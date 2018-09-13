<?php

namespace App\Controller\Financeiro;

use App\Business\Base\DiaUtilBusiness;
use App\Entity\Financeiro\Carteira;
use App\Entity\Financeiro\Movimentacao;
use App\EntityHandler\Financeiro\MovimentacaoEntityHandler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class MovimentacaoController
 * @package App\Controller\Financeiro
 * @author Carlos Eduardo Pauluk
 */
class MovimentacaoExtratoController extends MovimentacaoBaseController
{

    private $diaUtilBusiness;

    private $entityHandler;

    public function __construct(DiaUtilBusiness $diaUtilBusiness, MovimentacaoEntityHandler $entityHandler)
    {
        $this->entityHandler = $entityHandler;
        $this->diaUtilBusiness = $diaUtilBusiness;
    }

    /**
     *
     * @Route("/fin/movimentacao/extrato", name="fin_movimentacao_extrato")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function extrato(Request $request)
    {
        $parameters = $request->query->all();
        if (!array_key_exists('filter', $parameters)) {
            // inicializa para evitar o erro
            $parameters['filter'] = array();
            $parameters['filter']['dtUtil']['i'] = date('Y-m-d');
            $parameters['filter']['dtUtil']['f'] = date('Y-m-d');
            $parameters['filter']['carteira'] = 1;
        } else {

        }

        $filterDatas = $this->getFilterDatas($parameters);

        $carteira = $this->getDoctrine()->getRepository(Carteira::class)->find($parameters['filter']['carteira']);

        $repo = $this->getDoctrine()->getRepository(Movimentacao::class);
        $orders[] = ['column' => 'e.dtUtil', 'dir' => 'asc'];
        $orders[] = ['column' => 'categ.codigo', 'dir' => 'asc'];
        $dados = $repo->findByFilters($filterDatas, $orders, 0, null);

        $dtIni = $parameters['filter']['dtUtil']['i'];
        $dtFim = $parameters['filter']['dtUtil']['f'];

        $dtAnterior = \DateTime::createFromFormat('Y-m-d', $dtIni)->setTime(12, 0, 0, 0)->modify('last day');


        $parameters['anteriores']['movs'] = $repo->findAbertasAnteriores($dtAnterior, $carteira);
        $parameters['anteriores']['saldos'] = $this->calcularSaldos($dtAnterior, $carteira);

        $dia = null;
        $dias = array();
        $i = -1;
        foreach ($dados as $movimentacao) {
            if ($movimentacao->getDtUtil()->format('d/m/Y') != $dia) {
                $i++;
                $dia = $movimentacao->getDtUtil()->format('d/m/Y');
                $dias[$i]['dtUtil'] = $movimentacao->getDtUtil();
                $dias[$i]['saldos'] = $this->calcularSaldos($movimentacao->getDtUtil(), $carteira);
            }
            $dias[$i]['movs'][] = $movimentacao;
        }


        $parameters['dias'] = $dias;
        $parameters['carteira']['options'] = $this->getFilterCarteiraOptions($filterDatas);


        $prox = $this->diaUtilBusiness->incPeriodo(true, $dtIni, $dtFim);
        $ante = $this->diaUtilBusiness->incPeriodo(false, $dtIni, $dtFim);
        $parameters['antePeriodoI'] = $ante['dtIni'];
        $parameters['antePeriodoF'] = $ante['dtFim'];
        $parameters['proxPeriodoI'] = $prox['dtIni'];
        $parameters['proxPeriodoF'] = $prox['dtFim'];


        return $this->render('Financeiro/movimentacaoExtratoList.html.twig', $parameters);
    }

    public function calcularSaldos(\DateTime $data, Carteira $carteira)
    {
        $saldos = array();


        $saldoPosterior = $this->getDoctrine()->getRepository(Movimentacao::class)->findSaldo($data, $carteira, 'SALDO_POSTERIOR_REALIZADAS');
        $saldoPosteriorComCheques = $this->getDoctrine()->getRepository(Movimentacao::class)->findSaldo($data, $carteira, 'SALDO_POSTERIOR_COM_CHEQUES');
        $saldos['SALDO_POSTERIOR_REALIZADAS'] = $saldoPosterior;
        $saldos['SALDO_POSTERIOR_COM_CHEQUES'] = $saldoPosteriorComCheques;


//if (carteira.getLimite() != null) {
//BigDecimal disponivel = BigDecimal.ZERO;
//disponivel = saldoPosteriorComCheques.subtract(carteira.getLimite().negate());
//disponivel = disponivel == null ? BigDecimal.ZERO : disponivel;
//getTotalizacoes().put("SUMARIO_SALDO_DISPONIVEL", disponivel);
//}

        return $saldos;
    }


    public function getFilterCarteiraOptions($params)
    {
        $repoCarteira = $this->getDoctrine()->getRepository(Carteira::class);
        $carteiras = $repoCarteira->findAll('e.codigo');

        $param = null;
        foreach ($params as $p) {
            if ($p->field == 'carteira') {
                $param = $p;
                break;
            }
        }

        $str = "";
        $selected = "";
        foreach ($carteiras as $carteira) {
            if ($param->val) {
                $selected = $carteira->getId() == $param->val ? 'selected="selected"' : '';
            }
            $str .= "<option value=\"" . $carteira->getId() . "\"" . $selected . ">" . $carteira->getCodigo(true) . " - " . $carteira->getDescricao() . "</option>";
        }
        return $str;
    }


}