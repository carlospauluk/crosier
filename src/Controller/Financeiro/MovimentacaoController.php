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

        $form = $this->createForm(MovimentacaoType::class);
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
     * @Route("/fin/movimentacao/formGrupoItem/{grupoItem}/{movimentacao}", name="fin_movimentacao_formGrupoItem", defaults={"movimentacao"=null}, requirements={"grupoItem"="\d+","movimentacao"="\d+"})
     *
     * @param Request $request
     * @param GrupoItem $grupoItem
     * @param Movimentacao $movimentacao
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function formGrupoItem(Request $request, GrupoItem $grupoItem, Movimentacao $movimentacao = null)
    {
        $this->getSecurityBusiness()->checkAccess('fin_movimentacao_form');

        $movimentacaoForm = $request->request->get('movimentacao');
        if ($movimentacaoForm) {
            $movimentacaoForm['valorTotal'] = 0.0;
            $movimentacaoForm['dtUtil'] = '01/01/1900';
            $movimentacaoForm['dtVencto'] = '01/01/1900';
            $movimentacaoForm['dtVenctoEfetiva'] = '01/01/1900';
            $movimentacaoForm['centroCusto'] = 1;
            $request->request->set('movimentacao', $movimentacaoForm);
        }
        $exibirRecorrente = $this->getBusiness()->exibirRecorrente($movimentacao);
        $parameters = [];
        $parameters['exibirRecorrente'] = $exibirRecorrente;
        $parameters['grupoItem'] = $grupoItem;

        if (!$movimentacao) {
            $movimentacao = new Movimentacao();
            $movimentacao->setGrupoItem($grupoItem);
        }

        $form = $this->createForm(MovimentacaoType::class, $movimentacao);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                try {
                    $entity = $form->getData();
                    $this->getEntityHandler()->save($entity);
                    $this->addFlash('success', 'Registro salvo com sucesso!');
                    return $this->redirectToRoute('fin_movimentacao_formGrupoItem', array('grupoItem' => $grupoItem->getId(), 'movimentacao' => $movimentacao->getId()));
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
        $parameters['page_title'] = $grupoItem->getDescricao();
        return $this->render('Financeiro/movimentacaoFormGrupoItem.html.twig', $parameters);
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

    public function getFormPageTitle() {
        return "Movimentação";
    }

    public function getListPageTitle()
    {
        return "Movimentações";
    }


}