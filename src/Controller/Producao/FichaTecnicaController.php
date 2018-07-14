<?php
namespace App\Controller\Producao;

use App\Entity\Financeiro\Movimentacao;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class FichaTecnicaController extends Controller
{

    public function __construct()
    {
        Route::class;
        Method::class;
    }

    /**
     *
     * @Route("/prod/fichatecnica/form/{id}", name="fichatecnica_form", defaults={"id"=null}, requirements={"id"="\d+"})
     * 
     */
    public function form(Request $request, Movimentacao $movimentacao = null)
    {
        
        $queryParams = $request->query->all();
        
        $params = array();
        
        return $this->render('Producao/fichaTecnicaForm.html.twig', $params);
    }
}