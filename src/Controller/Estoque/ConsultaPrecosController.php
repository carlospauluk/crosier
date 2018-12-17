<?php
/**
 * Created by PhpStorm.
 * User: carlos
 * Date: 14/12/18
 * Time: 10:08
 */

namespace App\Controller\Estoque;


use App\Business\Estoque\ProdutoBusiness;
use App\Exception\ViewException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ConsultaPrecosController extends AbstractController
{

    private $produtoBusiness;

    /**
     *
     * @Route("/est/consultaPrecos/list", name="est_consultaPrecos_list")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function list(Request $request)
    {
        try {
            $vParams = [];
            $vParams['tamanho'] = $request->get('tamanho');
            $vParams['fornecedor'] = $request->get('fornecedor');

            if ($vParams['fornecedor'] and $vParams['tamanho']) {
                $vParams['msg'] = $this->getProdutoBusiness()->consultaPrecosGerarMsg($vParams['fornecedor'], $vParams['tamanho']);
            }
        } catch (ViewException $e) {
            $this->addFlash('error', $e->getMessage());
        } catch (\Exception $e) {
            $this->addFlash('error', "Erro ao processar.");
        }
        return $this->render('Estoque/consultaPrecos.html.twig', $vParams);
    }

    /**
     * @return mixed
     */
    public function getProdutoBusiness(): ProdutoBusiness
    {
        return $this->produtoBusiness;
    }

    /**
     * @required
     * @param mixed $produtoBusiness
     */
    public function setProdutoBusiness(ProdutoBusiness $produtoBusiness): void
    {
        $this->produtoBusiness = $produtoBusiness;
    }


}