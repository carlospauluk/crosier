<?php

namespace App\Controller\Financeiro;

use App\Business\Financeiro\MovimentacaoBusiness;
use App\Controller\FormListController;
use App\Entity\Financeiro\Carteira;
use App\Entity\Financeiro\Categoria;
use App\Entity\Financeiro\Modo;
use App\Entity\Financeiro\Movimentacao;
use App\Entity\Financeiro\Status;
use App\EntityHandler\EntityHandler;
use App\EntityHandler\Financeiro\MovimentacaoEntityHandler;
use App\Form\Financeiro\MovimentacaoType;
use App\Utils\Repository\FilterData;
use App\Utils\Repository\WhereBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class MovimentacaoBaseController
 * @package App\Controller\Financeiro
 * @author Carlos Eduardo Pauluk
 */
class MovimentacaoBaseController extends FormListController
{

    private $entityHandler;

    private $business;

    /**
     * @required
     * @param MovimentacaoEntityHandler $entityHandler
     */
    public function setEntityHandler(MovimentacaoEntityHandler $entityHandler)
    {
        $this->entityHandler = $entityHandler;
    }

    public function getEntityHandler(): EntityHandler
    {
        return $this->entityHandler;
    }

    /**
     * @required
     * @param MovimentacaoBusiness $business
     */
    public function setBusiness(MovimentacaoBusiness $business)
    {
        $this->business = $business;
    }

    public function getBusiness(): MovimentacaoBusiness
    {
        return $this->business;
    }

    public function getFormRoute()
    {
        return 'fin_movimentacao_form';
    }

    public function getFormView()
    {
        return 'Financeiro/movimentacaoForm.html.twig';
    }

    public function getListView()
    {
        return 'Financeiro/movimentacaoList.html.twig';
    }

    public function getListRoute()
    {
        return 'fin_movimentacao_list';
    }


    public function getTypeClass()
    {
        return MovimentacaoType::class;
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
                'categoria' => ['id', 'codigoSuper', 'descricaoMontada'],
                'carteira' => ['id', 'descricaoMontada'],
                'dtUtil' => ['timestamp'],
                'valorTotal',
                'updated' => ['timestamp'],
                'userUpdated' => ['id', 'nome'],
                'pessoa' => ['id', 'nome', 'nome_fantasia']
            )
        );
    }

    /**
     *
     * @Route("/fin/movimentacao/delete/{movimentacao}/", name="fin_movimentacao_delete", requirements={"movimentacao"="\d+"})
     * @param Request $request
     * @param Movimentacao $movimentacao
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function delete(Request $request, Movimentacao $movimentacao)
    {
        return $this->doDelete($request, $movimentacao);
    }

    public function getFilterDatas($params)
    {
        return array(
            new FilterData(array('id', 'descricao'), 'LIKE', isset($params['filter']['descricao']) ? $params['filter']['descricao'] : null),
            new FilterData('dtUtil', 'BETWEEN', isset($params['filter']['dtUtil']) ? $params['filter']['dtUtil'] : null),
            new FilterData('valorTotal', 'BETWEEN', isset($params['filter']['valorTotal']) ? $params['filter']['valorTotal'] : null, 'decimal'),
            new FilterData('carteira', 'IN', isset($params['filter']['carteira']) ? $params['filter']['carteira'] : null),
            new FilterData('status', 'IN', isset($params['filter']['status']) ? $params['filter']['status'] : null),
            new FilterData('modo', 'IN', isset($params['filter']['modo']) ? $params['filter']['modo'] : null),
            new FilterData('cadeia', 'EQ', isset($params['filter']['cadeia']) ? $params['filter']['cadeia'] : null),
            new FilterData('parcelamento', 'EQ', isset($params['filter']['parcelamento']) ? $params['filter']['parcelamento'] : null),
            new FilterData('recorrente', 'EQ', isset($params['filter']['recorrente']) ? $params['filter']['recorrente'] : null),
            new FilterData('categoria', 'IN', isset($params['filter']['categoria']) ? $params['filter']['categoria'] : null)
        );

    }

    protected function getFilterChoices()
    {
        $filterChoices = array();

        $repoCarteira = $this->getDoctrine()->getRepository(Carteira::class);
        $carteiras = $repoCarteira->findAll(WhereBuilder::buildOrderBy('codigo'));
        $filterChoices['carteiras'] = $carteiras;

        $filterChoices['status'] = Status::ALL;

        $repoModo = $this->getDoctrine()->getRepository(Modo::class);
        $modos = $repoModo->findAll();
        $filterChoices['modos'] = $modos;

        $repoCateg = $this->getDoctrine()->getRepository(Categoria::class);
        $categorias = $repoCateg->buildTreeList();
        $filterChoices['categorias'] = $categorias;

        return $filterChoices;
    }


}