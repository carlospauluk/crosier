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

    /**
     * Export to PDF
     *
     * @Route("/pdf", name="acme_demo_pdf")
     */
    public function pdfAction()
    {
        $repo = $this->getDoctrine()->getRepository(Carteira::class);
            $dados = $repo->findAll();
        $html = $this->renderView('Financeiro/carteiraList.html.twig', array('dados'=> $dados));
        
        $filename = sprintf('test-%s.pdf', date('Y-m-d'));
        
        return new Response($this->get('knp_snappy.pdf')->getOutputFromHtml($html), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => sprintf('attachment; filename="%s"', $filename)
        ]);
    }
    
    /**
     * Export to PDF
     *
     * @Route("/toastr", name="toastr")
     */
    public function toastr() {
        $this->addFlash('error', 'Testando mensagem de erro');
        
        return $this->render('toastr.html.twig');
        
    }
    
    
    
    
    
}