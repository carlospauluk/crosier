<?php

namespace App\Controller\Financeiro;

use App\Controller\FormListController;
use App\Entity\Financeiro\Carteira;
use App\Entity\Financeiro\Categoria;
use App\Entity\Financeiro\Modo;
use App\Entity\Financeiro\Movimentacao;
use App\Entity\Financeiro\Status;
use App\EntityHandler\EntityHandler;
use App\EntityHandler\Financeiro\MovimentacaoEntityHandler;
use App\Form\Financeiro\MovimentacaoType;
use App\Utils\Repository\FilterData;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class MovimentacaoController
 * @package App\Controller\Financeiro
 * @author Carlos Eduardo Pauluk
 */
class MovimentacaoExtratoController extends MovimentacaoController
{

    /**
     *
     * @Route("/fin/movimentacao/extrato", name="fin_movimentacao_extrato")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function extrato(Request $request)
    {
        $params = $request->query->all();
        if (!array_key_exists('filter', $params)) {
            // inicializa para evitar o erro
            $params['filter'] = null;
        }
        // Pode ou não ter vindo algo no $parameters. Independentemente disto, só adiciono o 'filter' aqui e foi-se.
        $parameters['filter'] = $params['filter'];

        $params = $this->getFilterDatas($params);

        $repo = $this->getDoctrine()->getRepository($this->getEntityHandler()->getEntityClass());
        $orders[] = ['column' => 'e.dtUtil', 'dir' => 'asc'];
        $orders[] = ['column' => 'categ.codigo', 'dir' => 'asc'];
        $dados = $repo->findByFilters($params, $orders, 0, null);

        $dia = null;
        $dias = array();
        $i = -1;
        foreach ($dados as $movimentacao) {
            if ($movimentacao->getDtUtil()->format('d/m/Y') != $dia) {
                $i++;
                $dia = $movimentacao->getDtUtil()->format('d/m/Y');
                $dias[$i]['dtUtil'] = $movimentacao->getDtUtil();
            }
            $dias[$i]['movs'][] = $movimentacao;
        }


        $parameters['dias'] = $dias;
        $parameters['carteira']['options'] = $this->getFilterCarteiraOptions($params);
        return $this->render('Financeiro/movimentacaoExtratoList.html.twig', $parameters);
    }

    public function getFilterCarteiraOptions($params)
    {
        $repoCarteira = $this->getDoctrine()->getRepository(Carteira::class);
        $carteiras = $repoCarteira->findAll('e.codigo');

        $param = null;
        foreach ($params as $p) {
            if ($p->field == 'carteira') {
                $param = $p;
                break;
            }
        }

        $str = "";
        $selected = "";
        foreach ($carteiras as $carteira) {
            if ($param->val) {
                $selected = $carteira->getId() == $param->val ? 'selected="selected"' : '';
            }
            $str .= "<option value=\"" . $carteira->getId() . "\"" . $selected . ">" . $carteira->getCodigo() . " - " . $carteira->getDescricao() . "</option>";
        }
        return $str;
    }


}