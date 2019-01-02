<?php

namespace App\Controller\Financeiro;

use App\Business\Financeiro\MovimentacaoBusiness;
use App\Business\Security\SecurityBusiness;
use App\Entity\Base\DiaUtil;
use App\Entity\Financeiro\Parcelamento;
use App\Form\Financeiro\MovimentacaoType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ParcelamentoController.
 *
 *
 *
 * @package App\Controller\Financeiro
 * @author Carlos Eduardo Pauluk
 */
class ParcelamentoController extends Controller
{

    private $securityBusiness;

    private $movimentacaoBusiness;


    /**
     *
     * @Route("/fin/parcelamento/form", name="fin_parcelamento_form")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function form(Request $request)
    {
        $this->securityBusiness->checkAccess('fin_parcelamento_form');

        $movimentacaoForm = $this->createForm(MovimentacaoType::class);

        $movimentacao = $request->get('movimentacao');
        if ($movimentacao and !is_array($movimentacao)) {
            // quando eu redireciono o $request pelo redirectToRoute com status 307, ele manda o array como json.
            parse_str(urldecode($request->get('movimentacao')), $movimentacao);
            $movimentacao = $movimentacao['movimentacao'];
        }


        $movimentacaoForm->handleRequest($request);

        if ($movimentacaoForm->isSubmitted()) {
            if ($movimentacaoForm->isValid()) {
                try {
                    $entity = $movimentacaoForm->getData();
                    $session = $request->hasSession() ? $request->getSession() : new Session();
                    $session->set('parcelamentoMovimentacao', $entity);
                    $this->addFlash('info', 'Movimentação preparada.');
                } catch (\Exception $e) {
                    $this->addFlash('error', $e->getMessage());
                    $this->addFlash('error', 'Erro ao salvar!');
                }
            } else {
                $errors = $movimentacaoForm->getErrors(true, true);
                foreach ($errors as $error) {
                    $this->addFlash('error', $error->getMessage());

                }
            }
        }

        // Pode ou não ter vindo algo no $parameters. Independentemente disto, só adiciono movimentacaoForm e foi-se.
        $parameters['form'] = $movimentacaoForm->createView();
        $parameters['page_title'] = 'Lançamento de Parcelamento';
        $parameters['parcelamento'] = $request->get('parcelamento');
        $parameters['parcelas'] = $request->get('parcela');
        return $this->render('Financeiro/parcelamentoForm.html.twig', $parameters);
    }

    /**
     * @Route("/fin/parcelamento/gerarParcelas", name="fin_parcelamento_gerarParcelas")
     * @param Request $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function gerarParcelas(Request $request)
    {
        $qtdeParcelas = $request->get('qtdeParcelas');
//        $valorTotal = $request->get('valorTotal');
        $valorParcela = floatval($request->get('valorParcela'));
        $diaFixo = $request->get('diaFixo');
        $primeiroVencto = $request->get('primeiroVencto');

        parse_str(urldecode($request->get('movimentacao')), $movimentacao);
        $movimentacao = $movimentacao['movimentacao'];
        $movimentacao['dtVencto'] = $primeiroVencto;

        // burlando o esquema para poder avaliar a entidade no handleRequest
        $request->request->set('movimentacao', $movimentacao);
        $movimentacaoForm = $this->createForm(MovimentacaoType::class);
        $movimentacaoForm->handleRequest($request);
        $movimentacao = $movimentacaoForm->getData();

        $dtVenctoAux = $movimentacao->getDtVencto();
        $documentoNumAux = $movimentacao->getDocumentoNum();

        $parcelas = [];

        for ($i = 0; $i < $qtdeParcelas; $i++) {
            $parcela = [];
            $parcela['numParcela'] = $i + 1;
            $parcela['valor'] = number_format($valorParcela, 2, ',', '.');

            if ($i > 0) {
                if ($diaFixo) {
                    $dtVenctoAux->setDate($dtVenctoAux->format('Y'), ($dtVenctoAux->format('m') + 1), $dtVenctoAux->format('d'));
                } else {
                    $dtVenctoAux->add(new \DateInterval('P30D'));
                }
            }
            $parcela['dtVencto'] = $dtVenctoAux->format('d/m/Y');
            $dtVenctoEfetiva = $this->getDoctrine()->getManager()->getRepository(DiaUtil::class)->findProximoDiaUtilFinanceiro($dtVenctoAux);
            $parcela['dtVenctoEfetiva'] = $dtVenctoEfetiva->format('d/m/Y');

            if ($documentoNumAux) {
                $parcela['documentoNum'] = ctype_digit($documentoNumAux) ? (intval($documentoNumAux) + $i) : $documentoNumAux;
            } else {
                $parcela['documentoNum'] = '';
            }


            $parcelas[] = $parcela;
        }


        return new JsonResponse($parcelas);
    }

    /**
     * @Route("/fin/parcelamento/salvarParcelas", name="fin_parcelamento_salvarParcelas")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \ReflectionException
     * @throws \Doctrine\ORM\ORMException
     */
    public function salvarParcelas(Request $request)
    {
        $session = $request->hasSession() ? $request->getSession() : new Session();
        $movimentacao = $session->get('parcelamentoMovimentacao');
        $this->getMovimentacaoBusiness()->refindAll($movimentacao);

        $parcelas = $request->get('parcela');
        if (!is_array($parcelas)) {
            $this->addFlash('error', 'Parcelas não geradas?');
            return $this->redirectToRoute('fin_parcelamento_form', ['request' => $request], 307);
        }

        try {
            $parcelamento = $this->getMovimentacaoBusiness()->salvarParcelas($movimentacao, $parcelas);
            if (!$parcelamento) {
                throw new \Exception('Erro ao gerar parcelamento');
            }
            return $this->redirectToRoute('fin_movimentacao_list', ['filter' => ['parcelamento' => $parcelamento->getId()]]);
        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());
            return $this->redirectToRoute('fin_parcelamento_form', ['request' => $request], 307);
        }

    }

    /**
     *
     * @Route("/fin/parcelamento/delete/{id}/", name="fin_parcelamento_delete", requirements={"id"="\d+"})
     * @param Request $request
     * @param Parcelamento $parcelamento
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function delete(Request $request, Parcelamento $parcelamento)
    {
        return $this->doDelete($request, $parcelamento);
    }


    /**
     *
     * @required
     * @param SecurityBusiness $securityBusiness
     */
    public function setSecurityBusiness(SecurityBusiness $securityBusiness)
    {
        $this->securityBusiness = $securityBusiness;
    }

    /**
     * @return mixed
     */
    public function getSecurityBusiness(): SecurityBusiness
    {
        return $this->securityBusiness;
    }

    /**
     * @return mixed
     */
    public function getMovimentacaoBusiness(): MovimentacaoBusiness
    {
        return $this->movimentacaoBusiness;
    }

    /**
     * @required
     * @param mixed $movimentacaoBusiness
     */
    public function setMovimentacaoBusiness(MovimentacaoBusiness $movimentacaoBusiness): void
    {
        $this->movimentacaoBusiness = $movimentacaoBusiness;
    }

}