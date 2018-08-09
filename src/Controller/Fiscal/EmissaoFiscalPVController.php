<?php
namespace App\Controller\Fiscal;

use App\Business\Vendas\VendaBusiness;
use App\Entity\Base\Pessoa;
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

    public function __construct(VendaBusiness $vendaBusiness, NotaFiscalBusiness $notaFiscalBusiness)
    {
        Route::class;
        $this->vendaBusiness = $vendaBusiness;
        $this->notaFiscalBusiness = $notaFiscalBusiness;
    }

    /**
     *
     * @Route("/fis/emissaofiscalpv/ini/", name="fis_emissaofiscalpv_ini")
     */
    public function ini(Request $request)
    {
        return $this->render('Fiscal/emissaoFiscalPV/ini.html.twig');
    }

    /**
     *
     * @Route("/fis/emissaofiscalpv/form/{id}", name="fis_emissaofiscalpv_form", defaults={"id"=null}, requirements={"id"="\d+"})
     */
    public function form(Request $request, Venda $venda = null)
    {
        if (! $venda) {
            $this->addFlash('error', 'Venda não encontrada!');
            return $this->redirectToRoute('fis_emissaofiscalpv_ini');
        }
        
        // Verifica se a venda já tem uma NotaFiscal associada
        $notaFiscal = $this->getDoctrine()
            ->getRepository(NotaFiscalVenda::class)
            ->findNotaFiscalByVenda($venda);
        if (! $notaFiscal) {
            $notaFiscal = new NotaFiscal();
            $notaFiscal->setTipoNotaFiscal('NFCE');
            $pessoaDestinatario = new Pessoa();
            $notaFiscal->setPessoaDestinatario($pessoaDestinatario);
            $pessoaDestinatario->setTipoPessoa('PESSOA_FISICA');
        } else {
//             $notaFiscal = $this->notaFiscalBusiness->consultarStatus($notaFiscal);
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
                $notaFiscal = $this->notaFiscalBusiness->saveNotaFiscalVenda($venda, $data);
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
        $permiteCartaCorrecao = $this->notaFiscalBusiness->permiteCartaCorrecao($notaFiscal);
        
        $response = $this->render('Fiscal/emissaoFiscalPV/form.html.twig', array(
            'form' => $form->createView(),
            'venda' => $venda,
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
     * Transforma um objeto NotaFiscal em um array com os dados para o EmissaoFiscalType.
     *
     * @param NotaFiscal $notaFiscal
     * @return NULL[]|string[]|\App\Entity\Financeiro\TipoPessoa[]
     */
    private function notaFiscal2FormData(NotaFiscal $notaFiscal)
    {
        $formData = array();
        
        if ($notaFiscal->getPessoaDestinatario()) {
            $tipoPessoa = $notaFiscal->getPessoaDestinatario()->getTipoPessoa();
        } else {
            $tipoPessoa = 'PESSOA_FISICA';
        }
        
        // Passo para o EmissaoFiscalType para que possa decidir se os inputs serão desabilitados.
        $formData['permiteFaturamento'] = $this->notaFiscalBusiness->permiteFaturamento($notaFiscal);
        
        $formData['uuid'] = $notaFiscal->getUuid();
        $formData['dtEmissao'] = $notaFiscal->getDtEmissao();
        $formData['dtSaiEnt'] = $notaFiscal->getDtSaiEnt();
        $formData['numero'] = $notaFiscal->getNumero();
        $formData['serie'] = $notaFiscal->getSerie();
        $formData['ambiente'] = $notaFiscal->getAmbiente();
        $formData['spartacusStatus'] = $notaFiscal->getSpartacusStatus();
        $formData['spartacusStatusReceita'] = $notaFiscal->getSpartacusStatusReceita();
        $formData['spartacusMensretornoReceita'] = $notaFiscal->getSpartacusMensretornoReceita();
        
        $formData['cancelamento_motivo'] = $notaFiscal->getMotivoCancelamento();
        
        $formData['_info_status'] = $notaFiscal->getInfoStatus();
        
        $formData['tipo'] = $notaFiscal->getTipoNotaFiscal();
        $formData['tipoPessoa'] = $tipoPessoa;
        
        if ($notaFiscal->getPessoaDestinatario()) {
            $formData['pessoa_id'] = $notaFiscal->getPessoaDestinatario()->getId();
            if ($tipoPessoa == 'PESSOA_FISICA') {
                $formData['cpf'] = $notaFiscal->getPessoaDestinatario()->getDocumento();
                $formData['nome'] = $notaFiscal->getPessoaDestinatario()->getNome();
            } else if ($tipoPessoa == 'PESSOA_JURIDICA') {
                $formData['cnpj'] = $notaFiscal->getPessoaDestinatario()->getDocumento();
                $formData['razao_social'] = $notaFiscal->getPessoaDestinatario()->getNome();
                $formData['nome_fantasia'] = $notaFiscal->getPessoaDestinatario()->getNomeFantasia();
            }
        }
        if ($notaFiscal->getTipoNotaFiscal() == 'NFE') {
            
            if ($notaFiscal->getPessoaDestinatario() and $notaFiscal->getPessoaDestinatario()->getEndereco()) {
                $formData['logradouro'] = $notaFiscal->getPessoaDestinatario()
                    ->getEndereco()
                    ->getLogradouro();
                $formData['numero'] = $notaFiscal->getPessoaDestinatario()
                    ->getEndereco()
                    ->getNumero();
                $formData['complemento'] = $notaFiscal->getPessoaDestinatario()
                    ->getEndereco()
                    ->getComplemento();
                $formData['bairro'] = $notaFiscal->getPessoaDestinatario()
                    ->getEndereco()
                    ->getBairro();
                $formData['cidade'] = $notaFiscal->getPessoaDestinatario()
                    ->getEndereco()
                    ->getCidade();
                $formData['estado'] = $notaFiscal->getPessoaDestinatario()
                    ->getEndereco()
                    ->getEstado();
                $formData['cep'] = $notaFiscal->getPessoaDestinatario()
                    ->getEndereco()
                    ->getCep();
            }
        }
        
        return $formData;
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
        if (! $venda) {
            $this->addFlash('error', 'PV não encontrado!');
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
     * @Route("/fis/emissaofiscalpv/reimprimirCartaCorrecao/{notaFiscal}/{venda}", name="fis_emissaofiscalpv_reimprimirCartaCorrecao")
     */
    public function reimprimirCartaCorrecao(Request $request, NotaFiscal $notaFiscal, Venda $venda)
    {
        $this->notaFiscalBusiness->imprimirCartaCorrecao($notaFiscal);
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