<?php

namespace App\Controller\Financeiro;


use App\Controller\FormListController;
use App\Entity\Financeiro\OperadoraCartao;
use App\EntityHandler\EntityHandler;
use App\EntityHandler\Financeiro\OperadoraCartaoEntityHandler;
use App\Form\Financeiro\OperadoraCartaoType;
use App\Utils\Repository\FilterData;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class 3CartaoController
 * @package App\Controller\Financeiro
 * @author Carlos Eduardo Pauluk
 */
class OperadoraCartaoController extends FormListController
{


    private $entityHandler;

    public function __construct(OperadoraCartaoEntityHandler $entityHandler)
    {
        $this->entityHandler = $entityHandler;
    }

    public function getEntityHandler(): ?EntityHandler
    {
        return $this->entityHandler;
    }

    public function getFormRoute()
    {
        return 'fin_operadoraCartao_form';
    }

    public function getFormView()
    {
        return 'Financeiro/operadoraCartaoForm.html.twig';
    }

    public function getFilterDatas($params)
    {
        return array(
            new FilterData('descricao', 'LIKE', $params['filter']['descricao'])
        );
    }

    public function getListView()
    {
        return 'Financeiro/operadoraCartaoList.html.twig';
    }

    public function getListRoute()
    {
        return 'fin_bandeiraCartao_list';
    }


    public function getTypeClass()
    {
        return OperadoraCartaoType::class;
    }

    /**
     *
     * @Route("/fin/operadoraCartao/form/{id}", name="fin_operadoraCartao_form", defaults={"id"=null}, requirements={"id"="\d+"})
     * @param Request $request
     * @param OperadoraCartao|null $operadoraCartao
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function form(Request $request, OperadoraCartao $operadoraCartao = null)
    {
        return $this->doForm($request, $operadoraCartao);
    }

    /**
     *
     * @Route("/fin/operadoraCartao/list/", name="fin_operadoraCartao_list")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function list(Request $request)
    {
        return $this->doList($request);
    }

    /**
     *
     * @Route("/fin/operadoraCartao/delete/{id}/", name="fin_operadoraCartao_delete", requirements={"id"="\d+"})
     * @param Request $request
     * @param OperadoraCartao $operadoraCartao
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function delete(Request $request, OperadoraCartao $operadoraCartao)
    {
        return $this->doDelete($request, $operadoraCartao);
    }


}