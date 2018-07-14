<?php
namespace App\Controller\Producao;

use App\Entity\Producao\Confeccao;
use App\Entity\Producao\Instituicao;
use App\Entity\Producao\TipoArtigo;
use App\Service\EntityIdSerializerService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ConfeccaoController extends Controller
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
     * @Route("/prod/confeccao/findAllByTipoArtigoInstituicao/{instituicao}/{tipoArtigo}", name="prod_findAllByTipoArtigoInstituicao", defaults={"instituicao"=null,"tipoArtigo"=null}, requirements={"instituicao"="\d+","tipoArtigo"="\d+"})
     */
    public function findAllByTipoArtigoInstituicao(Request $request, Instituicao $instituicao, TipoArtigo $tipoArtigo)
    {
        $repo = $this->getDoctrine()->getRepository(Confeccao::class);
        $rs = $repo->findAllByTipoArtigoInstituicao($instituicao,$tipoArtigo);
        
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