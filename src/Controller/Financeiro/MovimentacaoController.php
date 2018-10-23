<?php

namespace App\Controller\Financeiro;

use App\Entity\Financeiro\Cadeia;
use App\Entity\Financeiro\Movimentacao;
use App\Entity\Financeiro\Parcelamento;
use App\Form\Financeiro\MovimentacaoTransfPropriaType;
use App\Utils\ExceptionUtils;
use App\Utils\Repository\FilterData;
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
     * Exibe a seleção para os tipos de lançamento possíveis.
     *
     * @Route("/fin/movimentacao/formIni", name="fin_movimentacao_formIni")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function ini(Request $request)
    {
        if ($request->get('tipoLancto')) {
            $tiposLanctos = $this->getBusiness()->getTiposLancto();
            $tipoLancto = $tiposLanctos[$request->get('tipoLancto')];
            $rota = $tipoLancto['route'];
            return $this->redirectToRoute($rota);
        }
        $parameters['page_title'] = 'Nova Movimentação';
        $parameters['tiposLancto'] = $this->getBusiness()->getTiposLancto();
        return $this->render('Financeiro/movimentacaoFormIni.html.twig', $parameters);
    }

    /**
     *
     * @Route("/fin/movimentacao/form/{id}", name="fin_movimentacao_form", defaults={"id"=null}, requirements={"id"="\d+"})
     * @param Request $request
     * @param Movimentacao|null $movimentacaoForm
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function form(Request $request, Movimentacao $movimentacao = null)
    {
        $movimentacaoForm = $request->request->get('movimentacao');
        if ($movimentacaoForm) {
            $movimentacaoForm['valorTotal'] = 0.0;
            $movimentacaoForm['dtUtil'] = '01/01/1900';
            $movimentacaoForm['centroCusto'] = 1;
            $request->request->set('movimentacao', $movimentacaoForm);
        }
        $exibirRecorrente = $this->getBusiness()->exibirRecorrente($movimentacao);
        return $this->doForm($request, $movimentacao, ['exibirRecorrente' => $exibirRecorrente]);
    }

    /**
     *
     * @Route("/fin/movimentacao/formTransfPropria", name="fin_movimentacao_formTransfPropria")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function formTransfPropria(Request $request)
    {
        $this->getSecurityBusiness()->checkAccess('fin_movimentacao_form');

        $movimentacao = $request->get('movimentacao_transf_propria');
        if ($movimentacao) {
            $movimentacao['valorTotal'] = 0.0;
            $movimentacao['dtVencto'] = $movimentacao['dtMoviment'];
            $movimentacao['dtVenctoEfetiva'] = $movimentacao['dtMoviment'];
            $movimentacao['dtPagto'] = $movimentacao['dtMoviment'];
            $movimentacao['dtUtil'] = '01/01/1900';
            $movimentacao['centroCusto'] = 1;
            $request->request->set('movimentacao_transf_propria', $movimentacao);
        }

        $form = $this->createForm(MovimentacaoTransfPropriaType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                try {
                    $entity = $form->getData();
                    $entity = $this->getEntityHandler()->save($entity);
                    $cadeia = $entity->getCadeia();
                    $this->addFlash('success', 'Registro salvo com sucesso!');
                    return $this->redirectToRoute('fin_movimentacao_list', ['filter' => ['cadeia' => $cadeia->getId()]]);
                } catch (\Exception $e) {
                    $msg = ExceptionUtils::treatException($e);
                    $this->addFlash('error', $msg);
                    $this->addFlash('error', 'Erro ao salvar!');
                }
            } else {
                $errors = $form->getErrors(true, true);
                foreach ($errors as $error) {
                    $this->addFlash('error', $error->getMessage());
                }
            }
        }

        // Pode ou não ter vindo algo no $parameters. Independentemente disto, só adiciono form e foi-se.
        $parameters['form'] = $form->createView();
        $parameters['page_title'] = 'Transferência Própria';
        return $this->render('Financeiro/movimentacaoFormTransfPropria.html.twig', $parameters);
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
     * @Route("/fin/movimentacao/listCadeia/{cadeia}", name="fin_movimentacao_listCadeia", requirements={"cadeia"="\d+"})
     * @param Cadeia $cadeia
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listCadeia(Cadeia $cadeia)
    {
        $movs = $this->getDoctrine()->getRepository(Movimentacao::class)->findBy(['cadeia' => $cadeia]);

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

    public function getListPageTitle()
    {
        return "Movimentações";
    }


}