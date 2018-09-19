<?php

namespace App\Controller\Crediario;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ServipaWSController.
 * @package App\Controller\Crediario
 * @author Carlos Eduardo Pauluk
 */
class ServipaWSController extends Controller
{

    /**
     *
     * @Route("/crd/servipaWS/teste", name="crd_servipaWS_teste", methods={"GET"}, options = { "expose" = true })
     * @param Request $request
     * @return Response
     */
    public function teste(Request $request)
    {
        $soapClient = new \nusoap_client('https://sistema2.ocrediario.com.br/wsrcs.php?wsdl');
        $params = array();

        $params['ParamApp'] = '';
        $params['Instituicao'] = '3';
        $params['Loja'] = '15090';
        $params['Usuario'] = 'wsbonsucesso';
        $params['Senha'] = 'ws$321%^ers';
        $params['DataAlteracao'] = '20180914';
        $params['SenhaWS'] = '';
        $params['IP'] = '';
        $params['DadosCliente'] = '';
        $params['NomeURL'] = '';

        $result = $soapClient->call('fncListaClientesAlterados', $params);

        return new Response("bla");
    }


}