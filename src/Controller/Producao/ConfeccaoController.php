<?php
namespace App\Controller\Producao;

use App\Entity\Producao\Confeccao;
use App\Entity\Producao\Instituicao;
use App\Entity\Producao\TipoArtigo;
use App\Service\EntityIdSerializerService;
use App\Utils\Repository\FilterData;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\Producao\ConfeccaoType;

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
     * @Route("/prod/confeccao/findAllByTipoArtigoInstituicao/{instituicao}/{tipoArtigo}", name="prod_confeccao_findAllByTipoArtigoInstituicao", defaults={"instituicao"=null,"tipoArtigo"=null}, requirements={"instituicao"="\d+","tipoArtigo"="\d+"})
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
    
    /**
     *
     * @Route("/prod/confeccao/form/{id}", name="prod_confeccao_form", defaults={"id"=null}, requirements={"id"="\d+"})
     *
     */
    public function form(Request $request, Confeccao $confeccao = null)
    {
        if (! $confeccao) {
            $confeccao = new Confeccao();
            
            $confeccao->setInserted(new \DateTime('now'));
            $confeccao->setUpdated(new \DateTime('now'));
        }
        
        $form = $this->createForm(ConfeccaoType::class, $confeccao);
        
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $confeccao = $form->getData();
            
            // ... perform some action, such as saving the task to the database
            // for example, if Task is a Doctrine entity, save it!
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($confeccao);
            $entityManager->flush();
            $this->addFlash('success', 'Registro salvo com sucesso!');
            return $this->redirectToRoute('prod_confeccao_form', array('id' => $confeccao->getId()) );
        } else {
            $form->getErrors(true, false);
        }
        
        return $this->render('Producao/confeccaoForm.html.twig', array(
            'form' => $form->createView()
        ));
    }
    
    /**
     *
     * @Route("/prod/confeccao/list/", name="prod_confeccao_list")
     */
    public function list(Request $request)
    {
        $dados = null;
        $params = $request->query->all();
        
        if (! array_key_exists('filter', $params)) {
            $params['filter'] = null;
        }
        
        try {
            
            $repo = $this->getDoctrine()->getRepository(Confeccao::class);
            
            if (! $params['filter'] or count($params['filter']) == 0) {
                $dados = $repo->findAll();
            } else {
                
                $filters = array(
                    new FilterData('descricao', 'LIKE', $params['filter']['descricao']),
                    new FilterData('dtConsolidado', 'BETWEEN', $params['filter']['dtConsolidado'])
                );
                
                $dados = $repo->findByFilters($filters);
            }
        } catch (\Exception $e) {
            $this->addFlash('error', 'Erro ao listar (' . $e->getMessage() . ')');
        }
        
        return $this->render('Producao/confeccaoList.html.twig', array(
            'dados' => $dados,
            'filter' => $params['filter']
        ));
    }
}