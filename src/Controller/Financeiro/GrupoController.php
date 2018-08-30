<?php

namespace App\Controller\Financeiro;


use App\Controller\FormListController;
use App\Entity\Financeiro\Grupo;
use App\EntityHandler\EntityHandler;
use App\EntityHandler\Financeiro\GrupoEntityHandler;
use App\Form\Financeiro\GrupoType;
use App\Utils\Repository\FilterData;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class GrupoController
 * @package App\Controller\Financeiro
 * @author Carlos Eduardo Pauluk
 */
class GrupoController extends FormListController
{


    private $entityHandler;

    public function __construct(GrupoEntityHandler $entityHandler)
    {
        $this->entityHandler = $entityHandler;
    }

    public function getEntityHandler(): ?EntityHandler
    {
        return $this->entityHandler;
    }

    public function getFormRoute()
    {
        return 'fin_grupo_form';
    }

    public function getFormView()
    {
        return 'Financeiro/grupoForm.html.twig';
    }

    public function getFilterDatas($params)
    {
        return array(
            new FilterData('descricao', 'LIKE', $params['filter']['descricao'])
        );
    }

    public function getListView()
    {
        return 'Financeiro/grupoList.html.twig';
    }

    public function getListRoute()
    {
        return 'fin_grupo_list';
    }


    public function getTypeClass()
    {
        return GrupoType::class;
    }

    /**
     *
     * @Route("/fin/grupo/form/{id}", name="fin_grupo_form", defaults={"id"=null}, requirements={"id"="\d+"})
     * @param Request $request
     * @param Grupo|null $grupo
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function form(Request $request, Grupo $grupo = null)
    {
        return $this->doForm($request, $grupo);
    }

    /**
     *
     * @Route("/fin/grupo/list/", name="fin_grupo_list")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function list(Request $request)
    {
        return $this->doList($request);
    }

    /**
     *
     * @Route("/fin/grupo/delete/{id}/", name="fin_grupo_delete", requirements={"id"="\d+"})
     * @param Request $request
     * @param Grupo $grupo
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function delete(Request $request, Grupo $grupo)
    {
        return $this->doDelete($request, $grupo);
    }


}