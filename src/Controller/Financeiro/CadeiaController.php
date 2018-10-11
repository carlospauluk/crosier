<?php

namespace App\Controller\Financeiro;

use App\EntityHandler\Financeiro\CadeiaEntityHandler;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CadeiaController
 * @package App\Controller\Financeiro
 * @author Carlos Eduardo Pauluk
 */
class CadeiaController extends Controller
{

    private $entityHandler;

    /**
     * CadeiaController constructor.
     */
    public function __construct(CadeiaEntityHandler $entityHandler)
    {
        $this->entityHandler = $entityHandler;
    }


    /**
     *
     * @Route("/fin/cadeia/corrigirUnqcs", name="fin_cadeia_corrigirUnqcs")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function corrigirUnqcs(Request $request)
    {
        $this->entityHandler->corrigirUnqcs();
        return new Response('Corrigido');
    }


}