<?php

namespace App\Controller\Base;

use App\Entity\Base\DiaUtil;
use App\Utils\DateTimeUtils;
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
    public function findProximoDiaUtilFinanceiro(Request $request)
    {

        $params = $request->query->all();


        if (!array_key_exists('dia', $params)) {
            return null;
        } else {
            $dia = $params['dia'];
        }

        $dateTimeDia = \DateTime::createFromFormat('d/m/Y', $dia);
        $dateTimeDia->setTime(0, 0, 0, 0);

        $repo = $this->getDoctrine()->getRepository(DiaUtil::class);
        $diaUtil = $repo->findProximoDiaUtilFinanceiro($dateTimeDia);

        $response = "";
        if ($diaUtil) {
            $response = $diaUtil->getDia()->format('d/m/Y');
        }

        return new Response($response);
    }

    /**
     *
     * @Route("/bse/diaUtil/incPeriodo/{proFuturo}/{ini}/{fim}", name="bse_diaUtil_incPeriodo")
     *
     */
    public function incPeriodo($proFuturo, $ini, $fim)
    {
        try {
            $dtIni = \DateTime::createFromFormat('Y-m-d', $ini);
            $dtFim = \DateTime::createFromFormat('Y-m-d', $fim);
            $difDias = $dtFim->diff($dtIni)->days;// Se na tela foi informado um período relatorial...
            if (DateTimeUtils::isPeriodoRelatorial($dtIni, $dtFim)) {


                $periodoJson = DateTimeUtils::iteratePeriodoRelatorial($dtIni, $dtFim, $proFuturo);

            } else {

                if ($difDias == 0) {
                    if ($proFuturo) {
                        $dtIni = $this->getDoctrine()->getRepository(DiaUtil::class)->findProximoDiaUtilComercial($dtIni->add(new \DateInterval('P1D')));
                    } else {
                        $dtIni = $this->getDoctrine()->getRepository(DiaUtil::class)->findAnteriorDiaUtilComercial($dtIni->add(new \DateInterval('P1D')));
                    }
                    $dtFim = clone $dtIni;
                } else {
                    if (!$proFuturo) {
                        $dtIni = $dtIni->sub(new \DateInterval('P' . $difDias . 'D'))->format('Y-m-d');
                        $dtFim = $dtFim->sub(new \DateInterval('P' . $difDias . 'D'))->format('Y-m-d');
                    } else {
                        $dtIni = $dtIni->add(new \DateInterval('P' . $difDias . 'D'))->format('Y-m-d');
                        $dtFim = $dtFim->add(new \DateInterval('P' . $difDias . 'D'))->format('Y-m-d');
                    }

                }
            }
            $periodo['dtIni'] = $dtIni;
            $periodo['dtFim'] = $dtFim;
            $json = json_encode($periodo);
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent($json);
            return new Response($json);
        } catch (\Exception $e) {
            throw new \Exception("Erro ao gerar o período.");
        }

    }
}
