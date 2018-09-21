<?php

namespace App\Controller\Financeiro;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class RelatoriosController
 * @package App\Controller\Financeiro
 * @author Carlos Eduardo Pauluk
 */
class RelatoriosController extends Controller
{

    /**
     *
     * @Route("/fin/relatorios/list", name="fin_relatorios_list")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function list(Request $request)
    {
        return new Response();
    }


}