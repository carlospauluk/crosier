<?php

namespace App\Controller\Financeiro;


use App\Controller\FormListController;
use App\Entity\Financeiro\RegraImportacaoLinha;
use App\EntityHandler\EntityHandler;
use App\EntityHandler\Financeiro\RegraImportacaoLinhaEntityHandler;
use App\Form\Financeiro\RegraImportacaoLinhaType;
use App\Utils\Repository\FilterData;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class RegraImportacaoLinhaController
 * @package App\Controller\Financeiro
 * @author Carlos Eduardo Pauluk
 */
class RegraImportacaoLinhaController extends FormListController
{


    private $entityHandler;

    public function __construct(RegraImportacaoLinhaEntityHandler $entityHandler)
    {
        $this->entityHandler = $entityHandler;
    }

    public function getEntityHandler(): ?EntityHandler
    {
        return $this->entityHandler;
    }

    public function getFormRoute()
    {
        return 'fin_regraImportacaoLinha_form';
    }

    public function getFormView()
    {
        return 'Financeiro/regraImportacaoLinhaForm.html.twig';
    }

    public function getFilterDatas($params)
    {
        return array(
            new FilterData('regraRegexJava', 'LIKE', isset($params['filter']['regraRegexJava']) ? $params['filter']['regraRegexJava'] : null)
        );
    }

    public function getListView()
    {
        return 'Financeiro/regraImportacaoLinhaList.html.twig';
    }

    public function getListRoute()
    {
        return 'fin_regraImportacaoLinha_list';
    }


    public function getTypeClass()
    {
        return RegraImportacaoLinhaType::class;
    }

    /**
     *
     * @Route("/fin/regraImportacaoLinha/form/{id}", name="fin_regraImportacaoLinha_form", defaults={"id"=null}, requirements={"id"="\d+"})
     * @param Request $request
     * @param RegraImportacaoLinha|null $regraImportacaoLinha
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \ReflectionException
     */
    public function form(Request $request, RegraImportacaoLinha $regraImportacaoLinha = null)
    {
        return $this->doForm($request, $regraImportacaoLinha);
    }

    /**
     *
     * @Route("/fin/regraImportacaoLinha/list/", name="fin_regraImportacaoLinha_list")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \ReflectionException
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
                'regraRegexJava',
                'tipoLancto',
                'status',
                'carteira' => ['descricaoMontada'],
                'modo' => ['descricaoMontada']
            )
        );
    }

    /**
     *
     * @Route("/fin/regraImportacaoLinha/datatablesJsList/", name="fin_regraImportacaoLinha_datatablesJsList")
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
     * @Route("/fin/regraImportacaoLinha/delete/{id}/", name="fin_regraImportacaoLinha_delete", requirements={"id"="\d+"})
     * @param Request $request
     * @param RegraImportacaoLinha $regraImportacaoLinha
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function delete(Request $request, RegraImportacaoLinha $regraImportacaoLinha)
    {
        return $this->doDelete($request, $regraImportacaoLinha);
    }


}