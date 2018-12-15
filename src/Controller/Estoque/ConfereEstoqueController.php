<?php

namespace App\Controller\Estoque;

use App\Business\Estoque\OCBusiness;
use App\Business\Estoque\ProdutoBusiness;
use App\Entity\Estoque\Fornecedor;
use App\Exception\ViewException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ConfereEstoqueController.
 *
 * @package App\Controller\Estoque
 */
class ConfereEstoqueController extends Controller
{

    private $ocBusiness;

    private $produtoBusiness;

    /**
     *
     * @Route("/est/confereEstoque/list", name="est_confereEstoque_list")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function list(Request $request)
    {
        $vParams = [];

        $session = $request->hasSession() ? $request->getSession() : new Session();
        if (!$session->get('rs')) {
            $rs = $this->getProdutoBusiness()->conferirEstoques();
            $session->set('rs', $rs);
        } else {
            $rs = $session->get('rs');
        }
        $vParams['rs'] = $rs;

        return $this->render('Estoque/confereEstoque.html.twig', $vParams);
    }


    /**
     * @return OCBusiness
     */
    public function getOcBusiness(): OCBusiness
    {
        return $this->ocBusiness;
    }

    /**
     * @required
     * @param OCBusiness $ocBusiness
     */
    public function setOcBusiness(OCBusiness $ocBusiness): void
    {
        $this->ocBusiness = $ocBusiness;
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