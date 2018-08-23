<?php

namespace App\Controller\Financeiro;


use App\Controller\FormListController;
use App\Entity\Financeiro\Modo;
use App\EntityHandler\EntityHandler;
use App\EntityHandler\Financeiro\ModoEntityHandler;
use App\Form\Financeiro\ModoType;
use App\Utils\Repository\FilterData;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ModoController
 * @package App\Controller\Financeiro
 * @author Carlos Eduardo Pauluk
 */
class ModoController extends FormListController
{


    private $entityHandler;

    public function __construct(ModoEntityHandler $entityHandler)
    {
        $this->entityHandler = $entityHandler;
    }

    public function getEntityHandler(): ?EntityHandler
    {
        return $this->entityHandler;
    }

    public function getFormRoute()
    {
        return 'fin_modo_form';
    }

    public function getFormView()
    {
        return 'Financeiro/modoForm.html.twig';
    }

    public function getFilterDatas($params)
    {
        return array(
            new FilterData('descricao', 'LIKE', $params['filter']['descricao'])
        );
    }

    public function getListView()
    {
        return 'Financeiro/modoList.html.twig';
    }

    public function getListRoute()
    {
        return 'fin_modo_list';
    }


    public function getTypeClass()
    {
        return ModoType::class;
    }

    /**
     *
     * @Route("/fin/modo/form/{id}", name="fin_modo_form", defaults={"id"=null}, requirements={"id"="\d+"})
     * @param Request $request
     * @param MOdo|null $modo
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function form(Request $request, Modo $modo = null)
    {
        return $this->doForm($request, $modo);
    }

    /**
     *
     * @Route("/fin/modo/list/", name="fin_modo_list")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function list(Request $request)
    {
        return $this->doList($request);
    }

    /**
     *
     * @Route("/fin/modo/delete/{id}/", name="fin_modo_delete", requirements={"id"="\d+"})
     * @Method("POST")
     * @param Request $request
     * @param Modo $modo
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function delete(Request $request, Modo $modo)
    {
        return $this->doDelete($request, $modo);
    }


}