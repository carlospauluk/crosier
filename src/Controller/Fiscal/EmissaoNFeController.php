<?php
namespace App\Controller\Fiscal;

use App\Business\Fiscal\NotaFiscalBusiness;
use App\Business\Vendas\VendaBusiness;
use App\Entity\Base\Pessoa;
use App\Entity\Fiscal\NotaFiscal;
use App\Entity\Vendas\Venda;
use App\Form\Fiscal\EmissaoFiscalType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class EmissaoNFeController extends Controller
{

    private $notaFiscalBusiness;

    public function __construct(VendaBusiness $vendaBusiness, NotaFiscalBusiness $notaFiscalBusiness)
    {
        Route::class;
        $this->notaFiscalBusiness = $notaFiscalBusiness;
    }

    /**
     *
     * @Route("/fis/emissaonfe/form/{notaFiscal}", name="fis_emissaonfe_form", defaults={"notaFiscal"=null}, requirements={"notaFiscal"="\d+"})
     */
    public function form(Request $request, NotaFiscal $notaFiscal = null)
    {
        if (! $notaFiscal) {
            $notaFiscal = new NotaFiscal();
            $notaFiscal->setTipoNotaFiscal('NFE');
            $pessoaDestinatario = new Pessoa();
            $notaFiscal->setPessoaDestinatario($pessoaDestinatario);
            $pessoaDestinatario->setTipoPessoa('PESSOA_JURIDICA');
        }
        
        // Se foi passado via post
        $data = $this->notaFiscalBusiness->notaFiscal2FormData($notaFiscal);
        $dataPosted = $request->request->get('emissao_fiscal_pv');
        if (is_array($dataPosted)) {
            $data = array_merge($data, $dataPosted);
        }
        
        $form = $this->createForm(EmissaoFiscalType::class, $data);
        $form->handleRequest($request);
        
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $notaFiscal = $this->notaFiscalBusiness->faturar($notaFiscal);
                $data = $this->notaFiscalBusiness->notaFiscal2FormData($notaFiscal);
                return $this->redirectToRoute('fis_emissaoNFE_form', array(
                    'notaFiscal' => $notaFiscal->getId()
                ));
            } else {
                $form->getErrors(true, true);
            }
        }
        
        $permiteFaturamento = $this->notaFiscalBusiness->permiteFaturamento($notaFiscal);
        $permiteReimpressao = $this->notaFiscalBusiness->permiteReimpressao($notaFiscal);
        $permiteReimpressaoCancelamento = $this->notaFiscalBusiness->permiteReimpressaoCancelamento($notaFiscal);
        $permiteCancelamento = $this->notaFiscalBusiness->permiteCancelamento($notaFiscal);
        $permiteCartaCorrecao = $this->notaFiscalBusiness->permiteCartaCorrecao($notaFiscal);
        
        $response = $this->render('Fiscal/emissaoNFe/form.html.twig', array(
            'form' => $form->createView(),
            'notaFiscal' => $notaFiscal,
            'permiteFaturamento' => $permiteFaturamento,
            'permiteCancelamento' => $permiteCancelamento,
            'permiteReimpressao' => $permiteReimpressao,
            'permiteReimpressaoCancelamento' => $permiteReimpressaoCancelamento,
            'permiteCartaCorrecao' => $permiteCartaCorrecao
        ));
        return $response;
    }

    

    /**
     *
     * @Route("/fis/emissaofiscalpv/cancelarForm/{notaFiscal}/{venda}", name="fis_emissaofiscalpv_cancelarForm")
     */
    public function cancelarForm(Request $request, NotaFiscal $notaFiscal, Venda $venda)
    {
        if (! $venda) {
            $this->addFlash('error', 'Venda não encontrada!');
            return $this->redirectToRoute('fis_emissaofiscalpv_ini');
        }
        if (! $notaFiscal) {
            $this->addFlash('error', 'Venda não encontrada!');
            return $this->redirectToRoute('fis_emissaofiscalpv_form', array(
                'id' => $venda->getId()
            ));
        }
        
        // Se foi passado via post
        $data = $this->notaFiscalBusiness->notaFiscal2FormData($notaFiscal);
        $dataPosted = $request->request->get('emissao_fiscal_pv');
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
     * @Route("/fis/emissaofiscalpv/cartaCorrecaoForm/{notaFiscal}/{venda}", name="fis_emissaofiscalpv_cartaCorrecaoForm")
     */
    public function cartaCorrecaoForm(Request $request, NotaFiscal $notaFiscal, Venda $venda)
    {
        if (! $venda) {
            $this->addFlash('error', 'Venda não encontrada!');
            return $this->redirectToRoute('fis_emissaofiscalpv_ini');
        }
        if (! $notaFiscal) {
            $this->addFlash('error', 'Venda não encontrada!');
            return $this->redirectToRoute('fis_emissaofiscalpv_form', array(
                'id' => $venda->getId()
            ));
        }
        
        // Se foi passado via post
        $data = $this->notaFiscalBusiness->notaFiscal2FormData($notaFiscal);
        $dataPosted = $request->request->get('emissao_fiscal_pv');
        if (is_array($dataPosted)) {
            $data = array_merge($data, $dataPosted);
        }
        $form = $this->createForm(EmissaoFiscalType::class, $data);
        $form->handleRequest($request);
        
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $notaFiscal->setCartaCorrecao($data['carta_correcao']);
                $notaFiscal = $this->notaFiscalBusiness->cartaCorrecao($notaFiscal);
                return $this->redirectToRoute('fis_emissaofiscalpv_form', array(
                    'id' => $venda->getId()
                ));
            } else {
                $form->getErrors(true, true);
            }
        }
        
        // Mantenho pois as regras pra cancelamento e pra carta de correção são as mesmas
        $permiteCancelamento = $this->notaFiscalBusiness->permiteCancelamento($notaFiscal);
        $permiteReimpressaoCancelamento = $this->notaFiscalBusiness->permiteReimpressaoCancelamento($notaFiscal);
        
        $response = $this->render('Fiscal/emissaoFiscalPV/cartaCorrecaoForm.html.twig', array(
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
     * @Route("/fis/emissaonfe/consultarStatus/{notaFiscal}", name="fis_emissaofiscalpv_consultarStatus")
     */
    public function consultarStatus(Request $request, NotaFiscal $notaFiscal)
    {
        $notaFiscal = $this->notaFiscalBusiness->consultarStatus($notaFiscal);
        return $this->redirectToRoute('fis_emissaonfe_form', array(
            'notaFiscal' => $notaFiscal->getId()
        ));
    }
}