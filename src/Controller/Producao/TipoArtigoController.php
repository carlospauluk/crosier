<?php
namespace App\Controller\Producao;

use App\Entity\Producao\Instituicao;
use App\Entity\Producao\TipoArtigo;
use App\Service\EntityIdSerializerService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TipoArtigoController extends Controller
{

    private $eSerializer;
    
    public function __construct(EntityIdSerializerService $eSerializer)
    {
        Route::class;
        Method::class;
        $this->eSerializer = $eSerializer;
    }

    /**
     *
     * @Route("/prod/tipoartigo/findAllByInstituicao/{id}", name="prod_tipoArtigo_findAllByInstituicao", defaults={"id"=null}, requirements={"id"="\d+"})
     */
    public function findAllByInstituicao(Request $request, Instituicao $instituicao)
    {
        $repo = $this->getDoctrine()->getRepository(TipoArtigo::class);
        $rs = $repo->findAllByInstituicao($instituicao);
        
        $results = array(
            'results' => $rs
        );
        
        $json = $this->eSerializer->serializeIncluding($results, array(
            'id',
            'descricao'
        ));
        
        return new Response($json);
    }
    
    /**
     *
     * @Route("/prod/tipoartigo/findAll/{str}", name="prod_tipoArtigo_findAll", defaults={"str"=null})
     */
    public function findAll(Request $request, $str)
    {
        $repo = $this->getDoctrine()->getRepository(TipoArtigo::class);
        $rs = $repo->findAll($str);
        
        $results = array(
            'results' => $rs
        );
        
        $json = $this->eSerializer->serializeIncluding($results, array(
            'id',
            'descricao'
        ));
        
        return new Response($json);
    }
}