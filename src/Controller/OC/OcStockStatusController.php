<?php

namespace App\Controller\OC;

use App\EntityOC\OcStockStatus;
use App\Utils\Repository\WhereBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class OcStockStatusController
 */
class OcStockStatusController extends Controller
{

    private $doctrine;

    /**
     *
     * @Route("/oc/stockStatus/select2json", name="oc_stockStatus_select2json")
     * @param Request $request
     * @return JsonResponse
     */
    public function ocStockStatusSelect2json(Request $request)
    {

        $itens = $this->getDoctrine()->getManager('oc')->getRepository(OcStockStatus::class)->findAll(WhereBuilder::buildOrderBy('name ASC'));

        $rs = array();
        foreach ($itens as $item) {
            $r['id'] = $item->getCategoryId();
            $r['text'] = $this->getDescricao($item, "");
            $rs[] = $r;
        }


        return new JsonResponse($rs);
    }


}