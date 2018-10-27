<?php

namespace App\Controller\Financeiro;

use App\Business\Base\DiaUtilBusiness;
use App\Entity\Financeiro\Carteira;
use App\Entity\Financeiro\GrupoItem;
use App\Entity\Financeiro\Modo;
use App\Entity\Financeiro\Movimentacao;
use App\EntityHandler\Financeiro\MovimentacaoEntityHandler;
use App\Form\Financeiro\MovimentacaoType;
use App\Utils\ExceptionUtils;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class MovimentacaoCaixaController
 * @package App\Controller\Financeiro
 * @author Carlos Eduardo Pauluk
 */
class MovimentacaoCaixaController extends MovimentacaoBaseController
{

    private $diaUtilBusiness;

    private $entityHandler;

    public function __construct(DiaUtilBusiness $diaUtilBusiness, MovimentacaoEntityHandler $entityHandler)
    {
        $this->entityHandler = $entityHandler;
        $this->diaUtilBusiness = $diaUtilBusiness;
    }

    /**
     *
     * @Route("/fin/movimentacao/caixa", name="fin_movimentacao_caixa")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function movimentacoesCaixa(Request $request)
    {
        $parameters = $request->query->all();
        if (!array_key_exists('filter', $parameters)) {
            // inicializa para evitar o erro
            $parameters['filter'] = array();
        }

        if (!isset($parameters['filter']['dtMoviment']) or !$parameters['filter']['dtMoviment']) {
            $parameters['filter']['dtMoviment'] = date('Y-m-d');
        }

        if (!isset($parameters['filter']['carteira']) or !$parameters['filter']['carteira']) {
            $parameters['filter']['carteira'] = $this->getDoctrine()->getRepository(Carteira::class)->findOneBy(['caixa' => true])->getId();
        }

        $dtMoviment = \DateTime::createFromFormat('Y-m-d', $parameters['filter']['dtMoviment']);

        // Já calculo pois vou utilizar no saldo anterior
        $dtAnterior = (clone $dtMoviment)->setTime(12, 0, 0, 0)->modify('last day');

        if (isset($parameters['btnAnterior'])) {
            $dtMoviment = $dtAnterior;
        } else if (isset($parameters['btnPosterior'])) {
            $dtMoviment->setTime(12, 0, 0, 0)->modify('next day');
        }

        $dtMoviment->setTime(0,0,0,0);

        $parameters['filter']['dtMoviment'] = $dtMoviment->format('Y-m-d');


        $carteira = $this->getDoctrine()->getRepository(Carteira::class)->find($parameters['filter']['carteira']);

        $listMovs = $this->getDoctrine()->getRepository(Movimentacao::class)->findBy(['dtMoviment' => $dtMoviment, 'carteira' => $carteira], ['valor' => 'ASC']);

        $listCartoesDebito = [];
        $listDespesas = [];
        $listRetiradas = [];
        $listEntradas = [];
        $listOutras = [];

        foreach ($listMovs as $mov) {
            if ($mov->getModo()->getCodigo() == 10) {
                $listCartoesDebito[] = $mov;
            } else if ($mov->getCategoria()->getCodigoSuper() == 2 and $mov->getCategoria()->getCodigo() != 299) {
                // DESPESAS
                // tudo o que for categoria 2XXXX mas não 2.99
                $listDespesas[] = $mov;
            } else if ($mov->getCategoria()->getCodigo() == 299) {
                // RETIRADA
                // tudo o que for categoria 2.99
                $listRetiradas[] = $mov;
            } else if ($mov->getCategoria()->getCodigoSuper() == 1 and $mov->getCategoria()->getCodigo() != 199) {
                // ENTRADAS
                // tudo o que for categoria 1XXXX (exceto os 199)
                $listEntradas[] = $mov;
            } else {
                // OUTROS
                $listOutras[] = $mov;
            }
        }

        $parameters['lists']['listCartoesDebito']['titulo'] = "Cartões de Débito";
        $parameters['lists']['listCartoesDebito']['ents'] = $listCartoesDebito;
        $parameters['lists']['listCartoesDebito']['total'] = $this->getBusiness()->somarMovimentacoes($listCartoesDebito);

        $parameters['lists']['listEntradas']['titulo'] = 'Entradas';
        $parameters['lists']['listEntradas']['ents'] = $listEntradas;
        $parameters['lists']['listEntradas']['total'] = $this->getBusiness()->somarMovimentacoes($listEntradas);

        $parameters['lists']['listDespesas']['titulo'] = 'Despesas';
        $parameters['lists']['listDespesas']['ents'] = $listDespesas;
        $parameters['lists']['listDespesas']['total'] = $this->getBusiness()->somarMovimentacoes($listDespesas);

        $parameters['lists']['listRetiradas']['titulo'] = 'Retiradas';
        $parameters['lists']['listRetiradas']['ents'] = $listRetiradas;
        $parameters['lists']['listRetiradas']['total'] = $this->getBusiness()->somarMovimentacoes($listRetiradas);

        $parameters['lists']['listOutras']['titulo'] = 'Outras Movimentações';
        $parameters['lists']['listOutras']['ents'] = $listOutras;
        $parameters['lists']['listOutras']['total'] = $this->getBusiness()->somarMovimentacoes($listOutras);



        $parameters['saldoAnterior'] = $this->getDoctrine()->getRepository(Movimentacao::class)->findSaldo($dtAnterior, $carteira, 'SALDO_POSTERIOR_REALIZADAS');
        $parameters['saldoPosterior'] = $this->getDoctrine()->getRepository(Movimentacao::class)->findSaldo($dtMoviment, $carteira, 'SALDO_POSTERIOR_REALIZADAS');
        $parameters['page_title'] = "Movimentações de Caixa";

        return $this->render('Financeiro/movimentacaoCaixaList.html.twig', $parameters);
    }

    /**
     *
     * @Route("/fin/movimentacao/formCaixa/{carteira}/{movimentacao}", name="fin_movimentacao_formCaixa", defaults={"movimentacao"=null}, requirements={"carteira"="\d+","movimentacao"="\d+"})
     * @param Request $request
     * @param Carteira $carteira
     * @param Movimentacao|null $movimentacao
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function formCaixa(Request $request, Carteira $carteira, Movimentacao $movimentacao = null)
    {
        $this->getSecurityBusiness()->checkAccess('fin_movimentacao_formCaixa');

        $movimentacaoForm = $request->request->get('movimentacao');
        if (is_array($movimentacaoForm)) {
            $movimentacaoForm['tipoLancto'] = 'GERAL';
            $movimentacaoForm['recorrente'] = '0';
            $movimentacaoForm['valorTotal'] = 0.0;
            $movimentacaoForm['dtVencto'] = $movimentacaoForm['dtMoviment'];
            $movimentacaoForm['dtVenctoEfetiva'] = $movimentacaoForm['dtMoviment'];
            $movimentacaoForm['dtPagto'] = $movimentacaoForm['dtMoviment'];
            $movimentacaoForm['dtUtil'] = $movimentacaoForm['dtMoviment'];
            $movimentacaoForm['centroCusto'] = 1;
            $request->request->set('movimentacao', $movimentacaoForm);
        }

        $form = $this->createForm(MovimentacaoType::class, $movimentacao);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                try {
                    $entity = $form->getData();
                    $entity = $this->getEntityHandler()->save($entity);
                    $this->addFlash('success', 'Registro salvo com sucesso!');
                    return $this->redirectToRoute('fin_movimentacao_caixa', ['filter' => ['dtMoviment' => $entity->getDtMoviment()->format('Y-m-d')]]);
                } catch (\Exception $e) {
                    $msg = ExceptionUtils::treatException($e);
                    $this->addFlash('error', $msg);
                    $this->addFlash('error', 'Erro ao salvar!');
                }
            } else {
                $errors = $form->getErrors(true, true);
                foreach ($errors as $error) {
                    $errMsg = $error->getMessage();
                    if ($error->getMessageParameters()) {
                        foreach ($error->getMessageParameters() as $key => $p) {
                            $errMsg .= $p ? " (" . $p . ")" : '';
                        }
                    }
                    $this->addFlash('error', $errMsg);
                }
            }
        }

        // Pode ou não ter vindo algo no $parameters. Independentemente disto, só adiciono form e foi-se.
        $parameters['form'] = $form->createView();
        $parameters['page_title'] = 'Movimentação de Caixa';
        return $this->render('Financeiro/movimentacaoFormCaixa.html.twig', $parameters);
    }

}