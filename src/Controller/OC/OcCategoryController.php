<?php

namespace App\Controller\OC;

use App\EntityOC\OcCategory;
use App\EntityOC\OcCategoryDescription;
use App\Utils\Repository\WhereBuilder;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class OcCategoryController
 * @package App\Controller\Estoque
 */
class OcCategoryController extends Controller
{

    private $doctrine;

    /**
     *
     * @Route("/oc/category/select2json", name="oc_category_select2json")
     * @param Request $request
     * @return JsonResponse
     */
    public function ocCategorySelect2json(Request $request)
    {
//        $ql = 'SELECT c FROM App\EntityOC\OcCategory c WHERE c.parentId != 0 AND NOT EXISTS (SELECT f FROM App\EntityOC\OcCategory f WHERE f.parentId = c.categoryId)';
//        $qry = $this->getDoctrine()->getManager('oc')->createQuery($ql);
//        $itens = $qry->getResult();

        $itens = $this->getDoctrine()->getManager('oc')->getRepository(OcCategory::class)->findBy([]);

        $rs = array();
        foreach ($itens as $item) {
            $r['id'] = $item->getCategoryId(    );
            $r['text'] = $this->getDescricao($item, "");
            $rs[] = $r;
        }

        usort($rs, function($a, $b) { return($a['text'] >= $b['text']); });


        return new JsonResponse($rs);
    }


    private function getDescricao(OcCategory $category, $name = "")
    {
        $ocCategoryDescription = $this->getDoctrine()->getManager('oc')->getRepository(OcCategoryDescription::class)->findOneBy(['categoryId' => $category->getCategoryId()]);
        $name = $name ? $ocCategoryDescription->getName() . ' > ' . $name : $ocCategoryDescription->getName();
        if (!$category->getParentId()) {
            return $name;
        }
        $ocCategoryPai = $this->getDoctrine()->getManager('oc')->getRepository(OcCategory::class)->findOneBy(['categoryId' => $category->getParentId()]);
        return $this->getDescricao($ocCategoryPai, $name);
    }
//
//    /**
//     * @return mixed
//     */
//    public function getDoctrine(): RegistryInterface
//    {
//        return $this->doctrine;
//    }
//
//    /**
//     * @required
//     * @param mixed $doctrine
//     */
//    public function setDoctrine(RegistryInterface $doctrine): void
//    {
//        $this->doctrine = $doctrine;
//    }
//

}