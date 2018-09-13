<?php

namespace App\Controller\Producao;

use App\Entity\Producao\Confeccao;
use App\Entity\Producao\ConfeccaoItem;
use App\Entity\Producao\ConfeccaoItemQtde;
use App\Entity\Producao\Instituicao;
use App\Entity\Producao\TipoArtigo;
use App\Form\Producao\ConfeccaoItemType;
use App\Form\Producao\ConfeccaoType;
use App\Service\EntityIdSerializerService;
use App\Utils\Repository\FilterData;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ConfeccaoController extends Controller
{

    private $eSerializer;

    public function __construct(EntityIdSerializerService $eSerializer)
    {
        Route::class;
        $this->eSerializer = $eSerializer;
    }

    /**
     *
     * @Route("/prod/confeccao/findAllByTipoArtigoInstituicao/{instituicao}/{tipoArtigo}", name="prod_confeccao_findAllByTipoArtigoInstituicao", defaults={"instituicao"=null,"tipoArtigo"=null}, requirements={"instituicao"="\d+","tipoArtigo"="\d+"})
     */
    public function findAllByTipoArtigoInstituicao(Request $request, Instituicao $instituicao, TipoArtigo $tipoArtigo)
    {
        $repo = $this->getDoctrine()->getRepository(Confeccao::class);
        $rs = $repo->findAllByTipoArtigoInstituicao($instituicao, $tipoArtigo);

        $results = array(
            'results' => $rs
        );

        $json = $this->eSerializer->serializeIncluding($results, array(
            'id',
            'descricao'
        ));

        return new Response($json);
    }

    /**
     *
     * @Route("/prod/confeccao/form/{id}", name="prod_confeccao_form", defaults={"id"=null}, requirements={"id"="\d+"})
     *
     */
    public function form(Request $request, Confeccao $confeccao = null)
    {
        if (!$confeccao) {
            $confeccao = new Confeccao();

            $confeccao->setInserted(new \DateTime('now'));
            $confeccao->setUpdated(new \DateTime('now'));
        }

        $form = $this->createForm(ConfeccaoType::class, $confeccao);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $confeccao = $form->getData();

            // ... perform some action, such as saving the task to the database
            // for example, if Task is a Doctrine entity, save it!
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($confeccao);
            $entityManager->flush();
            $this->addFlash('success', 'Registro salvo com sucesso!');
            return $this->redirectToRoute('prod_confeccao_form', array(
                'id' => $confeccao->getId()
            ));
        } else {
            $form->getErrors(true, false);
        }

        return $this->render('Producao/confeccaoForm.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     *
     * @Route("/prod/confeccao/list/", name="prod_confeccao_list")
     */
    public function list(Request $request)
    {
        $dados = null;
        $params = $request->query->all();

        if (!array_key_exists('filter', $params)) {
            $params['filter'] = null;
        }

        try {

            $repo = $this->getDoctrine()->getRepository(Confeccao::class);

            if (!$params['filter'] or count($params['filter']) == 0) {
                $dados = $repo->findAll();
            } else {

                $filters = array(
                    new FilterData('descricao', 'LIKE', $params['filter']['descricao']),
                    new FilterData('dtConsolidado', 'BETWEEN', $params['filter']['dtConsolidado'])
                );

                $dados = $repo->findByFilters($filters);
            }
        } catch (\Exception $e) {
            $this->addFlash('error', 'Erro ao listar (' . $e->getMessage() . ')');
        }

        return $this->render('Producao/confeccaoList.html.twig', array(
            'dados' => $dados,
            'filter' => $params['filter']
        ));
    }

    /**
     *
     * @Route("/prod/confeccao/item/delete/{id}/", name="prod_confeccao_item_delete", requirements={"id"="\d+"}, methods={"POST"})
     *
     */
    public function delete(Request $request, ConfeccaoItem $item)
    {
        $confeccaoId = $item->getConfeccao()->getId();
        if (!$this->isCsrfTokenValid('delete', $request->request->get('token'))) {
            $this->addFlash('error', 'Erro interno do sistema.');
        } else {
            try {
                $em = $this->getDoctrine()->getManager();
                $em->remove($item);
                $em->flush();
                $this->addFlash('success', 'post.deleted_successfully');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Erro ao deletar.');
            }
        }

        return $this->redirectToRoute('prod_fichaTecnica_form', array(
            'id' => $confeccaoId
        ));
    }

    /**
     *
     * @Route("/prod/confeccao/item/form/{id}", name="prod_confeccao_item_form", defaults={"id"=null})
     *
     */
    public function itemForm(Request $request, ConfeccaoItem $item = null)
    {
        $itemA = array();
        if ($item) {
            $itemA['id'] = $item->getId();
            $itemA['confeccao_id'] = $item->getConfeccao()->getId();
            $itemA['unidade_produto_id'] = $item->getInsumo()->getUnidadeProduto()->getId();
            $itemA['grade_id'] = $item->getConfeccao()->getGrade()->getId();

            $r = $this->getDoctrine()->getRepository(ConfeccaoItem::class);
            $gradeMontada = $r->findGradeMontada($item);

            foreach ($gradeMontada as $key => $value) {
                $itemA['qtde_gt_' . $key] = $value;
            }
        }

        $form = $this->createForm(ConfeccaoItemType::class, $itemA);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $item = $form->getData();

            $this->persistConfeccaoItem($item);

            $this->addFlash('success', 'Registro salvo com sucesso!');
            return $this->redirectToRoute('prod_confeccao_item_form', array(
                'id' => $item['id']
            ));
        } else {
            $form->getErrors(true, false);
        }

        return $this->render('Producao/confeccaoItemForm.html.twig', array(
            'form' => $form->createView(),
            'confeccaoId' => $itemA['confeccao_id']
        ));
    }

    public function persistConfeccaoItem($itemArr)
    {
        $entityManager = $this->getDoctrine()->getManager();

        // deleta todos os prod_confeccao_item_qtde
        $r = $this->getDoctrine()->getRepository(ConfeccaoItem::class);
        $ci = $r->find($itemArr['id']);
        $r->deleteAllQtdes($ci);


        $r = $this->getDoctrine()->getRepository(ConfeccaoItem::class);
        $gradeMontada = $r->findGradeMontada($ci);

        foreach ($ci->getConfeccao()->getGrade()->getTamanhos() as $gt) {

//         foreach ($gradeMontada as $key=>$value) {
            $item = new ConfeccaoItemQtde();
            $qtde = $itemArr['qtde_gt_' . $gt->getOrdem()];
            $item->setQtde($qtde);
            $item->setConfeccaoItem($ci);
            $item->setGradeTamanho($gt);

            $item->setEstabelecimento(1);
            $item->setInserted(new \DateTime('now'));
            $item->setUpdated(new \DateTime('now'));
            $item->setUserInserted(1);
            $item->setUserUpdated(1);

            $entityManager->persist($item);
        }

        $entityManager->flush();
    }

}