<?php

namespace App\Controller;


use App\Entity\Base\EntityId;
use App\EntityHandler\EntityHandler;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

abstract class FormListController extends Controller
{

    abstract public function getEntityHandler(): ?EntityHandler;

    /**
     * Necessário para poder passar para o createForm.
     *
     * @return mixed
     */
    abstract public function getTypeClass();

    abstract public function getFormRoute();

    abstract public function getFormView();


    /**
     * Monta o formulário, faz as validações, manda salvar, trata erros, etc.
     *
     * @param Request $request
     * @param EntityId|null $entityId
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function doForm(Request $request, EntityId $entityId = null)
    {
        if (!$entityId) {
            $entityName = $this->getEntityHandler()->getEntityClass();
            $entityId = new $entityName();
        }

        $form = $this->createForm($this->getTypeClass(), $entityId);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $entity = $form->getData();
                $this->getEntityHandler()->persist($entity);
                $this->addFlash('success', 'Registro salvo com sucesso!');
                return $this->redirectToRoute($this->getFormRoute(), array('id' => $entityId->getId()));
            } else {
                $form->getErrors(true, false);
            }
        }

        return $this->render($this->getFormView(), array(
            'form' => $form->createView()
        ));
    }

    abstract public function getFilterDatas($params);

    abstract public function getListView();

    abstract public function getListRoute();

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function doList(Request $request)
    {
        $params = $request->query->all();
        if (!array_key_exists('filter', $params)) {
            // inicializa para evitar o erro
            $params['filter'] = null;
        }
        return $this->render($this->getListView(), array(
            'filter' => $params['filter']
        ));
    }

    /**
     * Necessário informar quais atributos da entidades deverão ser retornados no Json.
     *
     * @return mixed
     */
    public function getNormalizeAttributes() {
        return null;
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function doDatatablesJsList(Request $request)
    {
        $repo = $this->getDoctrine()->getRepository($this->getEntityHandler()->getEntityClass());

        $rParams = $request->request->all();

        $start = $rParams['start'];
        $limit = $rParams['length'];

        parse_str(urldecode($rParams['formPesquisar']), $formPesquisar);

        $orders = array();
        foreach ($rParams['order'] as $pOrder) {
            $order['column'] =  $rParams['columns'][$pOrder['column']]['name'];
            $order['dir'] = $pOrder['dir'];
            $orders[] = $order;
        }

        $params = $this->getFilterDatas($formPesquisar);

        $countByFilter = $repo->countByFilters($params);
        $dados = $repo->findByFilters($params, $orders, $start, $limit);

        $normalizer = new ObjectNormalizer();
        $encoder = new JsonEncoder();

        $serializer = new Serializer(array($normalizer), array($encoder));

        $data = $serializer->normalize($dados, 'json', $this->getNormalizeAttributes());

        $draw = (int)$rParams['draw'];
        $recordsTotal = $repo->count(array());

        $results = array(
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $countByFilter,
            'data' => $data
        );

        $json = $serializer->serialize($results, 'json');

        return new Response($json);
    }

    public function doDelete(Request $request, EntityId $entityId)
    {
        if (!$this->isCsrfTokenValid('delete', $request->request->get('token'))) {
            $this->addFlash('error', 'Erro interno do sistema.');
        } else {
            try {
                $this->entityHandler->delete($entityId);
                $this->addFlash('success', 'Registro deletado com sucesso.');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Erro ao deletar registro.');
            }
        }

        return $this->redirectToRoute($this->getListRoute());
    }


}