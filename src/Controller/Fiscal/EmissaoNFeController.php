<?php

namespace App\Controller\Fiscal;

use App\Business\Base\PessoaBusiness;
use App\Business\Fiscal\NotaFiscalBusiness;
use App\Controller\FormListController;
use App\Entity\Base\Pessoa;
use App\Entity\Fiscal\NotaFiscal;
use App\Entity\Fiscal\NotaFiscalItem;
use App\EntityHandler\EntityHandler;
use App\EntityHandler\Fiscal\NotaFiscalEntityHandler;
use App\EntityHandler\Fiscal\NotaFiscalItemEntityHandler;
use App\Form\Fiscal\EmissaoFiscalType;
use App\Form\Fiscal\NotaFiscalItemType;
use App\Utils\Repository\FilterData;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EmissaoNFeController extends FormListController
{

    private $notaFiscalBusiness;

    private $pessoaBusiness;

    private $notaFiscalItemEntityHandler;

    private $notaFiscalEntityHandler;

    public function getEntityHandler(): ?EntityHandler
    {
        return $this->notaFiscalEntityHandler;
    }

    /**
     * Necessário para poder passar para o createForm.
     *
     * @return mixed
     */
    public function getTypeClass()
    {
        return NotaFiscal::class;
    }

    public function getFormRoute()
    {
        return "fis_emissaonfe_form";
    }

    public function getFormView()
    {
        return "Fiscal/emissaoNFe/formItem.html.twig";
    }

    /**
     * Deve ser informado pela subclasse para poder renderizar a view da list.
     *
     * @return mixed
     */
    public function getListView()
    {
        return "Fiscal/emissaoNFe/list.html.twig";
    }

    /**
     * Deve ser informado pela subclasse. É utilizado pela 'storedViewInfoBusiness', para 'redirectToRoute' e no 'checkAccess'.
     *
     * @return mixed
     */
    public function getListRoute()
    {
        return "fis_emissaonfe_list";
    }

    public function __construct(NotaFiscalBusiness $notaFiscalBusiness,
                                PessoaBusiness $pessoaBusiness,
                                NotaFiscalEntityHandler $notaFiscalEntityHandler,
                                NotaFiscalItemEntityHandler $notaFiscalItemEntityHandler)
    {
        $this->notaFiscalBusiness = $notaFiscalBusiness;
        $this->pessoaBusiness = $pessoaBusiness;
        $this->notaFiscalItemEntityHandler = $notaFiscalItemEntityHandler;
        $this->notaFiscalEntityHandler = $notaFiscalEntityHandler;
    }

    /**
     *
     * @Route("/fis/emissaonfe/form/{notaFiscal}", name="fis_emissaonfe_form", defaults={"notaFiscal"=null}, requirements={"notaFiscal"="\d+"})
     * @param Request $request
     * @param NotaFiscal|null $notaFiscal
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function form(Request $request, NotaFiscal $notaFiscal = null)
    {
        if (!$notaFiscal) {
            $notaFiscal = new NotaFiscal();
            $notaFiscal->setTipoNotaFiscal('NFE');
            $notaFiscal->setEntrada(false);
            $notaFiscal->setDtSaiEnt(new \DateTime('now'));
            $pessoaDestinatario = new Pessoa();
            $notaFiscal->setPessoaDestinatario($pessoaDestinatario);
            $pessoaDestinatario->setTipoPessoa('PESSOA_JURIDICA');
            $notaFiscal->setNaturezaOperacao('VENDA');
        } else {
            if ($notaFiscal->getPessoaDestinatario()) {
                $this->pessoaBusiness->fillTransients($notaFiscal->getPessoaDestinatario());
            }
        }

        // Se foi passado via post
        $data = $this->notaFiscalBusiness->notaFiscal2FormData($notaFiscal);
        $dataPosted = $request->request->get('emissao_fiscal');


        if (is_array($dataPosted)) {
            $this->notaFiscalBusiness->parseFormData($dataPosted);
            // Converto as strings vazias para null por causa do erro com Transformation dos NumberTypes
            foreach ($dataPosted as $key => $value) {
                if ($value === '') {
                    $dataPosted[$key] = null;
                }
            }
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
     * @Route("/fis/emissaonfe/faturar/{notaFiscal}", name="fis_emissaonfe_faturar", defaults={"notaFiscal"=null}, requirements={"notaFiscal"="\d+"})
     * @param Request $request
     * @param NotaFiscal|null $notaFiscal
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Exception
     */
    public function faturar(Request $request, NotaFiscal $notaFiscal = null)
    {
        if (!$notaFiscal or !$notaFiscal->getId()) {
            $this->addFlash('erro', 'E a Nota Fiscal?');
        }

        try {
            $this->notaFiscalBusiness->faturar($notaFiscal);
        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());
        }
        return $this->redirectToRoute('fis_emissaonfe_form', array(
            'notaFiscal' => $notaFiscal->getId()
        ));
    }

    /**
     *
     * @Route("/fis/emissaonfe/cancelarForm/{notaFiscal}", name="fis_emissaonfe_cancelarForm")
     * @param Request $request
     * @param NotaFiscal $notaFiscal
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function cancelarForm(Request $request, NotaFiscal $notaFiscal)
    {
        if (!$notaFiscal) {
            $this->addFlash('error', 'Nota Fiscal não encontrada!');
            return $this->redirectToRoute('fis_emissaonfe_form', array(
                'notaFiscal' => $notaFiscal->getId()
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
                return $this->redirectToRoute('fis_emissaonfe_form', array(
                    'notaFiscal' => $notaFiscal->getId()
                ));
            } else {
                $form->getErrors(true, true);
            }
        }

        $permiteCancelamento = $this->notaFiscalBusiness->permiteCancelamento($notaFiscal);
        $permiteReimpressaoCancelamento = $this->notaFiscalBusiness->permiteReimpressaoCancelamento($notaFiscal);

        $response = $this->render('Fiscal/emissaoNFe/cancelarForm.html.twig', array(
            'form' => $form->createView(),
            'notaFiscal' => $notaFiscal,
            'permiteCancelamento' => $permiteCancelamento,
            'permiteReimpressaoCancelamento' => $permiteReimpressaoCancelamento
        ));
        return $response;
    }

    /**
     *
     * @Route("/fis/emissaonfe/reimprimirCancelamento/{notaFiscal}", name="fis_emissaonfe_reimprimirCancelamento")
     */
    public function reimprimirCancelamento(Request $request, NotaFiscal $notaFiscal)
    {
        $this->notaFiscalBusiness->imprimirCancelamento($notaFiscal);
        return $this->redirectToRoute('fis_emissaonfe_form', array(
            'notaFiscal' => $notaFiscal->getId()
        ));
    }

    /**
     *
     * @Route("/fis/emissaonfe/cartaCorrecaoForm/{notaFiscal}", name="fis_emissaonfe_cartaCorrecaoForm")
     * @param Request $request
     * @param NotaFiscal $notaFiscal
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function cartaCorrecaoForm(Request $request, NotaFiscal $notaFiscal)
    {
        if (!$notaFiscal) {
            $this->addFlash('error', 'Nota Fiscal não encontrada!');
            return $this->redirectToRoute('fis_emissaonfe_form', array(
                'notaFiscal' => $notaFiscal->getId()
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
                $notaFiscal = $this->notaFiscalBusiness->formData2NotaFiscal($data);
                $notaFiscal = $this->notaFiscalBusiness->saveNotaFiscal($notaFiscal);
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
        $permiteReimpressaoCartaCorrecao = $this->notaFiscalBusiness->permiteReimpressaoCartaCorrecao($notaFiscal);

        $response = $this->render('Fiscal/emissaoNFe/cartaCorrecaoForm.html.twig', array(
            'form' => $form->createView(),
            'notaFiscal' => $notaFiscal,
            'permiteCancelamento' => $permiteCancelamento,
            'permiteReimpressaoCartaCorrecao' => $permiteReimpressaoCartaCorrecao
        ));
        return $response;
    }

    /**
     *
     * @Route("/fis/emissaonfe/consultarStatus/{notaFiscal}", name="fis_emissaonfe_consultarStatus")
     * @param Request $request
     * @param NotaFiscal $notaFiscal
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
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
     * @param Request $request
     * @param NotaFiscal $notaFiscal
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function reimprimir(Request $request, NotaFiscal $notaFiscal)
    {
        $this->notaFiscalBusiness->imprimir($notaFiscal);
        $this->addFlash('success', 'Nota Fiscal enviada para reimpressão!');
        return $this->redirectToRoute('fis_emissaonfe_form', array(
            'notaFiscal' => $notaFiscal->getId()
        ));
    }

    /**
     *
     * @Route("/fis/emissaonfe/reimprimirCartaCorrecao/{notaFiscal}", name="fis_emissaonfe_reimprimirCartaCorrecao")
     * @param Request $request
     * @param NotaFiscal $notaFiscal
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function reimprimirCartaCorrecao(Request $request, NotaFiscal $notaFiscal)
    {
        $this->notaFiscalBusiness->imprimirCartaCorrecao($notaFiscal);
        $this->addFlash('success', 'Carta de Correção enviada para reimpressão!');
        return $this->redirectToRoute('fis_emissaonfe_form', array(
            'notaFiscal' => $notaFiscal->getId()
        ));
    }

    /**
     *
     * @Route("/fis/emissaonfe/formItem/{notaFiscal}/{item}", name="fis_emissaonfe_formItem", defaults={"item"=null}, requirements={"notaFiscal"="\d+","item"="\d+"})
     * @param Request $request
     * @param NotaFiscal $notaFiscal
     * @param NotaFiscalItem|null $item
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @ParamConverter("item", class="App\Entity\Fiscal\NotaFiscalItem", options={"mapping": {"item": "id"}})
     * @throws \Doctrine\ORM\ORMException
     */
    public function formItem(Request $request, NotaFiscal $notaFiscal, NotaFiscalItem $item = null)
    {
        if (!$item) {
            $item = new NotaFiscalItem();
            $item->setNotaFiscal($notaFiscal);
        }

        if (!$item->getOrdem()) {
            $item->setOrdem(0);
        }

        $form = $this->createForm(NotaFiscalItemType::class, $item);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $item = $form->getData();
                $this->notaFiscalItemEntityHandler->save($item);
                $this->addFlash('success', 'Registro salvo com sucesso!');
                $this->notaFiscalEntityHandler->getEntityManager()->refresh($notaFiscal);
                return $this->redirectToRoute('fis_emissaonfe_form', array(
                    'notaFiscal' => $notaFiscal->getId(),
                    '_fragment' => 'itens'
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

    /**
     *
     * @Route("/fis/emissaonfe/deleteItem/{item}", name="fis_emissaonfe_deleteItem", requirements={"item"="\d+"})
     * @param Request $request
     * @param NotaFiscalItem|null $item
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteItem(Request $request, NotaFiscalItem $item)
    {
        $notaFiscalId = $item->getNotaFiscal()->getId();
        if (!$this->isCsrfTokenValid('delete', $request->request->get('token'))) {
            $this->addFlash('error', 'Erro interno do sistema.');
        } else {
            try {
                $em = $this->getDoctrine()->getEntityManager();
                $em->remove($item);
                $em->flush();
                $this->addFlash('success', 'Item deletado com sucesso.');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Erro ao deletar item.');
            }
        }

        return $this->redirectToRoute('fis_emissaonfe_form', array(
            'notaFiscal' => $notaFiscalId
        ));
    }

    /**
     * @return array|mixed
     */
    public function getNormalizeAttributes()
    {
        return [
            'attributes' => [
                'id',
                'numero',
                'serie',
                'dtEmissao' => ['timestamp'],
                'infoStatus',
                'pessoaDestinatario' => ['id', 'tipoPessoa', 'documento', 'nome', 'nomeFantasia'],
                'valorTotal',
                'updated' => ['timestamp'],
                'userUpdated' => ['id', 'nome']
            ]
        ];
    }

    public function getFilterDatas($params)
    {
        return array(
            new FilterData('tipoNotaFiscal', 'EQ', $params['filter']['tipoNotaFiscal']),
            new FilterData('numero', 'EQ', isset($params['filter']['numero']) ? $params['filter']['numero'] : null),
            new FilterData('dtEmissao', 'BETWEEN_DATE', isset($params['filter']['dtEmissao']) ? $params['filter']['dtEmissao'] : null),
            new FilterData(['p.nome', 'p.nomeFantasia'], 'LIKE', isset($params['filter']['pessoaDestinatario']) ? $params['filter']['pessoaDestinatario'] : null)
        );
    }

    /**
     *
     * @Route("/fis/emissaonfe/list/", name="fis_emissaonfe_list")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function list(Request $request)
    {
//        $parameters['filterChoices'] = $this->getFilterChoices();
        return $this->doList($request);
    }

    /**
     *
     * @Route("/fis/emissaonfe/datatablesJsList/", name="fis_emissaonfe_datatablesJsList")
     * @param Request $request
     * @return Response
     */
    public function datatablesJsList(Request $request)
    {
        $defaultFilters['filter']['tipoNotaFiscal'] = 'NFE';
        $jsonResponse = $this->doDatatablesJsList($request, $defaultFilters);
        return $jsonResponse;
    }


    /**
     * @Route("/fis/emissaonfe/clonar/{notaFiscal}", name="fis_emissaonfe_clonar")
     * @param Request $request
     * @param NotaFiscal $notaFiscal
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function clonar(Request $request, NotaFiscal $notaFiscal)
    {
        $novaNotaFiscal = $this->notaFiscalEntityHandler->doClone($notaFiscal);
        $this->addFlash('success', 'Nota Fiscal clonada!');
        return $this->redirectToRoute('fis_emissaonfe_form', array(
            'notaFiscal' => $novaNotaFiscal->getId()
        ));
    }


}