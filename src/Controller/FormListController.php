<?php

namespace App\Controller;


use App\Entity\Base\EntityId;
use App\EntityHandler\EntityHandler;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

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

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function doList(Request $request)
    {
        $dados = null;
        $params = $request->query->all();

        if (!array_key_exists('filter', $params)) {
            $params['filter'] = null;
        }

        try {
            $repo = $this->getDoctrine()->getRepository($this->getEntityHandler()->getEntityClass());

            if (!$params['filter'] or count($params['filter']) == 0) {
                $dados = $repo->findBy([], null, 10000);
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

    abstract public function getListRoute();

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