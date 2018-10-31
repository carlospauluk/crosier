<?php

namespace App\Controller\Financeiro;

use App\Controller\FormListController;
use App\Entity\Financeiro\Carteira;
use App\Entity\Financeiro\Grupo;
use App\EntityHandler\EntityHandler;
use App\EntityHandler\Financeiro\CarteiraEntityHandler;
use App\Form\Financeiro\CarteiraType;
use App\Utils\Repository\FilterData;
use App\Utils\Repository\WhereBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * Class CarteiraController
 * @package App\Controller\Financeiro
 * @author Carlos Eduardo Pauluk
 */
class CarteiraController extends FormListController
{

    private $entityHandler;

    public function __construct(CarteiraEntityHandler $entityHandler)
    {
        $this->entityHandler = $entityHandler;
    }

    public function getEntityHandler(): ?EntityHandler
    {
        return $this->entityHandler;
    }

    public function getFormRoute()
    {
        return 'fin_carteira_form';
    }

    public function getFormView()
    {
        return 'Financeiro/carteiraForm.html.twig';
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
        return 'Financeiro/carteiraList.html.twig';
    }

    public function getListRoute()
    {
        return 'fin_carteira_list';
    }


    public function getTypeClass()
    {
        return CarteiraType::class;
    }

    /**
     *
     * @Route("/fin/carteira/form/{id}", name="fin_carteira_form", defaults={"id"=null}, requirements={"id"="\d+"})
     * @param Request $request
     * @param Carteira|null $carteira
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \ReflectionException
     * @throws \Exception
     */
    public function form(Request $request, Carteira $carteira = null)
    {
        return $this->doForm($request, $carteira);
    }

    /**
     *
     * @Route("/fin/carteira/list/", name="fin_carteira_list")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \ReflectionException
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
                'descricao',
                'dtConsolidado' => ['timestamp'],
                'limite'
            )
        );
    }

    /**
     *
     * @Route("/fin/carteira/datatablesJsList/", name="fin_carteira_datatablesJsList")
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
     * @Route("/fin/carteira/delete/{id}/", name="fin_carteira_delete", requirements={"id"="\d+"})
     * @param Request $request
     * @param Carteira $carteira
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function delete(Request $request, Carteira $carteira)
    {
        return $this->doDelete($request, $carteira);
    }

    /**
     *
     * @Route("/fin/carteira/select2json", name="fin_carteira_select2json")
     * @param Request $request
     * @param Grupo $item
     * @return Response
     */
    public function carteiraSelect2json(Request $request)
    {

        $params = [];
        $params['concreta'] = true;

        if ($request->get('cheque')) {
            $params['cheque'] = true;
        }
        if ($request->get('caixa')) {
            $params['caixa'] = true;
        }

        $itens = $this->getDoctrine()->getRepository(Carteira::class)->findBy($params,['codigo' => 'asc']);

        $rs = array();
        foreach ($itens as $item) {
            $r['id'] = $item->getId();
            $r['text'] = $item->getDescricaoMontada();
            // Adiciono estes campos para no casos de movimentacaoForm, onde os campos do cheque devem ser preenchidos no onChange da carteira
            $r['bancoId'] = $item->getBanco() ? $item->getBanco()->getId() : null;
            $r['agencia'] = $item->getAgencia();
            $r['conta'] = $item->getConta();
            $r['cheque'] = $item->getCheque();
            $rs[] = $r;
        }

        $normalizer = new ObjectNormalizer();
        $encoder = new JsonEncoder();

        $serializer = new Serializer(array($normalizer), array($encoder));
        $json = $serializer->serialize($rs, 'json');

        return new Response($json);
    }


}