<?php

namespace App\Controller\Financeiro;


use App\Controller\FormListController;
use App\Entity\Financeiro\BandeiraCartao;
use App\EntityHandler\EntityHandler;
use App\EntityHandler\Financeiro\BandeiraCartaoEntityHandler;
use App\Form\Financeiro\BandeiraCartaoType;
use App\Utils\Repository\FilterData;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class BandeiraCartaoController
 * @package App\Controller\Financeiro
 * @author Carlos Eduardo Pauluk
 */
class BandeiraCartaoController extends FormListController
{


    private $entityHandler;

    public function __construct(BandeiraCartaoEntityHandler $entityHandler)
    {
        $this->entityHandler = $entityHandler;
    }

    public function getEntityHandler(): ?EntityHandler
    {
        return $this->entityHandler;
    }

    public function getFormRoute()
    {
        return 'fin_bandeiraCartao_form';
    }

    public function getFormView()
    {
        return 'Financeiro/bandeiraCartaoForm.html.twig';
    }

    public function getFilterDatas($params)
    {
        return array(
            new FilterData('descricao', 'LIKE', $params['filter']['descricao'])
        );
    }

    public function getListView()
    {
        return 'Financeiro/bandeiraCartaoList.html.twig';
    }

    public function getListRoute()
    {
        return 'fin_bandeiraCartao_list';
    }


    public function getTypeClass()
    {
        return BandeiraCartaoType::class;
    }

    /**
     *
     * @Route("/fin/bandeiraCartao/form/{id}", name="fin_bandeiraCartao_form", defaults={"id"=null}, requirements={"id"="\d+"})
     * @param Request $request
     * @param BandeiraCartao|null $bandeiraCartao
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function form(Request $request, BandeiraCartao $bandeiraCartao = null)
    {
        return $this->doForm($request, $bandeiraCartao);
    }

    /**
     *
     * @Route("/fin/bandeiraCartao/list/", name="fin_bandeiraCartao_list")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function list(Request $request)
    {
        return $this->doList($request);
    }

    /**
     *
     * @Route("/fin/bandeiraCartao/delete/{id}/", name="fin_bandeiraCartao_delete", requirements={"id"="\d+"})
     * @param Request $request
     * @param BandeiraCartao $bandeiraCartao
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function delete(Request $request, BandeiraCartao $bandeiraCartao)
    {
        return $this->doDelete($request, $bandeiraCartao);
    }


}