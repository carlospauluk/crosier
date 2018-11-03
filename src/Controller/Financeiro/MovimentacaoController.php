<?php

namespace App\Controller\Financeiro;

use App\Entity\Financeiro\Cadeia;
use App\Entity\Financeiro\GrupoItem;
use App\Entity\Financeiro\Movimentacao;
use App\Entity\Financeiro\Parcelamento;
use App\Form\Financeiro\MovimentacaoType;
use App\Utils\ExceptionUtils;
use App\Utils\Repository\FilterData;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class MovimentacaoController.
 *
 *
 *
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
     * @throws \Exception
     */
    public function form(Request $request, Movimentacao $movimentacao = null)
    {
        $tipoLancto = $request->get('tipoLancto');

        // Se está tentando acessar uma url de uma movimentação que não existe
        if ($request->get('id') !== null and $movimentacao == null) {
            return $this->redirectToRoute('fin_movimentacao_form');
        }
        if ($movimentacao) {
            // Verifica se está editando uma transfPropria pela 1.99
            $m = $this->getBusiness()->checkEditTransfPropria($movimentacao);
            if ($m) {
                return $this->redirectToRoute('fin_movimentacao_form', ['id' => $m->getId()]);
            }
        }

        if  (!$movimentacao) {
            $movimentacao = new Movimentacao();
            $movimentacao->setTipoLancto($tipoLancto);
        }



        $vParams['exibirRecorrente'] = $this->getBusiness()->exibirRecorrente($movimentacao);

        return $this->doForm($request, $movimentacao, $vParams);
    }

    /**
     *
     * @Route("/fin/movimentacao/list", name="fin_movimentacao_list")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
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
     * @Route("/fin/movimentacao/getTiposLanctos", name="fin_movimentacao_getTiposLanctos")
     * @param Request $request
     * @param Movimentacao $movimentacao
     * @return JsonResponse
     */
    public function getTiposLanctos(Request $request)
    {
        parse_str(urldecode($request->get('formMovimentacao')), $formMovimentacao);
        $tiposLanctos = $this->getBusiness()->getTiposLanctos($formMovimentacao['movimentacao']);
        $response = new JsonResponse(array('tiposLanctos' => $tiposLanctos));
        return $response;
    }

    /**
     *
     * @Route("/fin/movimentacao/listParcelamento/{parcelamento}", name="fin_movimentacao_listParcelamento", requirements={"parcelamento"="\d+"})
     * @param Parcelamento $parcelamento
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
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
     * @Route("/fin/movimentacao/listCadeia/{cadeia}", name="fin_movimentacao_listCadeia", requirements={"cadeia"="\d+"})
     * @param Cadeia $cadeia
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listCadeia(Cadeia $cadeia)
    {
        $movs = $this->getDoctrine()->getRepository(Movimentacao::class)->findBy(['cadeia' => $cadeia]);

        return $this->render('Financeiro/movimentacaoParcelamentoList.html.twig', ['movs' => $movs]);
    }

    public function getFormPageTitle()
    {
        return "Movimentação";
    }

    public function getListPageTitle()
    {
        return "Movimentações";
    }


}