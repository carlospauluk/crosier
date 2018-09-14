<?php

namespace App\Controller;


use App\Business\Config\StoredViewInfoBusiness;
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

    private $storedViewInfoBusiness;

    /**
     * @required
     * @param StoredViewInfoBusiness $storedViewInfoBusiness
     */
    public function setStoredViewInfoBusiness(StoredViewInfoBusiness $storedViewInfoBusiness)
    {
        $this->storedViewInfoBusiness = $storedViewInfoBusiness;
    }

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

        // Pode ou não ter vindo algo no $parameters. Independentemente disto, só adiciono form e foi-se.
        $parameters['form'] = $form->createView();

        return $this->render($this->getFormView(), $parameters);
    }

    abstract public function getFilterDatas($params);

    public function doGetFilterDatas($params)
    {
        $filterDatas = $this->getFilterDatas($params);
        $clearedFilterDatas = array();
        if ($filterDatas and count($filterDatas) > 0) {
            foreach ($filterDatas as $filterData) {
                $notEmpty = false;
                if (is_array($filterData->val)) {
                    foreach ($filterData->val as $val) {
                        if ($val) {
                            $notEmpty = true;
                            break;
                        }
                    }
                } else if ($filterData->val) {
                    $notEmpty = true;
                }
                if ($notEmpty) {
                    $clearedFilterDatas[] = $filterData;
                }
            }
        }
        return $clearedFilterDatas;
    }

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

            if (isset($params['r']) and $params['r']) {
                $this->storedViewInfoBusiness->clear($this->getListRoute());
            } else {
                $storedViewInfo = $this->storedViewInfoBusiness->retrieve($this->getListRoute());
                if ($storedViewInfo) {
                    $blob = stream_get_contents($storedViewInfo->getViewInfo());
                    $unserialized = unserialize($blob);
                    $formPesquisar = isset($unserialized['formPesquisar']) ? $unserialized['formPesquisar'] : null;
                    if ($formPesquisar and $formPesquisar != $params) {
                        return $this->redirectToRoute($this->getListRoute(), $formPesquisar);
                    }
                }
            }
        }


        return $this->render($this->getListView(), $params);
    }

    /**
     * Necessário informar quais atributos da entidades deverão ser retornados no Json.
     *
     * @return mixed
     */
    public function getNormalizeAttributes()
    {
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

        // Inicializadores
        $filterDatas = null;
        $start = 0;
        $limit = 10;
        $orders = null;
        $draw = 1;

        if ($rParams) {
            $start = $rParams['start'];
            $limit = $rParams['length'];
            $orders = array();
            foreach ($rParams['order'] as $pOrder) {
                $order['column'] = $rParams['columns'][$pOrder['column']]['name'];
                $order['dir'] = $pOrder['dir'];
                $orders[] = $order;
            }
            $draw = (int)$rParams['draw'];
            parse_str(urldecode($rParams['formPesquisar']), $formPesquisar);
            $filterDatas = $this->doGetFilterDatas($formPesquisar);
        }

        $countByFilter = $repo->doCountByFilters($filterDatas);
        $dados = $repo->findByFilters($filterDatas, $orders, $start, $limit);

        $normalizer = new ObjectNormalizer();
        $encoder = new JsonEncoder();
        $serializer = new Serializer(array($normalizer), array($encoder));
        $data = $serializer->normalize($dados, 'json', $this->getNormalizeAttributes());


        $recordsTotal = $repo->count(array());

        $results = array(
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $countByFilter,
            'data' => $data
        );

        $json = $serializer->serialize($results, 'json');

        if ($filterDatas and count($filterDatas) > 0) {
            $viewInfo = array();
            $viewInfo['formPesquisar'] = $formPesquisar;
            $this->storedViewInfoBusiness->store($this->getListRoute(), $viewInfo);
        }

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