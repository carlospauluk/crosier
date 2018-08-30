<?php

namespace App\Controller\Financeiro;

use App\Controller\FormListController;
use App\Entity\Financeiro\Carteira;
use App\EntityHandler\EntityHandler;
use App\EntityHandler\Financeiro\CarteiraEntityHandler;
use App\Form\Financeiro\CarteiraType;
use App\Utils\Repository\FilterData;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CarteiraController
 * @package App\Controller\Financeiro
 * @author Carlos Eduardo Pauluk
 */
class CarteiraController extends FormListController
{


    private $entityHandler;

    public function __construct(CarteiraEntityHandler $entityHandler)
    {
        $this->entityHandler = $entityHandler;
    }

    public function getEntityHandler(): ?EntityHandler
    {
        return $this->entityHandler;
    }

    public function getFormRoute()
    {
        return 'fin_carteira_form';
    }

    public function getFormView()
    {
        return 'Financeiro/carteiraForm.html.twig';
    }

    public function getFilterDatas($params)
    {
        return array(
            new FilterData('descricao', 'LIKE', $params['filter']['descricao']),
            new FilterData('dtConsolidado', 'BETWEEN', $params['filter']['dtConsolidado'])
        );
    }

    public function getListView()
    {
        return 'Financeiro/carteiraList.html.twig';
    }

    public function getListRoute()
    {
        return 'fin_carteira_list';
    }


    public function getTypeClass()
    {
        return CarteiraType::class;
    }

    /**
     *
     * @Route("/fin/carteira/form/{id}", name="fin_carteira_form", defaults={"id"=null}, requirements={"id"="\d+"})
     * @param Request $request
     * @param Carteira|null $carteira
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function form(Request $request, Carteira $carteira = null)
    {
        return $this->doForm($request, $carteira);
    }

    /**
     *
     * @Route("/fin/carteira/list/", name="fin_carteira_list")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function list(Request $request)
    {
        return $this->doList($request);
    }

    /**
     * @return array|mixed
     */
    public function getNormalizeAttributes()
    {
        return array(
            'attributes' => array(
                'id',
                'codigo',
                'descricao',
                'dtConsolidado' => ['timestamp'],
                'limite'
            )
        );
    }

    /**
     *
     * @Route("/fin/carteira/datatablesJsList/", name="fin_carteira_datatablesJsList")
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
     * @Route("/fin/carteira/delete/{id}/", name="fin_carteira_delete", requirements={"id"="\d+"})
     * @param Request $request
     * @param Carteira $carteira
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function delete(Request $request, Carteira $carteira)
    {
        return $this->doDelete($request, $carteira);
    }


}