<?php

namespace App\Controller\Financeiro;


use App\Controller\FormListController;
use App\Entity\Financeiro\Grupo;
use App\Entity\Financeiro\GrupoItem;
use App\EntityHandler\EntityHandler;
use App\EntityHandler\Financeiro\GrupoEntityHandler;
use App\Form\Financeiro\GrupoType;
use App\Utils\Repository\FilterData;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * Class GrupoController
 * @package App\Controller\Financeiro
 * @author Carlos Eduardo Pauluk
 */
class GrupoController extends FormListController
{


    private $entityHandler;

    public function __construct(GrupoEntityHandler $entityHandler)
    {
        $this->entityHandler = $entityHandler;
    }

    public function getEntityHandler(): ?EntityHandler
    {
        return $this->entityHandler;
    }

    public function getFormRoute()
    {
        return 'fin_grupo_form';
    }

    public function getFormView()
    {
        return 'Financeiro/grupoForm.html.twig';
    }

    public function getFilterDatas($params)
    {
        return array(
            new FilterData('descricao', 'LIKE', $params['filter']['descricao'])
        );
    }

    public function getListView()
    {
        return 'Financeiro/grupoList.html.twig';
    }

    public function getListRoute()
    {
        return 'fin_grupo_list';
    }


    public function getTypeClass()
    {
        return GrupoType::class;
    }

    /**
     *
     * @Route("/fin/grupo/form/{id}", name="fin_grupo_form", defaults={"id"=null}, requirements={"id"="\d+"})
     * @param Request $request
     * @param Grupo|null $grupo
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function form(Request $request, Grupo $grupo = null)
    {
        return $this->doForm($request, $grupo);
    }

    /**
     *
     * @Route("/fin/grupo/list/", name="fin_grupo_list")
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
                'descricao',
                'ativo',
                'carteiraPagantePadrao' => ['descricao']
            )
        );
    }

    /**
     *
     * @Route("/fin/grupo/datatablesJsList/", name="fin_grupo_datatablesJsList")
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
     * @Route("/fin/grupo/delete/{id}/", name="fin_grupo_delete", requirements={"id"="\d+"})
     * @param Request $request
     * @param Grupo $grupo
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function delete(Request $request, Grupo $grupo)
    {
        return $this->doDelete($request, $grupo);
    }

    /**
     *
     * @Route("/fin/grupo/select2json", name="fin_grupo_select2json", methods={"GET"}, options = { "expose" = true })
     * @param Request $request
     * @return Response
     */
    public function grupoSelect2json(Request $request)
    {
        $grupos = $this->getDoctrine()->getRepository(Grupo::class)->findAll();

        $rs = array();
        foreach ($grupos as $grupo) {
            $r['id'] = $grupo->getId();
            $r['text'] = $grupo->getDescricao();
            $rs[] = $r;
        }

        $normalizer = new ObjectNormalizer();
        $encoder = new JsonEncoder();

        $serializer = new Serializer(array($normalizer), array($encoder));
        $json = $serializer->serialize($rs, 'json');

        return new Response($json);

    }

}