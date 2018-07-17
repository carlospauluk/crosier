<?php
namespace App\Controller\Producao;

use App\Entity\Estoque\Grade;
use App\Entity\Producao\Confeccao;
use App\Entity\Producao\ConfeccaoItem;
use App\Entity\Producao\Insumo;
use App\Entity\Producao\TipoArtigo;
use App\Service\EntityIdSerializerService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class FichaTecnicaController extends Controller
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
     * @Route("/prod/fichatecnica/inicial/{confeccao}", name="prod_fichaTecnica_inicial", defaults={"confeccao"=null})
     *
     */
    public function inicial(Request $request, Confeccao $confeccao = null)
    {
        $params = array();
        
        // Se mandou o id da confecção na URL...
        if ($confeccao instanceof Confeccao) {
            $params['instituicao']['id'] = $confeccao->getInstituicao()->getId();
            $params['instituicao']['nome'] = $confeccao->getInstituicao()->getNome();
            
            // Já pré-carrega todos os tipos de artigos para o select2
            $repo = $this->getDoctrine()->getRepository(TipoArtigo::class);
            $tiposArtigos = $repo->findAllByInstituicao($confeccao->getInstituicao());
            
            $params['tiposArtigos'] = array();
            foreach ($tiposArtigos as $t) {
                $pTipoArtigo = array();
                if ($t->getId() == $confeccao->getTipoArtigo()->getId()) {
                    $pTipoArtigo['selected'] = true;
                }
                $pTipoArtigo['id'] = $t->getId();
                $pTipoArtigo['text'] = $t->getDescricao();
                $params['tiposArtigos'][] = $pTipoArtigo;
            }
            
            $params['tiposArtigos'] = json_encode($params['tiposArtigos']);
            
            // Já pré-carrega todas as confecções para o select2
            $repo = $this->getDoctrine()->getRepository(Confeccao::class);
            $confeccoes = $repo->findAllByTipoArtigoInstituicao($confeccao->getInstituicao(), $confeccao->getTipoArtigo());
            
            $params['confeccoes'] = array();
            foreach ($confeccoes as $c) {
                $pConfeccao = array();
                if ($c->getId() == $confeccao->getId()) {
                    $pConfeccao['selected'] = true;
                }
                $pConfeccao['id'] = $c->getId();
                $pConfeccao['text'] = $c->getDescricao();
                $params['confeccoes'][] = $pConfeccao;
            }
            
            $params['confeccoes'] = json_encode($params['confeccoes']);
        }
        
        return $this->render('Producao/FichaTecnica/inicial.html.twig', $params);
    }

    /**
     *
     * @Route("/prod/fichatecnica/form/{id}", name="prod_fichaTecnica_form", defaults={"id"=null})
     *
     */
    public function form(Request $request, Confeccao $confeccao = null)
    {
        $params = array();
        
        $params['confeccao'] = $confeccao;
        
        // Carrega a tabela
        $repoConfeccaoItem = $this->getDoctrine()->getRepository(ConfeccaoItem::class);
        $itens = $repoConfeccaoItem->findAllByConfeccao($confeccao);
        
        $tabela = array();
        
        $repoInsumo = $this->getDoctrine()->getRepository(Insumo::class);
        
        $repoGrade = $this->getDoctrine()->getRepository(Grade::class);
        
        $tabela['grade'] = $repoGrade->findGradeArray($confeccao->getGrade());
        
        foreach ($itens as $ci) {
            
            $insumo = array();
            $insumo['descricao'] = $ci->getInsumo()->getDescricao();
            $precoAtual = $repoInsumo->findPrecoAtual($ci->getInsumo());
            $insumo['preco'] = $precoAtual->getPrecoCusto();
            
            $insumo['grade'] = $repoConfeccaoItem->findGradeMontada($ci);
            
            $tabela['tiposInsumos'][$ci->getInsumo()
                ->getTipoInsumo()
                ->getDescricao()]['insumos'][] = $insumo;
        }
        $params['tabela'] = $tabela;
        
        return $this->render('Producao/FichaTecnica/form.html.twig', $params);
    }
}