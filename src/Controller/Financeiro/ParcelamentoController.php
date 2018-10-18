<?php

namespace App\Controller\Financeiro;

use App\Business\Security\SecurityBusiness;
use App\Entity\Financeiro\Movimentacao;
use App\Form\Financeiro\MovimentacaoType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Date;

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
    public function getSecurityBusiness()
    {
        return $this->securityBusiness;
    }

    /**
     *
     * @Route("/fin/parcelamento/movimentacaoForm", name="fin_parcelamento_movimentacaoForm")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \ReflectionException
     * @throws \Exception
     */
    public function form(Request $request)
    {
        $this->securityBusiness->checkAccess('fin_parcelamento_movimentacaoForm');

        $movimentacaoForm = $this->createForm(MovimentacaoType::class);

        if ($request->get('movimentacaoForm_btnSubmit')) {
            $movimentacao = $request->request->get('movimentacao');
            $movimentacao['valor'] = 0.0;
            $movimentacao['descontos'] = 0.0;
            $movimentacao['acrescimos'] = 0.0;
            $movimentacao['valor_total'] = 0.0;
            $movimentacao['dtVenctoEfetiva'] = '01/01/1900';
            $request->request->set('movimentacao', $movimentacao);
        }

        $movimentacaoForm->handleRequest($request);

        if ($movimentacaoForm->isSubmitted()) {
            if ($movimentacaoForm->isValid()) {
                try {
                    $entity = $movimentacaoForm->getData();
                    $this->addFlash('success', 'Registro salvo com sucesso!');
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
        $parameters['movimentacaoForm'] = $movimentacaoForm->createView();
        $parameters['page_title'] = 'Lançamento de Parcelamento';
        return $this->render('Financeiro/parcelamentoForm.html.twig', $parameters);
    }

    /**
     *
     * @Route("/fin/parcelamento/list/", name="fin_parcelamento_list")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \ReflectionException
     * @throws \Exception
     */
    public function list(Request $request)
    {
//        $parameters['filterChoices'] = $this->getFilterChoices();
//        return $this->doList($request, $parameters);
        return new Response();
    }


    /**
     *
     * @Route("/fin/movimentacao/delete/{id}/", name="fin_movimentacao_delete", requirements={"id"="\d+"})
     * @param Request $request
     * @param Movimentacao $movimentacao
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function delete(Request $request, Movimentacao $movimentacao)
    {
        return $this->doDelete($request, $movimentacao);
    }

}