<?php

namespace App\Controller\Vendas;

use App\Business\Base\DiaUtilBusiness;
use App\Entity\Vendas\Venda;
use App\Utils\Repository\FilterData;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class VendasResultsController extends Controller
{

    private $diaUtilBusiness;

    /**
     * @return mixed
     */
    public function getDiaUtilBusiness(): DiaUtilBusiness
    {
        return $this->diaUtilBusiness;
    }

    /**
     * @required
     * @param mixed $diaUtilBusiness
     */
    public function setDiaUtilBusiness(DiaUtilBusiness $diaUtilBusiness): void
    {
        $this->diaUtilBusiness = $diaUtilBusiness;
    }


    public function getFilterDatas($params)
    {
        return array(
            new FilterData('dtVenda', 'BETWEEN', isset($params['filter']['dtVenda']) ? $params['filter']['dtVenda'] : null),
            new FilterData('vendedor.codigo', 'BETWEEN', isset($params['filter']['vendedorCodigo']) ? $params['filter']['vendedorCodigo'] : null)
        );

    }

    /**
     *
     * @Route("/ven/vendasResults/vendasPorPeriodo", name="ven_vendasResults_vendasPorPeriodo")
     *
     */
    public function vendasPorPeriodo(Request $request)
    {
        $parameters = $request->query->all();
        if (!array_key_exists('filter', $parameters)) {
            // inicializa para evitar o erro
            $parameters['filter'] = array();
            $parameters['filter']['dtVenda']['i'] = date('Y-m-d');
            $parameters['filter']['dtVenda']['f'] = date('Y-m-d');
        }

        $filterDatas = $this->getFilterDatas($parameters);

        $repo = $this->getDoctrine()->getRepository(Venda::class);
        $orders[] = ['column' => 'vendedor.codigo', 'dir' => 'asc'];
        $dados = $repo->findByFilters($filterDatas, $orders, 0, null);

        $dtIni = $parameters['filter']['dtVenda']['i'];
        $dtFim = $parameters['filter']['dtVenda']['f'];

        $prox = $this->diaUtilBusiness->incPeriodo(true, $dtIni, $dtFim);
        $ante = $this->diaUtilBusiness->incPeriodo(false, $dtIni, $dtFim);
        $parameters['antePeriodoI'] = $ante['dtIni'];
        $parameters['antePeriodoF'] = $ante['dtFim'];
        $parameters['proxPeriodoI'] = $prox['dtIni'];
        $parameters['proxPeriodoF'] = $prox['dtFim'];

        $parameters['page_title'] = "Vendas por Período";

        return $this->render('Vendas/vendasResults/vendasPorPeriodo.html.twig', $parameters);
    }

}