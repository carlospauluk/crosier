<?php

namespace App\Controller\Financeiro;


use App\Controller\FormListController;
use App\Entity\Financeiro\Banco;
use App\EntityHandler\EntityHandler;
use App\EntityHandler\Financeiro\BancoEntityHandler;
use App\Form\Financeiro\BancoType;
use App\Utils\Repository\FilterData;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class BancoController
 * @package App\Controller\Financeiro
 * @author Carlos Eduardo Pauluk
 */
class BancoController extends FormListController
{


    private $entityHandler;

    public function __construct(BancoEntityHandler $entityHandler)
    {
        $this->entityHandler = $entityHandler;
    }

    public function getEntityHandler(): ?EntityHandler
    {
        return $this->entityHandler;
    }

    public function getFormRoute()
    {
        return 'fin_banco_form';
    }

    public function getFormView()
    {
        return 'Financeiro/bancoForm.html.twig';
    }

    public function getFilterDatas($params)
    {
        return array(
            new FilterData(array('codigoBanco', 'nome'), 'LIKE', $params['filter']['str'])
        );
    }

    public function getListView()
    {
        return 'Financeiro/bancoList.html.twig';
    }

    public function getListRoute()
    {
        return 'fin_banco_list';
    }


    public function getTypeClass()
    {
        return BancoType::class;
    }

    /**
     *
     * @Route("/fin/banco/form/{id}", name="fin_banco_form", defaults={"id"=null}, requirements={"id"="\d+"})
     * @param Request $request
     * @param Banco|null $banco
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function form(Request $request, Banco $banco = null)
    {
        return $this->doForm($request, $banco);
    }

    /**
     *
     * @Route("/fin/banco/list/", name="fin_banco_list")
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
                'codigoBanco',
                'nome'
            )
        );
    }

    /**
     *
     * @Route("/fin/banco/datatablesJsList/", name="fin_banco_datatablesJsList")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function datatablesJsList(Request $request)
    {
        $jsonResponse = $this->doDatatablesJsList($request);
        return $jsonResponse;
    }

    /**
     *
     * @Route("/fin/banco/delete/{id}/", name="fin_banco_delete", requirements={"id"="\d+"})
     * @param Request $request
     * @param Banco $banco
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function delete(Request $request, Banco $banco)
    {
        return $this->doDelete($request, $banco);
    }


}