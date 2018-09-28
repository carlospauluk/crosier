<?php

namespace App\Controller\Financeiro;

use App\Business\Financeiro\MovimentacaoBusiness;
use App\Business\Financeiro\MovimentacaoImporter;
use App\Entity\Financeiro\Carteira;
use App\Entity\Financeiro\GrupoItem;
use App\Entity\Financeiro\Movimentacao;
use App\EntityHandler\EntityHandler;
use App\EntityHandler\Financeiro\MovimentacaoEntityHandler;
use App\Form\Financeiro\MovimentacaoType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
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

    private $movimentacaoImporter;

    public function __construct(MovimentacaoEntityHandler $entityHandler,
                                MovimentacaoBusiness $business,
                                MovimentacaoImporter $movimentacaoImporter)
    {
        $this->entityHandler = $entityHandler;
        $this->business = $business;
        $this->movimentacaoImporter = $movimentacaoImporter;
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
     * @throws \Exception
     */
    public function import(Request $request)
    {

        if ($request->request->get('btnImportar')) {
            // Se foi mandado importar
            $this->importar($request);
        } else if ($request->request->get('btnSalvarTodas')) {
            // Se foi mandado salvar todas
            $this->salvarTodas($request);
            $this->importar($request);
        }

        $session = $request->hasSession() ? $request->getSession() : new Session();
        $vParams = $session->get('vParams');
        if (!$vParams) {
            // Valores padrão para o primeiro load
            $vParams = [];
            $vParams['tipoExtrato'] = 'EXTRATO_SIMPLES';
            $vParams['linhasExtrato'] = null;
            $vParams['carteiraExtrato'] = null;
            $vParams['carteiraDestino'] = null;
            $vParams['grupo'] = null;
            $vParams['grupoItem'] = null;
            $vParams['gerarSemRegras'] = null;
        }

        $vParams['page_title'] = "Importação de Movimentações";
        return $this->render('Financeiro/movimentacaoImport.html.twig', $vParams);
    }

    /**
     * @param Request $request
     * @param $vParams
     * @param $session
     * @return mixed
     * @throws \Exception
     */
    public function importar(Request $request)
    {
        $session = $request->hasSession() ? $request->getSession() : new Session();

        $vParams = $session->get('vParams');

        $vParams['tipoExtrato'] = $request->request->get('tipoExtrato');
        $vParams['linhasExtrato'] = $request->request->get('linhasExtrato');

        $carteiraExtrato = null;
        $carteiraExtratoId = $request->request->get('carteiraExtrato');
        $vParams['carteiraExtrato'] = $carteiraExtratoId;
        if ($carteiraExtratoId) {
            $carteiraExtrato = $this->getDoctrine()->getRepository(Carteira::class)->find($carteiraExtratoId);
        }

        $carteiraDestino = null;
        $carteiraDestinoId = $request->request->get('carteiraDestino');
        $vParams['carteiraDestino'] = $carteiraDestinoId;
        if ($carteiraDestinoId) {
            $carteiraDestino = $this->getDoctrine()->getRepository(Carteira::class)->find($carteiraDestinoId);
        }

        $vParams['grupoItem'] = $request->request->get('grupoItem');
        if ($vParams['grupoItem']) {
            $grupoItem = $this->getDoctrine()->getRepository(GrupoItem::class)->find($vParams['grupoItem']);
            $vParams['grupo'] = $grupoItem->getId();
        }
        $vParams['gerarSemRegras'] = $request->request->get('gerarSemRegras');

        // Importa
        $r = $this->movimentacaoImporter->importar(
            $vParams['tipoExtrato'],
            $vParams['linhasExtrato'],
            $carteiraExtrato,
            $carteiraDestino,
            $vParams['grupoItem'],
            $vParams['gerarSemRegras']);

        $vParams['movsImportadas'] = $r['movs'];

        $sessionMovs = array();
        foreach ($r['movs'] as $mov) {
            $sessionMovs[$mov->getUnqControle()] = $mov;
        }
        $vParams['linhasExtrato'] = $r['LINHAS_RESULT'];
        $vParams['total'] = $this->getBusiness()->somarMovimentacoes($r['movs']);

        $session->set('movs', $sessionMovs);
        $session->set('vParams', $vParams);
    }


    private function salvarTodas(Request $request)
    {
        $session = $request->hasSession() ? $request->getSession() : new Session();
        try {
            $movs = $session->get('movs');
            $this->getEntityHandler()->persistAll($movs);
            $session->getFlashBag()->add('success', 'Movimentações salvas com sucesso!');
            $session->set('movs', null);
        } catch (\Exception $e) {
            $session->getFlashBag()->add('error', $e->getMessage());
        }
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

    /**
     *
     * @Route("/fin/movimentacao/import/form/{unqControle}", name="fin_movimentacao_import_form")
     * @param Request $request
     * @param $unqControle
     * @param Movimentacao|null $movimentacao
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Doctrine\ORM\ORMException
     * @throws \Exception
     */
    public function form(Request $request, $unqControle)
    {
        if (!$unqControle) {
            throw new \Exception("unqControle não informado");
        }
        $session = new Session();
        $sessionMovs = $session->get('movs');

        $movimentacao = $sessionMovs[$unqControle];

        // Dá um merge nos atributos manyToOne pra não dar erro no createForm
        $this->getBusiness()->mergeAll($movimentacao);

        $formData = null;
        $form = $this->createForm(MovimentacaoType::class, $movimentacao);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $movimentacao = $form->getData();
                $sessionMovs[$unqControle] = $movimentacao;
                $this->addFlash('success', 'Registro salvo com sucesso!');
                return $this->redirectToRoute('fin_movimentacao_import');
            } else {
                $form->getErrors(true, false);
            }
        }

        // Pode ou não ter vindo algo no $parameters. Independentemente disto, só adiciono form e foi-se.
        $parameters['form'] = $form->createView();

        return $this->render('Financeiro/movimentacaoImportForm.html.twig', $parameters);
    }



}