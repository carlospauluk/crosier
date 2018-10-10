<?php

namespace App\Controller\Financeiro;

use App\Business\Financeiro\ConferenciaFinanceiroBusiness;
use App\Utils\DateTimeUtils;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ConferenciaFinanceiroController extends Controller
{

    private $business;
    /**
     * ConferenciaFinanceiroController constructor.
     */
    public function __construct(ConferenciaFinanceiroBusiness $business)
    {
        $this->business = $business;
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
        $dtIni =  \DateTime::createFromFormat('Y-m-d', $request->get('dtIni'));
        $dtFim = \DateTime::createFromFormat('Y-m-d', $request->get('dtFim'));

        $params = [];
        $params['lists'] = $this->business->buildLists($dtIni, $dtFim);
        return $this->render('Financeiro/conferenciaFinanceiro.html.twig', $params);
    }

}