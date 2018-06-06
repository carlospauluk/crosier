<?php
namespace App\Controller\Base;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\Financeiro\CarteiraType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use App\Utils\Repository\FilterData;
use App\Entity\Base\Pessoa;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Serializer;
use App\Entity\Base\DiaUtil;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;

class DiaUtilController extends Controller
{

    /**
     *
     * @Route("/base/diautil/findProximoDiaUtilFinanceiro/", name="findProximoDiaUtilFinanceiro")
     * @Method("GET")
     *
     */
    public function findProximoDiaUtilFinanceiro(Request $request) {
        
        $params = $request->query->all();
        
        
        
        if (! array_key_exists('dia', $params)) {
            return null;
        } else {
            $dia = $params['dia'];
        }
        
        $dateTimeDia = \DateTime::createFromFormat('d/m/Y', $dia);
        $dateTimeDia->setTime(0,0,0,0);
        
        $repo = $this->getDoctrine()->getRepository(DiaUtil::class);
        $diaUtil = $repo->findProximoDiaUtilFinanceiro($dateTimeDia);
        
        $response = "";
        if ($diaUtil) {
            $response = $diaUtil->getDia()->format('d/m/Y');
        } 
        
        return new Response($response);
//         array_walk($results, function(&$value, &$key) {
//             if ($key == 'dia') {
//                 $value = str_replace('dog', '', $value);
//             }
//         });
        
//         $encoder = new JsonEncoder();
        
//         $serializer = new Serializer(array(new DateTimeNormalizer(), new ObjectNormalizer()), array($encoder));
//         $json = $serializer->serialize($results, 'json');
        
    }
    
}