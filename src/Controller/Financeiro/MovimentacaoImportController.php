<?php

namespace App\Controller\Financeiro;

use App\Business\Financeiro\MovimentacaoBusiness;
use App\Entity\Financeiro\Movimentacao;
use App\EntityHandler\EntityHandler;
use App\EntityHandler\Financeiro\MovimentacaoEntityHandler;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * Class MovimentacaoImportController
 * @package App\Controller\Financeiro
 * @author Carlos Eduardo Pauluk
 */
class MovimentacaoImportController extends Controller
{

    private $entityHandler;

    private $business;

    public function __construct(MovimentacaoEntityHandler $entityHandler, MovimentacaoBusiness $business)
    {
        $this->entityHandler = $entityHandler;
        $this->business = $business;
    }

    public function getEntityHandler(): ?EntityHandler
    {
        return $this->entityHandler;
    }

    public function getBusiness(): MovimentacaoBusiness
    {
        return $this->business;
    }

    /**
     *
     * @Route("/fin/movimentacao/import", name="fin_movimentacao_import", options = { "expose" = true })
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function import(Request $request)
    {
        if ($request->request->get('tipoExtrato') and $request->request->get('linhasExtrato')) {
            $tipoExtrato = $request->request->get('tipoExtrato');
            $linhasExtrato = $request->request->get('linhasExtrato');
        }


        return $this->render('Financeiro/movimentacaoImport.html.twig');
    }

    /**
     *
     * @Route("/fin/movimentacao/import/tiposExtratos", name="fin_movimentacao_import_tiposExtratos", methods={"GET"}, options = { "expose" = true })
     * @param Request $request
     * @return Response
     */
    public function tiposExtratos(Request $request)
    {
        $tiposExtratos = [
            ["id" => "EXTRATO_SIMPLES", "text" => "EXTRATO SIMPLES"],
            ["id" => "EXTRATO_GRUPO_MOVIMENTACOES", "text" => "EXTRATO GRUPO DE MOVIMENTAÇÕES"],
            ["id" => "EXTRATO_COMPRA_BNDES_BB", "text" => "EXTRATO COMPRA BNDES BB"],
            ["id" => "EXTRATO_RDCARD_CREDITO", "text" => "EXTRATO RDCARD - CRÉDITO"],
            ["id" => "EXTRATO_RDCARD_DEBITO", "text" => "EXTRATO RDCARD - DÉBITO"],
            ["id" => "EXTRATO_MODERNINHA_DEBITO", "text" => "EXTRATO MODERNINHA - DÉBITO"],
            ["id" => "EXTRATO_CIELO_CREDITO", "text" => "EXTRATO CIELO - CRÉDITO"],
            ["id" => "EXTRATO_CIELO_DEBITO", "text" => "EXTRATO CIELO - DÉBITO"],
            ["id" => "EXTRATO_CIELO_CREDITO_NOVO", "text" => "EXTRATO CIELO - CRÉDITO (NOVO)"],
            ["id" => "EXTRATO_CIELO_DEBITO_NOVO", "text" => "EXTRATO CIELO - DÉBITO (NOVO)"],
            ["id" => "EXTRATO_STONE_CREDITO", "text" => "EXTRATO STONE - CRÉDITO"],
            ["id" => "EXTRATO_STONE_DEBITO", "text" => "EXTRATO STONE - DÉBITO"]
        ];

//        $results = array('results' => $tiposExtratos);

        $normalizer = new ObjectNormalizer();
        $encoder = new JsonEncoder();

        $serializer = new Serializer(array($normalizer), array($encoder));
        $json = $serializer->serialize($tiposExtratos, 'json');

        return new Response($json);

    }


}