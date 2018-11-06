<?php

namespace App\Controller\OC;

use App\EntityOC\OcManufacturer;
use App\Utils\Repository\WhereBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class OcManufacturerController
 * @package App\Controller\Estoque
 */
class OcManufacturerController extends Controller
{

    /**
     *
     * @Route("/oc/manufacturer/select2json", name="oc_manufacturer_select2json")
     * @param Request $request
     * @return JsonResponse
     */
    public function ocManufacturerSelect2json(Request $request)
    {
        $itens = $this->getDoctrine()->getManager('oc')->getRepository(OcManufacturer::class)->findAll(WhereBuilder::buildOrderBy('name ASC'));

        $rs = array();
        foreach ($itens as $item) {
            $r['id'] = $item->getManufacturerId();
            $r['text'] = $item->getName();
            $rs[] = $r;
        }

        return new JsonResponse($rs);
    }


}