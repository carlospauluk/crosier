<?php

namespace App\Controller\Financeiro;

use App\Controller\FormListController;
use App\EntityHandler\EntityHandler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ConferenciaController
 * @package App\Controller\Financeiro
 * @author Carlos Eduardo Pauluk
 */
class ConferenciaController extends FormListController
{

    /**
     *
     * @Route("/fin/conferencia/list", name="fin_conferencia_list")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function list(Request $request)
    {
        return new Response();
    }


    public function getEntityHandler(): ?EntityHandler
    {
        // TODO: Implement getEntityHandler() method.
    }

    /**
     * Necessário para poder passar para o createForm.
     *
     * @return mixed
     */
    public function getTypeClass()
    {
        // TODO: Implement getTypeClass() method.
    }

    public function getFormRoute()
    {
        // TODO: Implement getFormRoute() method.
    }

    public function getFormView()
    {
        // TODO: Implement getFormView() method.
    }

    public function getFilterDatas($params)
    {
        // TODO: Implement getFilterDatas() method.
    }

    public function getListView()
    {
        // TODO: Implement getListView() method.
    }

    public function getListRoute()
    {
        // TODO: Implement getListRoute() method.
    }
}