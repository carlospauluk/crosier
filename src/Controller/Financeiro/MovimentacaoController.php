<?php

namespace App\Controller\Financeiro;

use App\Entity\Financeiro\Movimentacao;
use App\Entity\Financeiro\Parcelamento;
use App\Utils\Repository\FilterData;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class MovimentacaoController
 * @package App\Controller\Financeiro
 * @author Carlos Eduardo Pauluk
 */
class MovimentacaoController extends MovimentacaoBaseController
{

    /**
     *
     * @Route("/fin/movimentacao/form/{id}", name="fin_movimentacao_form", defaults={"id"=null}, requirements={"id"="\d+"})
     * @param Request $request
     * @param Movimentacao|null $movimentacao
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \ReflectionException
     */
    public function form(Request $request, Movimentacao $movimentacao = null)
    {
        return $this->doForm($request, $movimentacao);
    }

    /**
     *
     * @Route("/fin/movimentacao/list/", name="fin_movimentacao_list")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \ReflectionException
     * @throws \Exception
     */
    public function list(Request $request)
    {
        $parameters['filterChoices'] = $this->getFilterChoices();
        return $this->doList($request, $parameters);
    }

    /**
     *
     * @Route("/fin/movimentacao/datatablesJsList/", name="fin_movimentacao_datatablesJsList")
     * @param Request $request
     * @return Response
     */
    public function datatablesJsList(Request $request)
    {
        $jsonResponse = $this->doDatatablesJsList($request);
        return $jsonResponse;
    }

    /**
     *
     * @Route("/fin/movimentacao/delete/{id}/", name="fin_movimentacao_delete", requirements={"id"="\d+"})
     * @param Request $request
     * @param Movimentacao $movimentacao
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function delete(Request $request, Movimentacao $movimentacao)
    {
        return $this->doDelete($request, $movimentacao);
    }

    /**
     *
     * @Route("/fin/movimentacao/listParcelamento/{parcelamento}", name="fin_movimentacao_listParcelamento", requirements={"parcelamento"="\d+"})
     * @param Parcelamento $parcelamento
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listParcelamento(Parcelamento $parcelamento)
    {
        $filterDatas = array(
            new FilterData('parcelamento', 'EQ', $parcelamento)
        );

        $orders = array(
            ['column' => 'e.dtVenctoEfetiva', 'dir' => 'asc']
        );

        $movs = $this->getDoctrine()->getRepository(Movimentacao::class)->findByFilters($filterDatas, $orders, 0, 0);

        $total = $this->getBusiness()->somarMovimentacoes($movs);

        $vParams['movs'] = $movs;
        $vParams['total'] = $total;

        return $this->render('Financeiro/movimentacaoParcelamentoList.html.twig', $vParams);
    }

    /**
     *
     * @Route("/fin/movimentacao/aPagarReceber", name="fin_movimentacao_aPagarReceber")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function aPagarReceber(Request $request)
    {
        return new Response();
    }

    /**
     *
     * @Route("/fin/movimentacao/recorrentes", name="fin_movimentacao_recorrentes")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function recorrentes(Request $request)
    {
        return new Response();
    }

    /**
     *
     * @Route("/fin/movimentacao/caixa", name="fin_movimentacao_caixa")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function caixa(Request $request)
    {
        return new Response();
    }


}