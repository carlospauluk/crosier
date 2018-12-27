<?php

namespace App\Controller\Financeiro;


use App\Controller\FormListController;
use App\Entity\Financeiro\RegistroConferencia;
use App\EntityHandler\EntityHandler;
use App\EntityHandler\Financeiro\RegistroConferenciaEntityHandler;
use App\Form\Financeiro\RegistroConferenciaType;
use App\Utils\Repository\FilterData;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class RegistroConferenciaController
 * @package App\Controller\Financeiro
 * @author Carlos Eduardo Pauluk
 */
class RegistroConferenciaController extends FormListController
{


    private $entityHandler;

    public function __construct(RegistroConferenciaEntityHandler $entityHandler)
    {
        $this->entityHandler = $entityHandler;
    }

    public function getEntityHandler(): ?EntityHandler
    {
        return $this->entityHandler;
    }

    public function getFormRoute()
    {
        return 'fin_registroConferencia_form';
    }

    public function getFormView()
    {
        return 'Financeiro/registroConferenciaForm.html.twig';
    }

    public function getFilterDatas($params)
    {
        return array(
            new FilterData('descricao', 'LIKE', $params['filter']['descricao'])
        );
    }

    public function getListView()
    {
        return 'Financeiro/registroConferenciaList.html.twig';
    }

    public function getListRoute()
    {
        return 'fin_registroConferencia_list';
    }


    public function getTypeClass()
    {
        return RegistroConferenciaType::class;
    }

    /**
     *
     * @Route("/fin/registroConferencia/form/{id}", name="fin_registroConferencia_form", defaults={"id"=null}, requirements={"id"="\d+"})
     * @param Request $request
     * @param RegistroConferencia|null $registroConferencia
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function form(Request $request, RegistroConferencia $registroConferencia = null)
    {
        return $this->doForm($request, $registroConferencia);
    }

    /**
     *
     * @Route("/fin/registroConferencia/list/", name="fin_registroConferencia_list")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
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
                'descricao',
                'dtRegistro' => ['timestamp'],
                'carteira' => ['id','descricao','descricaoMontada'],
                'valor'
            )
        );
    }

    /**
     *
     * @Route("/fin/registroConferencia/datatablesJsList/", name="fin_registroConferencia_datatablesJsList")
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
     * @Route("/fin/registroConferencia/delete/{id}/", name="fin_registroConferencia_delete", requirements={"id"="\d+"})
     * @param Request $request
     * @param RegistroConferencia $registroConferencia
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function delete(Request $request, RegistroConferencia $registroConferencia)
    {
        return $this->doDelete($request, $registroConferencia);
    }


}