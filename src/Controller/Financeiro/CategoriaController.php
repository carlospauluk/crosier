<?php

namespace App\Controller\Financeiro;

use App\Controller\FormListController;
use App\Entity\Financeiro\Categoria;
use App\Entity\Financeiro\Grupo;
use App\EntityHandler\EntityHandler;
use App\EntityHandler\Financeiro\CategoriaEntityHandler;
use App\Form\Financeiro\CategoriaType;
use App\Utils\Repository\FilterData;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * Class CategoriaController
 * @package App\Controller\Financeiro
 * @author Carlos Eduardo Pauluk
 */
class CategoriaController extends FormListController
{

    private $entityHandler;

    public function __construct(CategoriaEntityHandler $entityHandler)
    {
        $this->entityHandler = $entityHandler;
    }

    public function getEntityHandler(): ?EntityHandler
    {
        return $this->entityHandler;
    }

    public function getFormRoute()
    {
        return 'fin_categoria_form';
    }

    public function getFormView()
    {
        return 'Financeiro/categoriaForm.html.twig';
    }

    public function getFilterDatas($params)
    {
        return array(
            new FilterData('descricao', 'LIKE', $params['filter']['descricao']),
            new FilterData('dtConsolidado', 'BETWEEN', $params['filter']['dtConsolidado'])
        );
    }

    public function getListView()
    {
        return 'Financeiro/categoriaList.html.twig';
    }

    public function getListRoute()
    {
        return 'fin_categoria_list';
    }


    public function getTypeClass()
    {
        return CategoriaType::class;
    }

    /**
     *
     * @Route("/fin/categoria/form/{id}", name="fin_categoria_form", defaults={"id"=null}, requirements={"id"="\d+"})
     * @param Request $request
     * @param Categoria|null $categoria
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function form(Request $request, Categoria $categoria = null)
    {
        return $this->doForm($request, $categoria);
    }

    /**
     *
     * @Route("/fin/categoria/list/", name="fin_categoria_list")
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
                'descricao',
                'dtConsolidado' => ['timestamp'],
                'limite'
            )
        );
    }

    /**
     *
     * @Route("/fin/categoria/datatablesJsList/", name="fin_categoria_datatablesJsList")
     * @param Request $request
     * @return Response
     */
    public function datatablesJsList(Request $request)
    {
        $jsonResponse = $this->doDatatablesJsList($request);
        return $jsonResponse;
    }

    /**
     *
     * @Route("/fin/categoria/delete/{id}/", name="fin_categoria_delete", requirements={"id"="\d+"})
     * @param Request $request
     * @param Categoria $categoria
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function delete(Request $request, Categoria $categoria)
    {
        return $this->doDelete($request, $categoria);
    }

    /**
     *
     * @Route("/fin/categoria/select2json", name="fin_categoria_select2json", methods={"GET"}, options = { "expose" = true })
     * @param Request $request
     * @param Grupo $item
     * @return Response
     */
    public function categoriaSelect2json(Request $request)
    {
        $itens = $this->getDoctrine()->getRepository(Categoria::class)->findBy(['concreta' => true], ['codigo' => 'ASC']);

        $rs = array();
        foreach ($itens as $item) {
            $r['id'] = $item->getId();
            $r['text'] = $item->getDescricaoMontada();
            $rs[] = $r;
        }

        $normalizer = new ObjectNormalizer();
        $encoder = new JsonEncoder();

        $serializer = new Serializer(array($normalizer), array($encoder));
        $json = $serializer->serialize($rs, 'json');

        return new Response($json);
    }


}