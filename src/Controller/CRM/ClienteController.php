<?php

namespace App\Controller\CRM;


use App\Business\CRM\ClienteBusiness;
use App\Controller\FormListController;
use App\Entity\CRM\Cliente;
use App\EntityHandler\CRM\ClienteEntityHandler;
use App\EntityHandler\EntityHandler;
use App\Form\CRM\ClienteType;
use App\Utils\Repository\FilterData;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

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
        $dados = null;
        $params = $request->query->all();

        if (!array_key_exists('filter', $params)) {
            $params['filter'] = null;
        }

        try {
            $repo = $this->getDoctrine()->getRepository($this->getEntityHandler()->getEntityClass());

            if (!$params['filter'] or count($params['filter']) == 0) {
                $dados = $repo->findAll(100);
            } else {
                $dados = $repo->findByFilters($this->getFilterDatas($params));
            }
        } catch (\Exception $e) {
            $this->addFlash('error', 'Erro ao listar (' . $e->getMessage() . ')');
        }

        return $this->render($this->getListView(), array(
            'dados' => $dados,
            'filter' => $params['filter']
        ));
    }

    /**
     *
     * @Route("/crm/cliente/delete/{id}/", name="crm_cliente_delete", requirements={"id"="\d+"})
     * @Method("POST")
     * @param Request $request
     * @param Cliente $cliente
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function delete(Request $request, Cliente $cliente)
    {
        return $this->doDelete($request, $cliente);
    }


}