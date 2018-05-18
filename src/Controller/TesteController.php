<?php
namespace App\Controller;

use App\Entity\Financeiro\Carteira;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\Financeiro\CarteiraType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use App\Utils\Repository\FilterData;
use App\Utils\StringUtils;
use App\Entity\Financeiro\Categoria;

class TesteController extends Controller
{

    /**
     *
     * @Route("/teste", name="teste")
     */
    public function form(Request $request)
    {
        $response = new Response('Content', Response::HTTP_OK, array(
            'content-type' => 'text/html'
        ));
        
        $mask = StringUtils::mascarar(123333333444455555, Categoria::MASK);
        $response->setContent($mask);
        
        // the headers public attribute is a ResponseHeaderBag
        $response->headers->set('Content-Type', 'text/plain');
        
        $response->setStatusCode(Response::HTTP_NOT_FOUND);
        
        return $response;
    }
}