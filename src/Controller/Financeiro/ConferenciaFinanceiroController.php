<?php

namespace App\Controller\Financeiro;

use App\Business\Base\DiaUtilBusiness;
use App\Business\Financeiro\ConferenciaFinanceiroBusiness;
use App\Utils\DateTimeUtils;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ConferenciaFinanceiroController extends Controller
{

    private $business;

    private $diaUtilBusiness;

    /**
     * ConferenciaFinanceiroController constructor.
     * @param ConferenciaFinanceiroBusiness $business
     * @param DiaUtilBusiness $diaUtilBusiness
     */
    public function __construct(ConferenciaFinanceiroBusiness $business, DiaUtilBusiness $diaUtilBusiness)
    {
        $this->business = $business;
        $this->diaUtilBusiness = $diaUtilBusiness;
    }

    /**
     *
     * @Route("/fin/conferencia/list/", name="fin_conferencia_list")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function list(Request $request)
    {
        $vParams = $request->query->all();
        if ($request->get('btnMesAtual') or !array_key_exists('filter', $vParams)) {
            // inicializa para evitar o erro
            $hj = new \DateTime();
            $vParams['filter'] = [];
            $vParams['filter']['dtUtil']['i'] = $hj->format('Y-m-') . '01';
            $vParams['filter']['dtUtil']['f'] = $hj->format('Y-m-t');
        }

        $dtIni = \DateTime::createFromFormat('Y-m-d', $vParams['filter']['dtUtil']['i']);
        $dtFim = \DateTime::createFromFormat('Y-m-d', $vParams['filter']['dtUtil']['f']);

        $vParams['lists'] = $this->business->buildLists($dtIni, $dtFim);

        $prox = $this->diaUtilBusiness->incPeriodo(true, $dtIni, $dtFim);
        $ante = $this->diaUtilBusiness->incPeriodo(false, $dtIni, $dtFim);
        $vParams['antePeriodoI'] = $ante['dtIni'];
        $vParams['antePeriodoF'] = $ante['dtFim'];
        $vParams['proxPeriodoI'] = $prox['dtIni'];
        $vParams['proxPeriodoF'] = $prox['dtFim'];

        return $this->render('Financeiro/conferenciaFinanceiro.html.twig', $vParams);
    }

}