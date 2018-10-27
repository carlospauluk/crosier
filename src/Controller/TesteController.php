<?php

namespace App\Controller;

use App\Entity\Financeiro\Carteira;
use App\Entity\Financeiro\Categoria;
use App\Utils\StringUtils;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TesteController extends Controller
{

    /**
     *
     * @Route("/teste", name="teste")
     */
    public function form(Request $request)
    {
        return $this->render('teste.html.twig');
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
        $html = $this->renderView('Financeiro/carteiraList.html.twig', array('dados' => $dados));

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
    public function toastr()
    {
        $this->addFlash('error', 'Testando mensagem de erro');

        return $this->render('toastr.html.twig');

    }

    /**
     * Export to PDF
     *
     * @Route("/index", name="index")
     */
    public function index()
    {
        return $this->render('index.html.twig');
    }


}