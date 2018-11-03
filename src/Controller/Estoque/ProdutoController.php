<?php

namespace App\Controller\Estoque;

use App\Controller\FormListController;
use App\Entity\Estoque\Produto;
use App\EntityHandler\EntityHandler;
use App\EntityHandler\Estoque\ProdutoEntityHandler;
use App\Form\Estoque\ProdutoType;
use App\Utils\Repository\FilterData;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ProdutoController
 * @package App\Controller\Estoque
 */
class ProdutoController extends FormListController
{

    private $entityHandler;

    /**
     * @required
     * @param ProdutoEntityHandler $entityHandler
     */
    public function setEntityHandler(ProdutoEntityHandler $entityHandler)
    {
        $this->entityHandler = $entityHandler;
    }

    /**
     * @return EntityHandler|null
     */
    public function getEntityHandler(): EntityHandler
    {
        return $this->entityHandler;
    }

    public function getFormRoute()
    {
        return 'est_produto_form';
    }

    public function getFormView()
    {
        return 'Estoque/produtoForm.html.twig';
    }

    public function getFilterDatas($params)
    {
        return array(
            new FilterData('e.reduzidoEkt', 'EQ', $params['filter']['reduzido_ekt']),
            new FilterData('e.reduzido', 'EQ', $params['filter']['reduzido']),
            new FilterData('e.descricao', 'LIKE', $params['filter']['descricao']),
//            new FilterData('sd.depto', 'EQ', $params['filter']['p_depto']),
//            new FilterData('p.subdepto', 'EQ', $params['filter']['p_subdepto']),
        );
    }

    public function getListView()
    {
        return 'Estoque/produtoList.html.twig';
    }

    public function getListRoute()
    {
        return 'est_produto_list';
    }


    public function getTypeClass()
    {
        return ProdutoType::class;
    }

    /**
     *
     * @Route("/est/produto/form/{produto}", name="est_produto_form", defaults={"produto"=null}, requirements={"produto"="\d+"})
     * @param Request $request
     * @param Produto|null $produto
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function form(Request $request, Produto $produto = null)
    {
        return $this->doForm($request, $produto);
    }

    /**
     *
     * @Route("/est/produto/list/", name="est_produto_list")
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
                'reduzido',
                'reduzidoEkt',
                'fornecedor' => [
                    'id',
                    'codigo',
                    'pessoa' => ['nome', 'nomeFantasia']
                ],
                'updated' => ['timestamp']
            )
        );
    }

    /**
     *
     * @Route("/est/produto/datatablesJsList/", name="est_produto_datatablesJsList")
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
     * @Route("/est/produto/delete/{id}/", name="est_produto_delete", requirements={"id"="\d+"})
     * @param Request $request
     * @param Produto $produto
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function delete(Request $request, Produto $produto)
    {
        return $this->doDelete($request, $produto);
    }


}