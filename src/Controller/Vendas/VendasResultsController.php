<?php

namespace App\Controller\Vendas;

use App\Business\Base\DiaUtilBusiness;
use App\Entity\Vendas\Venda;
use App\Utils\Repository\FilterData;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Date;

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

    /**
     *
     * @Route("/ven/vendasResults/vendasPorPeriodo", name="ven_vendasResults_vendasPorPeriodo")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function vendasPorPeriodo(Request $request)
    {
        $parameters = $request->query->all();
        if (!is_array($parameters)) {
            $parameters = array();
        }
        if ($request->get('btnHoje') or !array_key_exists('dtVenda', $parameters)) {
            // inicializa para evitar o erro
            $dt = new \DateTime();
            $parameters['dtVenda']['i'] = $dt->format('Y-m') . '-01';
            $parameters['dtVenda']['f'] = $dt->format('Y-m-d');
        }
        if (!array_key_exists('codVendedor', $parameters)) {
            $parameters['codVendedor']['i'] = 0;
            $parameters['codVendedor']['f'] = 99;
        }

        
        $repo = $this->getDoctrine()->getRepository(Venda::class);

        $dtIni = \DateTime::createFromFormat('Y-m-d', $parameters['dtVenda']['i']);
        $dtFim = \DateTime::createFromFormat('Y-m-d', $parameters['dtVenda']['f']);

        $codVendedorIni = $parameters['codVendedor']['i'];
        $codVendedorFim = $parameters['codVendedor']['f'];

        $dados = $repo->findTotalVendasPorPeriodoVendedores($dtIni, $dtFim, $codVendedorIni, $codVendedorFim);

        $parameters['dados'] = $dados;

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