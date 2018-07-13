<?php
namespace App\Controller\Producao;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use App\Entity\Producao\Instituicao;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Response;

class InstituicaoController extends Controller
{

    public function __construct()
    {
        Route::class;
        Method::class;
    }

    /**
     *
     * @Route("/prod/instituicao/findbynome/{str}", name="instituicao_findbynome")
     * @Method("GET")
     *
     */
    public function findByNome($str = null)
    {
        $repo = $this->getDoctrine()->getRepository(Instituicao::class);
        $rs = $repo->findAllByNomes($str);
        
        $results = array(
            'results' => $rs
        );
        
        $normalizer = new ObjectNormalizer();
        $normalizer->setIgnoredAttributes(array(
            'inserted',
            'updated'
        ));
        $encoder = new JsonEncoder();
        
        $serializer = new Serializer(array(
            $normalizer
        ), array(
            $encoder
        ));
        
        $data = $serializer->normalize($results, 'json', array(
            'attributes' => array(
                'id',
                'nome',
                'codigo',
                'cliente' => [
                    'id',
                    'pessoa' => [
                        'nome', 'nome_fantasia'
                    ]
                ],
                'fornecedor' => [
                    'id',
                    'pessoa' => [
                        'nome', 'nome_fantasia'
                    ]
                ]
            )
        ));
        $json = $serializer->serialize($data, 'json');
        
        return new Response($json);
    }
}