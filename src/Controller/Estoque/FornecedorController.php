<?php

namespace App\Controller\Estoque;

use App\Business\Base\PessoaBusiness;
use App\Entity\Base\Pessoa;
use App\Entity\Estoque\Fornecedor;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class FornecedorController extends Controller
{

    /**
     *
     * @Route("/est/fornecedor/findByDocumento/{documento}", name="est_fornecedor_findByDocumento", methods={"GET"})
     * @param null $documento
     * @return Response|void
     * @throws \Exception
     */
    public function findByDocumento($documento = null)
    {
        if ($documento == null) {
            return;
        }
        $repo = $this->getDoctrine()->getRepository(Fornecedor::class);
        $fornecedor = $repo->findByDocumento(preg_replace('/\D/', '', $documento));

        $normalizer = new ObjectNormalizer();
        $encoder = new JsonEncoder();

        $serializer = new Serializer(array($normalizer), array($encoder));
        $json = $serializer->serialize($fornecedor, 'json');

        return new Response($json);
    }


}