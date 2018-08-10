<?php
namespace App\Controller\Fiscal;

use App\Business\Fiscal\NotaFiscalBusiness;
use App\Entity\Base\Pessoa;
use App\Entity\Fiscal\NotaFiscal;
use App\Form\Fiscal\EmissaoFiscalType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Fiscal\NotaFiscalItem;
use App\Form\Fiscal\NotaFiscalItemType;
use App\Business\Base\EntityIdBusiness;

class EmissaoNFeController extends Controller
{

    private $notaFiscalBusiness;
    
    private $entityIdBusiness;

    public function __construct(NotaFiscalBusiness $notaFiscalBusiness, EntityIdBusiness $entityIdBusiness)
    {
        Route::class;
        $this->notaFiscalBusiness = $notaFiscalBusiness;
        $this->entityIdBusiness = $entityIdBusiness;
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
            $notaFiscal->setEntrada(false);
            $pessoaDestinatario = new Pessoa();
            $notaFiscal->setPessoaDestinatario($pessoaDestinatario);
            $pessoaDestinatario->setTipoPessoa('PESSOA_JURIDICA');
        }
        
        // Se foi passado via post
        $data = $this->notaFiscalBusiness->notaFiscal2FormData($notaFiscal);
        $dataPosted = $request->request->get('emissao_fiscal');
        
        if (! $dataPosted) {
            $dataPosted['tipoPessoa'] = 'PESSOA_JURIDICA';
        }
        
        if (is_array($dataPosted)) {
            $data = array_merge($data, $dataPosted);
        }
        
        $form = $this->createForm(EmissaoFiscalType::class, $data);
        $form->handleRequest($request);
        
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $notaFiscal = $this->notaFiscalBusiness->formData2NotaFiscal($data);
                $notaFiscal = $this->notaFiscalBusiness->saveNotaFiscal($notaFiscal);
                $data = $this->notaFiscalBusiness->notaFiscal2FormData($notaFiscal);
                return $this->redirectToRoute('fis_emissaonfe_form', array(
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
     * @Route("/fis/emissaonfe/cancelarForm/{notaFiscal}", name="fis_emissaonfe_cancelarForm")
     */
    public function cancelarForm(Request $request, NotaFiscal $notaFiscal)
    {
        if (! $notaFiscal) {
            $this->addFlash('error', 'Nota Fiscal não encontrada!');
            return $this->redirectToRoute('fis_emissaonfe_form', array(
                'notaFiscal' => $notaFiscal->getId()
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
                return $this->redirectToRoute('fis_emissaonfe_form', array(
                    'notaFiscal' => $notaFiscal->getId()
                ));
            } else {
                $form->getErrors(true, true);
            }
        }
        
        $permiteCancelamento = $this->notaFiscalBusiness->permiteCancelamento($notaFiscal);
        $permiteReimpressaoCancelamento = $this->notaFiscalBusiness->permiteReimpressaoCancelamento($notaFiscal);
        
        $response = $this->render('Fiscal/emissaonfe/cancelarForm.html.twig', array(
            'form' => $form->createView(),
            'notaFiscal' => $notaFiscal,
            'permiteCancelamento' => $permiteCancelamento,
            'permiteReimpressaoCancelamento' => $permiteReimpressaoCancelamento
        ));
        return $response;
    }

    /**
     *
     * @Route("/fis/emissaonfe/cartaCorrecaoForm/{notaFiscal}", name="fis_emissaonfe_cartaCorrecaoForm")
     */
    public function cartaCorrecaoForm(Request $request, NotaFiscal $notaFiscal)
    {
        if (! $notaFiscal) {
            $this->addFlash('error', 'Nota Fiscal não encontrada!');
            return $this->redirectToRoute('fis_emissaonfe_form', array(
                'notaFiscal' => $notaFiscal->getId()
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
                return $this->redirectToRoute('fis_emissaonfe_form', array(
                    'notaFiscal' => $notaFiscal->getId()
                ));
            } else {
                $form->getErrors(true, true);
            }
        }
        
        // Mantenho pois as regras pra cancelamento e pra carta de correção são as mesmas
        $permiteCancelamento = $this->notaFiscalBusiness->permiteCancelamento($notaFiscal);
        $permiteReimpressaoCancelamento = $this->notaFiscalBusiness->permiteReimpressaoCancelamento($notaFiscal);
        
        $response = $this->render('Fiscal/emissaonfe/cartaCorrecaoForm.html.twig', array(
            'form' => $form->createView(),
            'notaFiscal' => $notaFiscal,
            'permiteCancelamento' => $permiteCancelamento,
            'permiteReimpressaoCancelamento' => $permiteReimpressaoCancelamento
        ));
        return $response;
    }

    /**
     *
     * @Route("/fis/emissaonfe/consultarStatus/{notaFiscal}", name="fis_emissaonfe_consultarStatus")
     */
    public function consultarStatus(Request $request, NotaFiscal $notaFiscal)
    {
        $notaFiscal = $this->notaFiscalBusiness->consultarStatus($notaFiscal);
        return $this->redirectToRoute('fis_emissaonfe_form', array(
            'notaFiscal' => $notaFiscal->getId()
        ));
    }

    /**
     *
     * @Route("/fis/emissaonfe/reimprimir/{notaFiscal}", name="fis_emissaonfe_reimprimir")
     */
    public function reimprimir(Request $request, NotaFiscal $notaFiscal)
    {
        $this->notaFiscalBusiness->imprimir($notaFiscal);
        return $this->redirectToRoute('fis_emissaonfe_form', array(
            'notaFiscal' => $notaFiscal->getId()
        ));
    }

    /**
     *
     * @Route("/fis/emissaonfe/formItem/{notaFiscal}/{notaFiscalItem}", name="fis_emissaonfe_formItem", defaults={"notaFiscalItem"=null}, requirements={"notaFiscal"="\d+","notaFiscalItem"="\d+"})
     */
    public function formItem(Request $request, NotaFiscal $notaFiscal, NotaFiscalItem $item = null)
    {
        if (! $item) {
            $item = new NotaFiscalItem();
            $item->setNotaFiscal($notaFiscal);
            $item->setOrdem(1);
            $this->entityIdBusiness->handlePersist($item);
        }
        
        $form = $this->createForm(NotaFiscalItemType::class, $item);
        
        $form->handleRequest($request);
        
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                // $form->getData() holds the submitted values
                // but, the original `$task` variable has also been updated
                $item = $form->getData();
                
                // ... perform some action, such as saving the task to the database
                // for example, if Task is a Doctrine entity, save it!
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($item);
                $entityManager->flush();
                $this->addFlash('success', 'Registro salvo com sucesso!');
                return $this->redirectToRoute('fis_emissaonfe_form', array(
                    'notaFiscal' => $notaFiscal->getId()
                ));
            } else {
                $form->getErrors(true, false);
            }
        }
        return $this->render('Fiscal/emissaoNFe/formItem.html.twig', array(
            'form' => $form->createView(),
            'notaFiscal' => $notaFiscal 
        ));
    }
}