<?php
namespace App\Controller\Fiscal;

use App\Business\Vendas\VendaBusiness;
use App\Entity\Base\Pessoa;
use App\Entity\Fiscal\NotaFiscal;
use App\Entity\Fiscal\NotaFiscalVenda;
use App\Entity\Vendas\Venda;
use App\Form\Fiscal\EmissaoFiscalPVType;
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
        
        $notaFiscal = null;
        
        // Se foi passado via post
        $data = $request->request->get('emissao_fiscal_pv');
        if (! $data) {
            // Verifica se a venda já tem uma NotaFiscal associada
            $notaFiscal = $this->getDoctrine()
                ->getRepository(NotaFiscalVenda::class)
                ->findNotaFiscalByVenda($venda);
            
            // Se não tem, monta os valores para o form
            if (! $notaFiscal) {
                $notaFiscal = new NotaFiscal();
                $notaFiscal->setTipoNotaFiscal('NFCE');
                $pessoaDestinatario = new Pessoa();
                $notaFiscal->setPessoaDestinatario($pessoaDestinatario);
                $pessoaDestinatario->setTipoPessoa('PESSOA_FISICA');
            } else {
                $notaFiscal = $this->notaFiscalBusiness->consultarStatus($notaFiscal);
            }
            $data = $this->notaFiscal2FormData($notaFiscal);
        } else {
            $notaFiscal = $this->vendaBusiness->saveNotaFiscalVenda($venda, $data);
            $this->notaFiscalBusiness->faturar($notaFiscal);
        }
        
        
        $form = $this->createForm(EmissaoFiscalPVType::class, $data);
        
        // Chamado aqui para setar os 'totalItem'
        $this->vendaBusiness->recalcularTotais($venda);
        
        $response = $this->render('Fiscal/emissaoFiscalPV/form.html.twig', array(
            'form' => $form->createView(),
            'venda' => $venda,
            'notaFiscal' => $notaFiscal
        ));
        return $response;
    }

    /**
     * Transforma um objeto NotaFiscal em um array com os dados para o EmissaoFiscalPVType.
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
        $formData['spartacusMensretorno'] = $notaFiscal->getSpartacusMensretorno();
        
        $formData['_info_status'] = $notaFiscal->getInfoStatus();
        
        $formData['tipo'] = $notaFiscal->getTipoNotaFiscal();
        $formData['tipoPessoa'] = $tipoPessoa;
        
        if ($tipoPessoa == 'PESSOA_FISICA') {
            if ($notaFiscal->getPessoaDestinatario()) {
                $formData['cpf'] = $notaFiscal->getPessoaDestinatario()->getDocumento();
                $formData['nome'] = $notaFiscal->getPessoaDestinatario()->getNome();
            }
        } else if ($tipoPessoa == 'PESSOA_JURIDICA') {
            if ($notaFiscal->getPessoaDestinatario()) {
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
}