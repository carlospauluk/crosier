<?php
namespace App\Controller\Base;

use App\Entity\Base\DiaUtil;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DiaUtilController extends Controller
{

    public function __construct()
    {
        Route::class;
    }
    
    /**
     *
     * @Route("/base/diautil/findProximoDiaUtilFinanceiro/", name="findProximoDiaUtilFinanceiro")
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



//public function incPeriodo($proFuturo, $ini, $fim) {
//
//
//    $dtIni = \DateTime::createFromFormat('Y-m-d',$ini);
//    $dtFim = \DateTime::createFromFormat('Y-m-d',$fim);
//
//    $dif = $dtFim->diff($dtIni)->days;
//
//
//
//    // Se na tela foi informado um período relatorial...
//if (CalendarUtil.isPeriodoRelatorial(dtIni, dtFim)) {
//Date r[] = CalendarUtil.iteratePeriodoRelatorial(dtIni, dtFim, proFuturo);
//getFiltros().put("dtIni", r[0]);
//getFiltros().put("dtFim", r[1]);
//
//} else {
//
//    if (qtdeDias == 0) {
//        if (proFuturo) {
//            dtIni = getMovimentacaoBusiness().getDiaUtilFinder()
//                .findProximoDiaUtilComercial(CalendarUtil.incDias(dtIni, 1));
//        } else {
//            dtIni = getMovimentacaoBusiness().getDiaUtilFinder().findAnteriorDiaUtilComercial(dtIni);
//        }
//        dtFim = dtIni;
//    } else {
//        if (!proFuturo) {
//            dtIni = CalendarUtil.incDias(dtIni, -qtdeDias.intValue());
//            dtFim = CalendarUtil.incDias(dtFim, -qtdeDias.intValue());
//        } else {
//            dtIni = CalendarUtil.incDias(dtIni, qtdeDias.intValue());
//            dtFim = CalendarUtil.incDias(dtFim, qtdeDias.intValue());
//        }
//
//    }
//    getFiltros().put("dtIni", dtIni);
//    getFiltros().put("dtFim", dtFim);
//
//}
//
//pesquisar();
//} catch (ViewException e) {
//    logger.error(e);
//    JSFUtils.addHandledException(e);
//}
//	}
    
}