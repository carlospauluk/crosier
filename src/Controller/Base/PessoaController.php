<?php
namespace App\Controller\Base;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\Financeiro\CarteiraType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use App\Utils\Repository\FilterData;
use App\Entity\Base\Pessoa;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Serializer;

class PessoaController extends Controller
{

    /**
     *
     * @Route("/base/pessoa/findbynome/{str}", name="pessoaFindByNome")
     * @Method("GET")
     *
     */
    public function findByNome($str=null) {
        
        $repo = $this->getDoctrine()->getRepository(Pessoa::class);
        $pessoas = $repo->findAllByNome($str);
        
        $results = array('results' => $pessoas);
        
        $normalizer = new ObjectNormalizer();
        $encoder = new JsonEncoder();
        
        $serializer = new Serializer(array($normalizer), array($encoder));
        $json = $serializer->serialize($results, 'json');
        
        return new Response($json);
    }
    
}