<?php

namespace App\Controller\CRM;


use App\Business\CRM\ClienteBusiness;
use App\Controller\Base\EnderecoController;
use App\Controller\FormListController;
use App\Entity\Base\Endereco;
use App\Entity\Base\Pessoa;
use App\Entity\CRM\Cliente;
use App\EntityHandler\Base\EnderecoEntityHandler;
use App\EntityHandler\CRM\ClienteEntityHandler;
use App\EntityHandler\EntityHandler;
use App\Form\Base\EnderecoType;
use App\Form\CRM\ClienteType;
use App\Utils\Repository\FilterData;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ClienteController
 * @package App\Controller\Financeiro
 * @author Carlos Eduardo Pauluk
 */
class ClienteController extends FormListController
{

    private $entityHandler;

    private $enderecoEntityHandler;

    private $clienteBusiness;

    private $enderecoController;

    public function __construct(ClienteEntityHandler $entityHandler,
                                EnderecoEntityHandler $enderecoEntityHandler,
                                ClienteBusiness $clienteBusiness,
                                EnderecoController $enderecoController)
    {
        $this->entityHandler = $entityHandler;
        $this->enderecoEntityHandler = $enderecoEntityHandler;
        $this->clienteBusiness = $clienteBusiness;
        $this->enderecoController = $enderecoController;
        $this->enderecoController->setRouteToRedirect('crm_cliente_form');
    }

    public function getEntityHandler(): ?EntityHandler
    {
        return $this->entityHandler;
    }

    public function getEnderecoEntityHandler(): ?EnderecoEntityHandler
    {
        return $this->enderecoEntityHandler;
    }


    public function getFormRoute()
    {
        return 'crm_cliente_form';
    }

    public function getFormView()
    {
        return 'CRM/clienteForm.html.twig';
    }

    public function getFilterDatas($params)
    {
        return array(
            new FilterData(['p.nome','p.documento'], 'LIKE', $params['filter']['p_nome'])
        );
    }

    public function getListView()
    {
        return 'CRM/clienteList.html.twig';
    }

    public function getListRoute()
    {
        return 'crm_cliente_list';
    }


    public function getTypeClass()
    {
        return ClienteType::class;
    }

    /**
     *
     * @Route("/crm/cliente/form/{id}", name="crm_cliente_form", defaults={"id"=null}, requirements={"id"="\d+"})
     * @param Request $request
     * @param Cliente|null $cliente
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function form(Request $request, Cliente $cliente = null)
    {
        $this->getSecurityBusiness()->checkAccess("crm_cliente_form");
        if (!$cliente) {
            $cliente = new Cliente();
            $pessoa = new Pessoa();
            $pessoa->setTipoPessoa('PESSOA_FISICA');
            $cliente->setPessoa($pessoa);
        }

        // Se foi passado via post
        $data = $this->clienteBusiness->cliente2FormData($cliente);
        $dataPosted = $request->request->get('cliente');
        if (is_array($dataPosted)) {
            $this->clienteBusiness->parseFormData($dataPosted);
            $data = array_merge($data, $dataPosted);
        }

        $form = $this->createForm($this->getTypeClass(), $data);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $cliente = $this->clienteBusiness->formData2Cliente($data);
                $this->getEntityHandler()->save($cliente);
                $this->addFlash('success', 'Registro salvo com sucesso!');
                return $this->redirectToRoute($this->getFormRoute(), array('id' => $cliente->getId()));
            } else {
                $form->getErrors(true, false);
            }
        }

        $formEnderecoView = null;
        if ($cliente->getId()) {
            $endereco = new Endereco();
            $formEndereco = $this->createForm(EnderecoType::class, $endereco,
                array('action' => $this->generateUrl('crm_cliente_enderecoForm', array('endereco' => $endereco->getId(), 'ref' => $cliente->getId()))));
            $formEnderecoView = $formEndereco->createView();
        }

        return $this->render($this->getFormView(), array(
            'ref' => $cliente,
            'form' => $form->createView(),
            'formEndereco' => $formEnderecoView,
            'page_title' => $this->getFormPageTitle()
        ));
    }

    /**
     *
     * @Route("/crm/cliente/enderecoForm/{ref}/{endereco}", name="crm_cliente_enderecoForm", defaults={"endereco"=null})
     * @param Request $request
     * @param Cliente $ref
     * @param Endereco|null $endereco
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @ParamConverter("ref", class="App\Entity\CRM\Cliente", options={"mapping": {"ref": "id"}})
     */
    public function enderecoForm(Request $request, Cliente $ref, Endereco $endereco = null)
    {
        return $this->enderecoController->doEnderecoForm($request, $ref, $endereco);
    }

    /**
     *
     * @Route("/crm/cliente/enderecoDelete/{ref}/{endereco}", name="crm_cliente_enderecoDelete", requirements={"ref"="\d+","endereco"="\d+"})
     * @param Request $request
     * @param Cliente $ref
     * @param Endereco|null $endereco
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function enderecoDelete(Request $request, Cliente $ref, Endereco $endereco)
    {
        $this->getSecurityBusiness()->checkAccess("crm_cliente_form");
        return $this->enderecoController->doEnderecoDelete($request, $ref, $endereco);
    }

    /**
     *
     * @Route("/crm/cliente/list/", name="crm_cliente_list")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function list(Request $request)
    {
        return $this->doList($request);
    }

    /**
     * @return array|mixed
     */
    public function getNormalizeAttributes()
    {
        return array(
            'attributes' => array(
                'id',
                'codigo',
                'pessoa' => [
                    'id',
                    'nome',
                    'nome_fantasia'
                ],
                'updated' => ['timestamp']
            )
        );
    }

    /**
     *
     * @Route("/crm/cliente/datatablesJsList/", name="crm_cliente_datatablesJsList")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function datatablesJsList(Request $request)
    {
        $jsonResponse = $this->doDatatablesJsList($request);
        return $jsonResponse;
    }

    /**
     *
     * @Route("/crm/cliente/delete/{id}/", name="crm_cliente_delete", requirements={"id"="\d+"})
     * @param Request $request
     * @param Cliente $cliente
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function delete(Request $request, Cliente $cliente)
    {
        return $this->doDelete($request, $cliente);
    }

}