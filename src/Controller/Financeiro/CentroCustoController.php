<?php

namespace App\Controller\Financeiro;


use App\Controller\FormListController;
use App\Entity\Financeiro\CentroCusto;
use App\EntityHandler\EntityHandler;
use App\EntityHandler\Financeiro\CentroCustoEntityHandler;
use App\Form\Financeiro\CentroCustoType;
use App\Utils\Repository\FilterData;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CentroCustoController
 * @package App\Controller\Financeiro
 * @author Carlos Eduardo Pauluk
 */
class CentroCustoController extends FormListController
{


    private $entityHandler;

    public function __construct(CentroCustoEntityHandler $entityHandler)
    {
        $this->entityHandler = $entityHandler;
    }

    public function getEntityHandler(): ?EntityHandler
    {
        return $this->entityHandler;
    }

    public function getFormRoute()
    {
        return 'fin_centroCusto_form';
    }

    public function getFormView()
    {
        return 'Financeiro/centroCustoForm.html.twig';
    }

    public function getFilterDatas($params)
    {
        return array(
            new FilterData('descricao', 'LIKE', $params['filter']['descricao'])
        );
    }

    public function getListView()
    {
        return 'Financeiro/centroCustoList.html.twig';
    }

    public function getListRoute()
    {
        return 'fin_centroCusto_list';
    }


    public function getTypeClass()
    {
        return CentroCustoType::class;
    }

    /**
     *
     * @Route("/fin/centroCusto/form/{id}", name="fin_centroCusto_form", defaults={"id"=null}, requirements={"id"="\d+"})
     * @param Request $request
     * @param CentroCusto|null $centroCusto
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function form(Request $request, CentroCusto $centroCusto = null)
    {
        return $this->doForm($request, $centroCusto);
    }

    /**
     *
     * @Route("/fin/centroCusto/list/", name="fin_centroCusto_list")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function list(Request $request)
    {
        return $this->doList($request);
    }

    /**
     *
     * @Route("/fin/centroCusto/delete/{id}/", name="fin_centroCusto_delete", requirements={"id"="\d+"})
     * @Method("POST")
     * @param Request $request
     * @param CentroCusto $centroCusto
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function delete(Request $request, CentroCusto $centroCusto)
    {
        return $this->doDelete($request, $centroCusto);
    }


}