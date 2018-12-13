<?php

namespace App\Controller\Estoque;

use App\Business\Estoque\OCBusiness;
use App\Entity\Estoque\Fornecedor;
use App\Entity\Estoque\Subdepto;
use App\Exception\ViewException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ProdutoOCCompareController.
 *
 * @package App\Controller\Estoque
 */
class ProdutosOCCompareController extends Controller
{

    private $ocBusiness;

    /**
     *
     * @Route("/est/produtosOCCompare/list", name="est_produtosOCCompare_list")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function list(Request $request)
    {
        $vParams = [];

        $fornecedorId = $request->get('fornecedor');
        $fornecedor = null;
        if ($fornecedorId) {
            $fornecedor = $this->getDoctrine()->getRepository(Fornecedor::class)->find($fornecedorId);
            if ($fornecedor) {
                try {
                    $vParams['compare'] = $this->getOcBusiness()->compareEstProdutosOcProducts($fornecedor);
                } catch (ViewException $e) {
                    $this->addFlash('error', 'Erro ao executar comparação entre os produtos');
                }
            }
        }

        $vParams['somenteNaLoja'] = $request->get('somenteNaLoja');
        $vParams['fornecedor'] = $fornecedor ? $fornecedor->getId() : null;
        return $this->render('Estoque/produtosOCCompare.html.twig', $vParams);
    }

    /**
     *
     * @Route("/est/produtosOCCompare/regerar", name="est_produtosOCCompare_regerar")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function regerar(Request $request)
    {
        $fornecedorId = $request->get('fornecedor');
        $fornecedor = $this->getDoctrine()->getRepository(Fornecedor::class)->find($fornecedorId);
        $subdeptoId = $request->get('subdepto');
        $subdepto = $this->getDoctrine()->getRepository(Subdepto::class)->find($subdeptoId);
        try {
            $this->getOcBusiness()->regerarOcProducts($fornecedor, $subdepto);
        } catch (ViewException $e) {
            $this->addFlash('error', $e->getMessage());
        }
        return $this->redirectToRoute('est_produtosOCCompare_list', ['fornecedor' => $fornecedor->getId()]);
    }

    /**
     *
     * @Route("/est/produtosOCCompare/corrigirNomesEDescricoes", name="est_produtosOCCompare_corrigirNomesEDescricoes")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function corrigirNomesEDescricoes(Request $request)
    {
        $fornecedorId = $request->get('fornecedor');
        $fornecedor = $this->getDoctrine()->getRepository(Fornecedor::class)->find($fornecedorId);
        $subdeptoId = $request->get('subdepto');
        $subdepto = $this->getDoctrine()->getRepository(Subdepto::class)->find($subdeptoId);
        try {
            $this->getOcBusiness()->corrigirNomesEDescricoes($fornecedor, $subdepto);
        } catch (ViewException $e) {
            $this->addFlash('error', $e->getMessage());
        }
        return $this->redirectToRoute('est_produtosOCCompare_list', ['fornecedor' => $fornecedor->getId()]);
    }

    /**
     *
     * @Route("/est/produtosOCCompare/ativarDesativar", name="est_produtosOCCompare_ativarDesativar")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function ativarDesativar(Request $request)
    {
        $fornecedorId = $request->get('fornecedor');
        $fornecedor = $this->getDoctrine()->getRepository(Fornecedor::class)->find($fornecedorId);
        $subdeptoId = $request->get('subdepto');
        $subdepto = $this->getDoctrine()->getRepository(Subdepto::class)->find($subdeptoId);

        $ativarDesativar = $request->get('ativarDesativar');
        try {
            $this->getOcBusiness()->ativarDesativar($fornecedor, $subdepto, $ativarDesativar);
        } catch (ViewException $e) {
            $this->addFlash('error', $e->getMessage());
        }
        return $this->redirectToRoute('est_produtosOCCompare_list', ['fornecedor' => $fornecedor->getId()]);
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


}