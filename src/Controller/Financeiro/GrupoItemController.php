<?php

namespace App\Controller\Financeiro;


use App\Business\Financeiro\GrupoBusiness;
use App\Controller\FormListController;
use App\Entity\Financeiro\Grupo;
use App\Entity\Financeiro\GrupoItem;
use App\EntityHandler\EntityHandler;
use App\EntityHandler\Financeiro\GrupoItemEntityHandler;
use App\Form\Financeiro\GrupoItemType;
use App\Utils\ExceptionUtils;
use App\Utils\Repository\FilterData;
use App\Utils\Repository\WhereBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * Class GrupoItemController
 * @package App\Controller\Financeiro
 * @author Carlos Eduardo Pauluk
 */
class GrupoItemController extends FormListController
{

    private $entityHandler;

    private $grupoBusiness;

    public function __construct(GrupoItemEntityHandler $entityHandler, GrupoBusiness $grupoBusiness)
    {
        $this->entityHandler = $entityHandler;
        $this->grupoBusiness = $grupoBusiness;
    }

    public function getEntityHandler(): ?EntityHandler
    {
        return $this->entityHandler;
    }

    public function getFormRoute()
    {
        return 'fin_grupoItem_form';
    }

    public function getFormView()
    {
        return 'Financeiro/grupoItemForm.html.twig';
    }

    public function getFilterDatas($params)
    {
        return array(
            new FilterData('descricao', 'LIKE', isset($params['filter']['descricao']) ? $params['filter']['descricao'] : null),
            new FilterData('pai', 'EQ', $params['filter']['pai'])
        );
    }

    public function getListView()
    {
        return 'Financeiro/grupoItemList.html.twig';
    }

    public function getListRoute()
    {
        return 'fin_grupoItem_list';
    }


    public function getTypeClass()
    {
        return GrupoItemType::class;
    }

    /**
     * Somente para edição.
     *
     * @Route("/fin/grupoItem/form/{id}", name="fin_grupoItem_form", requirements={"id"="\d+"})
     * @param Request $request
     * @param GrupoItem|null $grupoItem
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function form(Request $request, GrupoItem $grupoItem)
    {
        $parameters = ['pai' => $grupoItem->getPai()];
        return $this->doForm($request, $grupoItem, $parameters);
    }

    /**
     *
     * @Route("/fin/grupoItem/gerarNovo/{pai}", name="fin_grupoItem_gerarNovo", requirements={"pai"="\d+"})
     * @param Request $request
     * @param Grupo|null $pai
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function gerarNovo(Request $request, Grupo $pai = null)
    {
        try {
            $this->grupoBusiness->gerarNovo($pai);
        } catch (\Exception $e) {
            $msg = ExceptionUtils::treatException($e);
            $this->addFlash('error', $msg);
        }
        return $this->redirectToRoute('fin_grupoItem_list', ['pai' => $pai->getId()]);
    }

    /**
     *
     * @Route("/fin/grupoItem/list/{pai}", name="fin_grupoItem_list", requirements={"pai"="\d+"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function list(Request $request, Grupo $pai)
    {
        $this->getSecurityBusiness()->checkAccess($this->getListRoute());


        $defaultFilters = ['filter' => ['pai' => $pai]];

        $repo = $this->getDoctrine()->getRepository(GrupoItem::class);

        $rParams = $request->request->all();

        $formPesquisar = [];
        if (isset($rParams['formPesquisar'])) {
            parse_str(urldecode($rParams['formPesquisar']), $formPesquisar);
        }
        $formPesquisar = array_merge_recursive($formPesquisar, $defaultFilters);
        $filterDatas = $this->doGetFilterDatas($formPesquisar);

        $orders = WhereBuilder::buildOrderBy('dtVencto DESC');

        $dados = $repo->findByFilters($filterDatas, $orders,0,null);

        $vParams = [];
        $vParams['dados'] = $dados;
        $vParams['pai'] = $pai;
        $vParams['filter'] = $formPesquisar;
        $vParams['page_title'] = $pai->getDescricao();

        return $this->render('Financeiro/grupoItemList.html.twig', $vParams);

    }


    /**
     *
     * @Route("/fin/grupoItem/delete/{id}/", name="fin_grupoItem_delete", requirements={"id"="\d+"})
     * @param Request $request
     * @param GrupoItem $grupo
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function delete(Request $request, GrupoItem $grupo)
    {
        return $this->doDelete($request, $grupo);
    }

    /**
     *
     * @Route("/fin/grupoItem/select2json", name="fin_grupoItem_select2json", methods={"GET"}, options = { "expose" = true })
     * @param Request $request
     * @return Response
     */
    public function grupoSelect2json(Request $request)
    {
        $paiId = $request->get('pai');
        if (!$paiId) {
            return null;
        }

        $where = ['pai' => $paiId];
        if ($request->get('fechados')) {
            $where['fechado'] = true;
        }
        $itens = $this->getDoctrine()->getRepository(GrupoItem::class)->findBy($where, WhereBuilder::buildOrderBy('dtVencto DESC'));

        $rs = array();
        foreach ($itens as $item) {
            $r['id'] = $item->getId();
            $r['text'] = $item->getDescricao();
            $rs[] = $r;
        }

        $normalizer = new ObjectNormalizer();
        $encoder = new JsonEncoder();

        $serializer = new Serializer(array($normalizer), array($encoder));
        $json = $serializer->serialize($rs, 'json');

        return new Response($json);

    }

}