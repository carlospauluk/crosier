<?php

namespace App\Controller\CRM;


use App\Business\CRM\ClienteBusiness;
use App\Controller\FormListController;
use App\Entity\Base\Pessoa;
use App\Entity\CRM\Cliente;
use App\EntityHandler\CRM\ClienteEntityHandler;
use App\EntityHandler\EntityHandler;
use App\Form\CRM\ClienteType;
use App\Utils\Repository\FilterData;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * Class ClienteController
 * @package App\Controller\Financeiro
 * @author Carlos Eduardo Pauluk
 */
class ClienteController extends FormListController
{


    private $entityHandler;

    private $clienteBusiness;

    public function __construct(ClienteEntityHandler $entityHandler, ClienteBusiness $clienteBusiness)
    {
        $this->entityHandler = $entityHandler;
        $this->clienteBusiness = $clienteBusiness;
    }

    public function getEntityHandler(): ?EntityHandler
    {
        return $this->entityHandler;
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
            new FilterData('p.nome', 'LIKE', $params['filter']['p_nome'])
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
        if (!$cliente) {
            $cliente = new Cliente();
            $cliente->setPessoa(new Pessoa());
        }

        // Se foi passado via post
        $data = $this->clienteBusiness->cliente2FormData($cliente);
        $dataPosted = $request->request->get('cliente');
        if (is_array($dataPosted)) {
            $data = array_merge($data, $dataPosted);
        }

        $form = $this->createForm($this->getTypeClass(), $data);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $cliente = $this->clienteBusiness->formData2Cliente($data);
                $this->getEntityHandler()->persist($cliente);
                $this->addFlash('success', 'Registro salvo com sucesso!');
                return $this->redirectToRoute($this->getFormRoute(), array('id' => $cliente->getId()));
            } else {
                $form->getErrors(true, false);
            }
        }

        return $this->render($this->getFormView(), array(
            'form' => $form->createView()
        ));
    }

    /**
     *
     * @Route("/crm/cliente/list/", name="crm_cliente_list")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
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
                ]
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
        $json = $this->doDatatablesJsList($request);
        return new Response($json);
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