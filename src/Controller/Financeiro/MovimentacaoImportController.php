<?php

namespace App\Controller\Financeiro;

use App\Business\Financeiro\MovimentacaoBusiness;
use App\Business\Financeiro\MovimentacaoImporter;
use App\Entity\Financeiro\Carteira;
use App\Entity\Financeiro\GrupoItem;
use App\EntityHandler\EntityHandler;
use App\EntityHandler\Financeiro\MovimentacaoEntityHandler;
use App\Exception\ViewException;
use App\Form\Financeiro\MovimentacaoType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
class MovimentacaoImportController extends AbstractController
{

    private $entityHandler;

    private $business;

    private $movimentacaoImporter;

    private $vParams = array();

    public function __construct(MovimentacaoEntityHandler $entityHandler,
                                MovimentacaoBusiness $business,
                                MovimentacaoImporter $movimentacaoImporter)
    {
        $this->entityHandler = $entityHandler;
        $this->business = $business;
        $this->movimentacaoImporter = $movimentacaoImporter;

        $this->setDefaults();
    }

    public function getEntityHandler(): ?EntityHandler
    {
        return $this->entityHandler;
    }

    public function getBusiness(): MovimentacaoBusiness
    {
        return $this->business;
    }

    public function setDefaults()
    {
        $this->vParams['tipoExtrato'] = 'EXTRATO_SIMPLES';
        $this->vParams['linhasExtrato'] = null;
        $this->vParams['carteiraExtrato'] = null;
        $this->vParams['carteiraDestino'] = null;
        $this->vParams['grupo'] = null;
        $this->vParams['grupoItem'] = null;
        $this->vParams['gerarSemRegras'] = null;
        $this->vParams['usarCabecalho'] = null;
        $this->vParams['movs'] = null;
        $this->vParams['total'] = null;
    }

    /**
     * Lida com os vParams, sobrepondo na seguinte ordem: defaults > session > request.
     * @param Request $request
     */
    public function handleVParams(?Request $request)
    {
        $session = $request->hasSession() ? $request->getSession() : new Session();
        if (is_array($session->get('vParams'))) {
            $this->vParams = array_merge($this->vParams, $session->get('vParams'));
        }
        if (is_array($request->request->all())) {
            $this->vParams = array_merge($this->vParams, $request->request->all());
        }
    }

    /**
     *
     * @Route("/fin/movimentacao/import", name="fin_movimentacao_import")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function import(Request $request)
    {
        $this->handleVParams($request);

        try {
            if ($request->request->get('btnImportar')) {
                $this->importar($request);
            } else if ($request->request->get('btnSalvarTodas')) {
                $this->salvarTodas($request);
                $this->importar($request);
            } else if ($request->request->get('btnLimpar')) {
                $this->limpar($request);
            }
        } catch (ViewException $e) {
            $this->addFlash('error', $e->getMessage());
        } catch (\Exception $e) {
            $this->addFlash('error', 'Erro ao processar requisição.');
        }

        $session = $request->hasSession() ? $request->getSession() : new Session();
        $this->vParams['page_title'] = "Importação de Movimentações";
        $session->set('vParams', $this->vParams);

        return $this->render('Financeiro/movimentacaoImport.html.twig', $this->vParams);
    }

    public function limpar(Request $request)
    {
        $session = $request->hasSession() ? $request->getSession() : new Session();
        $session->set('vParams', null);
        $this->setDefaults();
    }

    /**
     * @param Request $request
     * @return mixed
     * @throws ViewException
     */
    public function importar(Request $request)
    {
        $this->vParams['movs'] = null;

        $tipoExtrato = $request->get('tipoExtrato');
        if (!$tipoExtrato) {
            throw new ViewException('É necessário informar o tipo do extrato.');
        }


        $carteiraExtrato = null;
        if ($this->vParams['carteiraExtrato']) {
            $carteiraExtrato = $this->getDoctrine()->getRepository(Carteira::class)->find($this->vParams['carteiraExtrato']);
        }

        $carteiraDestino = null;
        if ($this->vParams['carteiraDestino']) {
            $carteiraDestino = $this->getDoctrine()->getRepository(Carteira::class)->find($this->vParams['carteiraDestino']);
        }

        $grupoItem = null;
        if ($this->vParams['grupoItem']) {
            $grupoItem = $this->getDoctrine()->getRepository(GrupoItem::class)->find($this->vParams['grupoItem']);
            $this->vParams['grupo'] = $grupoItem->getId();
        }


        if (strpos($tipoExtrato, 'DEBITO') !== FALSE) {
            if (!$carteiraExtrato or !$carteiraDestino) {
                throw new ViewException("Para extratos do tipo 'DÉBITO' é necessário informar as carteiras de origem e destino");
            }
        } else if (strpos($tipoExtrato, 'GRUPO') !== FALSE) {
            if (!$grupoItem) {
                throw new ViewException("Para extratos do tipo 'GRUPO' é necessário informar o grupo");
            }
        } else {
            if (!$carteiraExtrato) {
                throw new ViewException("É necessário informar a carteira do extrato");
            }
        }

        $r = $this->movimentacaoImporter->importar(
            $this->vParams['tipoExtrato'],
            $this->vParams['linhasExtrato'],
            $carteiraExtrato,
            $carteiraDestino,
            $grupoItem,
            $this->vParams['gerarSemRegras'],
            $this->vParams['usarCabecalho']);

        $movsImportadas = $r['movs'];

        $sessionMovs = array();
        $unqs = [];
        foreach ($movsImportadas as $mov) {
            if (in_array($mov->getUnqControle(), $unqs)) {
                throw new ViewException('Movimentação duplicada na sessão: ' . $mov->getDescricao());
            }
            $unqs[] = $mov->getUnqControle();

            $sessionMovs[$mov->getUnqControle()] = $mov;
        }

        $this->vParams['linhasExtrato'] = $r['LINHAS_RESULT'];
        $this->vParams['total'] = $this->getBusiness()->somarMovimentacoes($r['movs']);

        $this->vParams['movs'] = $sessionMovs;
    }


    /**
     * Salva todas as movimentações.
     *
     * @param Request $request
     */
    private function salvarTodas(Request $request)
    {
        try {
            $this->getEntityHandler()->persistAll($this->vParams['movs']);
            $this->addFlash('success', 'Movimentações salvas com sucesso!');
            $this->vParams['movs'] = null;
        } catch (ViewException $e) {
            $this->addFlash('error', $e->getMessage());
        } catch (\Exception $e) {
            $this->addFlash('error', 'Erro ao processar requisição');
        }
    }

    /**
     *
     * @Route("/fin/movimentacao/import/tiposExtratos", name="fin_movimentacao_import_tiposExtratos")
     * @param Request $request
     * @return Response
     */
    public function tiposExtratos(Request $request)
    {
        // FIXME: isto deveria estar em uma tabela.
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
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Doctrine\ORM\ORMException
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