<?php

namespace App\Controller\Financeiro;

use App\Business\Base\DiaUtilBusiness;
use App\Entity\Financeiro\Movimentacao;
use App\EntityHandler\Financeiro\MovimentacaoEntityHandler;
use App\Utils\ExceptionUtils;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class MovimentacaoRecorrentesController
 *
 * Listagem e geração de movimentações recorrentes.
 *
 * @package App\Controller\Financeiro
 * @author Carlos Eduardo Pauluk
 */
class MovimentacaoRecorrentesController extends MovimentacaoBaseController
{

    private $diaUtilBusiness;

    private $entityHandler;

    public function __construct(DiaUtilBusiness $diaUtilBusiness, MovimentacaoEntityHandler $entityHandler)
    {
        $this->entityHandler = $entityHandler;
        $this->diaUtilBusiness = $diaUtilBusiness;
    }

    /**
     *
     * @Route("/fin/movimentacaoRecorrente/list", name="fin_movimentacaoRecorrente_list")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function list(Request $request)
    {
        $parameters = $request->query->all();
        if (!array_key_exists('filter', $parameters)) {
            // inicializa para evitar o erro
            $dt = new \DateTime();
            $parameters['filter'] = array();
            $parameters['filter']['dtUtil']['i'] = $dt->format('Y-m-') . '01';
            $parameters['filter']['dtUtil']['f'] = $dt->format('Y-m-t');
        }

        $parameters['filter']['recorrente'] = true;

        $dtIni = $parameters['filter']['dtUtil']['i'];
        $dtFim = $parameters['filter']['dtUtil']['f'];

        $prox = $this->diaUtilBusiness->incPeriodo(true, $dtIni, $dtFim);
        $ante = $this->diaUtilBusiness->incPeriodo(false, $dtIni, $dtFim);
        $parameters['antePeriodoI'] = $ante['dtIni'];
        $parameters['antePeriodoF'] = $ante['dtFim'];
        $parameters['proxPeriodoI'] = $prox['dtIni'];
        $parameters['proxPeriodoF'] = $prox['dtFim'];

        return $this->doList($request, $parameters);
    }


    /**
     *
     * @Route("/fin/movimentacaoRecorrente/datatablesJsList/", name="fin_movimentacaoRecorrente_datatablesJsList")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function datatablesJsList(Request $request)
    {
        $defaultFilters['filter']['recorrente'] = true;
        $jsonResponse = $this->doDatatablesJsList($request);
        return $jsonResponse;
    }

    /**
     *
     * @Route("/fin/movimentacaoRecorrente/processar", name="fin_movimentacaoRecorrente_processar")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function processar(Request $request)
    {
        try {
            $movs = $request->get('movs');
            $rMovs = [];
            if (!$movs) {
                $this->addFlash('warn', 'Nenhuma movimentação selecionada');
            } else {
                foreach ($movs as $movId => $on) {
                    $rMovs[] = $this->getDoctrine()->getRepository(Movimentacao::class)->find($movId);
                }
                $msgs = $this->getBusiness()->processarRecorrentes($rMovs);
                $this->addFlash('success', $msgs);
            }
        } catch (\Exception $e) {
            $msg = ExceptionUtils::treatException($e);
            $this->addFlash('error', $msg);
            $this->addFlash('error', 'Erro ao processar recorrentes');
        }
        return $this->redirectToRoute('fin_movimentacaoRecorrente_list', ['filter' => $request->get('filter')]);
    }

    public function getListView()
    {
        return 'Financeiro/movimentacaoRecorrentesList.html.twig';
    }

    public function getListRoute()
    {
        return 'fin_movimentacaoRecorrente_list';
    }

    public function getListPageTitle()
    {
        return "Recorrentes";
    }


}