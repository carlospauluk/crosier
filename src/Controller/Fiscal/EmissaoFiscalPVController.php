<?php

namespace App\Controller\Fiscal;

use App\Business\CRM\ClienteBusiness;
use App\Business\Vendas\VendaBusiness;
use App\Entity\Base\Pessoa;
use App\Entity\Fiscal\FinalidadeNF;
use App\Entity\Fiscal\NotaFiscal;
use App\Entity\Fiscal\NotaFiscalVenda;
use App\Entity\Vendas\Venda;
use App\Form\Fiscal\EmissaoFiscalType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Business\Fiscal\NotaFiscalBusiness;

class EmissaoFiscalPVController extends Controller
{

    private $vendaBusiness;

    private $notaFiscalBusiness;

    public function __construct(VendaBusiness $vendaBusiness,
                                NotaFiscalBusiness $notaFiscalBusiness)
    {
        $this->vendaBusiness = $vendaBusiness;
        $this->notaFiscalBusiness = $notaFiscalBusiness;
    }

    /**
     *
     * @Route("/fis/emissaofiscalpv/ini/", name="fis_emissaofiscalpv_ini")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function ini(Request $request)
    {
        $tudook = $this->vendaBusiness->checkAcessoPVs();
        if (!$tudook) {
            $this->addFlash('error', 'Pasta dos PVs no EKT indisponível.');
        } else {
            $this->addFlash('info', 'Arquivos EKT disponíveis!');
        }
        return $this->render('Fiscal/emissaoFiscalPV/ini.html.twig', array('tudook' => $tudook));
    }

    /**
     *
     * @Route("/fis/emissaofiscalpv/form/{id}", name="fis_emissaofiscalpv_form", defaults={"id"=null}, requirements={"id"="\d+"})
     * @param Request $request
     * @param Venda|null $venda
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function form(Request $request, Venda $venda = null)
    {
        if (!$venda) {
            $this->addFlash('error', 'Venda não encontrada!');
            return $this->redirectToRoute('fis_emissaofiscalpv_ini');
        }

        // Verifica se a venda já tem uma NotaFiscal associada
        $notaFiscal = $this->getDoctrine()
            ->getRepository(NotaFiscalVenda::class)
            ->findNotaFiscalByVenda($venda);
        if (!$notaFiscal) {
            $notaFiscal = new NotaFiscal();
            $notaFiscal->setTipoNotaFiscal('NFCE');
            $notaFiscal->setFinalidadeNf(FinalidadeNF::NORMAL['key']);
            $pessoaDestinatario = new Pessoa();
            $notaFiscal->setPessoaDestinatario($pessoaDestinatario);
            $pessoaDestinatario->setTipoPessoa('PESSOA_FISICA');
        }

        // Se foi passado via post
        $data = $this->notaFiscalBusiness->notaFiscal2FormData($notaFiscal);
        $dataPosted = $request->request->get('emissao_fiscal');
        if (is_array($dataPosted)) {
            $data = array_merge($data, $dataPosted);
        }

        $form = $this->createForm(EmissaoFiscalType::class, $data);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $notaFiscal = $this->notaFiscalBusiness->formData2NotaFiscal($data);
                $notaFiscal = $this->notaFiscalBusiness->saveNotaFiscalVenda($venda, $notaFiscal);
                $notaFiscal = $this->notaFiscalBusiness->faturar($notaFiscal);
                $data = $this->notaFiscalBusiness->notaFiscal2FormData($notaFiscal);
                return $this->redirectToRoute('fis_emissaofiscalpv_form', array(
                    'id' => $venda->getId()
                ));
            } else {
                $form->getErrors(true, true);
            }
        }

        // Chamado aqui para setar os 'totalItem'
        $this->vendaBusiness->recalcularTotais($venda);

        $permiteFaturamento = $this->notaFiscalBusiness->permiteFaturamento($notaFiscal);
        $permiteReimpressao = $this->notaFiscalBusiness->permiteReimpressao($notaFiscal);
        $permiteReimpressaoCancelamento = $this->notaFiscalBusiness->permiteReimpressaoCancelamento($notaFiscal);
        $permiteCancelamento = $this->notaFiscalBusiness->permiteCancelamento($notaFiscal);

        $response = $this->render('Fiscal/emissaoFiscalPV/form.html.twig', array(
            'form' => $form->createView(),
            'venda' => $venda,
            'notaFiscal' => $notaFiscal,
            'permiteFaturamento' => $permiteFaturamento,
            'permiteCancelamento' => $permiteCancelamento,
            'permiteReimpressao' => $permiteReimpressao,
            'permiteReimpressaoCancelamento' => $permiteReimpressaoCancelamento
        ));
        return $response;
    }

    /**
     *
     * @Route("/fis/emissaofiscalpv/processarPV/{pv}", name="fis_emissaofiscalpv_processarPV", defaults={"pv"=null}, requirements={"pv"="\d+"})
     */
    public function processarPV(Request $request, $pv)
    {
        // Processa os arquivos do EKT para gerar a venda
        $this->vendaBusiness->processarTXTsEKTeApagarArquivos();

        $venda = $this->getDoctrine()
            ->getRepository(Venda::class)
            ->findByPV($pv);
        if (!$venda) {
            $this->addFlash('error', 'Venda não encontrada!');
            return $this->redirectToRoute('fis_emissaofiscalpv_ini');
        }

        return $this->redirectToRoute('fis_emissaofiscalpv_form', array(
            'id' => $venda->getId()
        ));
    }

    /**
     *
     * @Route("/fis/emissaofiscalpv/reimprimir/{notaFiscal}/{venda}", name="fis_emissaofiscalpv_reimprimir")
     */
    public function reimprimir(Request $request, NotaFiscal $notaFiscal, Venda $venda)
    {
        $this->notaFiscalBusiness->imprimir($notaFiscal);
        return $this->redirectToRoute('fis_emissaofiscalpv_form', array(
            'id' => $venda->getId()
        ));
    }

    /**
     *
     * @Route("/fis/emissaofiscalpv/reimprimirCancelamento/{notaFiscal}/{venda}", name="fis_emissaofiscalpv_reimprimirCancelamento")
     */
    public function reimprimirCancelamento(Request $request, NotaFiscal $notaFiscal, Venda $venda)
    {
        $this->notaFiscalBusiness->imprimirCancelamento($notaFiscal);
        return $this->redirectToRoute('fis_emissaofiscalpv_form', array(
            'id' => $venda->getId()
        ));
    }

    /**
     *
     * @Route("/fis/emissaofiscalpv/cancelarForm/{notaFiscal}/{venda}", name="fis_emissaofiscalpv_cancelarForm")
     */
    public function cancelarForm(Request $request, NotaFiscal $notaFiscal, Venda $venda)
    {
        if (!$venda) {
            $this->addFlash('error', 'Venda não encontrada!');
            return $this->redirectToRoute('fis_emissaofiscalpv_ini');
        }
        if (!$notaFiscal) {
            $this->addFlash('error', 'Venda não encontrada!');
            return $this->redirectToRoute('fis_emissaofiscalpv_form', array(
                'id' => $venda->getId()
            ));
        }

        // Se foi passado via post
        $data = $this->notaFiscalBusiness->notaFiscal2FormData($notaFiscal);
        $dataPosted = $request->request->get('emissao_fiscal');
        if (is_array($dataPosted)) {
            $data = array_merge($data, $dataPosted);
        }
        $form = $this->createForm(EmissaoFiscalType::class, $data);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $notaFiscal->setMotivoCancelamento($data['cancelamento_motivo']);
                $notaFiscal = $this->notaFiscalBusiness->cancelar($notaFiscal);
                return $this->redirectToRoute('fis_emissaofiscalpv_form', array(
                    'id' => $venda->getId()
                ));
            } else {
                $form->getErrors(true, true);
            }
        }

        $permiteCancelamento = $this->notaFiscalBusiness->permiteCancelamento($notaFiscal);
        $permiteReimpressaoCancelamento = $this->notaFiscalBusiness->permiteReimpressaoCancelamento($notaFiscal);

        $response = $this->render('Fiscal/emissaoFiscalPV/cancelarForm.html.twig', array(
            'form' => $form->createView(),
            'venda' => $venda,
            'notaFiscal' => $notaFiscal,
            'permiteCancelamento' => $permiteCancelamento,
            'permiteReimpressaoCancelamento' => $permiteReimpressaoCancelamento
        ));
        return $response;
    }

    /**
     *
     * @Route("/fis/emissaofiscalpv/consultarStatus/{notaFiscal}/{venda}", name="fis_emissaofiscalpv_consultarStatus")
     */
    public function consultarStatus(Request $request, NotaFiscal $notaFiscal, Venda $venda)
    {
        $notaFiscal = $this->notaFiscalBusiness->consultarStatus($notaFiscal);
        return $this->redirectToRoute('fis_emissaofiscalpv_form', array(
            'id' => $venda->getId()
        ));
    }
}