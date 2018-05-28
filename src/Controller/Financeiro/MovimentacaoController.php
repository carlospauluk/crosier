<?php
namespace App\Controller\Financeiro;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use App\Utils\Repository\FilterData;
use App\Entity\Financeiro\Movimentacao;
use App\Entity\Financeiro\Carteira;
use App\Entity\Financeiro\Status;
use App\Entity\Financeiro\Modo;
use App\Entity\Financeiro\Categoria;
use App\Form\Financeiro\MovimentacaoType;

class MovimentacaoController extends Controller
{

    /**
     *
     * @Route("/fin/movimentacao/form/{id}", name="movimentacao_form", defaults={"id"=null}, requirements={"id"="\d+"})
     */
    public function form(Request $request, Movimentacao $movimentacao = null)
    {
        if (! $movimentacao) {
            $movimentacao = new Movimentacao();
            
            $movimentacao->setInserted(new \DateTime('now'));
            $movimentacao->setUpdated(new \DateTime('now'));
        }
        
        $form = $this->createForm(MovimentacaoType::class, $movimentacao);
        
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $movimentacao = $form->getData();
            
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($movimentacao);
            $entityManager->flush();
            $this->addFlash('success', 'Registro salvo com sucesso!');
            return $this->redirectToRoute('movimentacao_form', array('id' => $movimentacao->getId()) );
        } else {
            $form->getErrors(true, false);
        }
        
        return $this->render('Financeiro/movimentacaoForm.html.twig', array(
            'form' => $form->createView()
        ));
    }
    
    /**
     * Monta os parâmetros para os filtros da listagem.
     * 
     * @return \App\Utils\Repository\FilterData[]
     */
    private function handleFilters($params) {
        if (!isset($params['filter'])) return;
        $filters = array();
        
        if (isset($params['filter']['descricao'])) {
            $filters[] = new FilterData(array('id','descricao'), 'LIKE', $params['filter']['descricao']);
        }
        
        if (isset($params['filter']['dtUtil'])) {
            $filters[] = new FilterData('dtUtil', 'BETWEEN', $params['filter']['dtUtil']);
        }
        
        if (isset($params['filter']['valorTotal'])) {
            $filters[] =  new FilterData('valorTotal', 'BETWEEN', $params['filter']['valorTotal'], 'decimal');
        }
        
        if (isset($params['filter']['carteira'])) {
            $filters[] = new FilterData('carteira', 'IN', $params['filter']['carteira']);
        }
        
        if (isset($params['filter']['status'])) {
            $filters[] = new FilterData('status', 'IN', $params['filter']['status']);
        }
        
        if (isset($params['filter']['modo'])) {
            $filters[] = new FilterData('modo', 'IN', $params['filter']['modo']);
        }
        
        if (isset($params['filter']['categoria'])) {
            $filters[] = new FilterData('categoria', 'IN', $params['filter']['categoria']);
        }
        
        return $filters;
    }
    
    /**
     * Monta os dados para os campos de filtragem.
     */
    private function buildFilterDatas() {
        $filterDatas = array();
        
        $repoCarteira = $this->getDoctrine()->getRepository(Carteira::class);
        $carteiras = $repoCarteira->findAll();
        $filterDatas['carteiras'] = $carteiras;
        
        $filterDatas['status'] = Status::ALL;
        
        $repoModo = $this->getDoctrine()->getRepository(Modo::class);
        $modos = $repoModo->findAll();
        $filterDatas['modos'] = $modos;
        
        $repoCateg = $this->getDoctrine()->getRepository(Categoria::class);
        $categorias = $repoCateg->buildTreeList();
        $filterDatas['categorias'] = $categorias;
        
        return $filterDatas;
    }

    /**
     *
     * @Route("/fin/movimentacao/list/", name="movimentacao_list")
     */
    public function list(Request $request)
    {
        $dados = null;
        $params = $request->query->all();
        
        if (! array_key_exists('filter', $params)) {
            $params['filter'] = null;
        }
        
        try {
            
            $repo = $this->getDoctrine()->getRepository(Movimentacao::class);
            
            if (! $params['filter'] or count($params['filter']) == 0) {
                $dados = $repo->findFirsts(100);
            } else {
                $dados = $repo->findByFilters($this->handleFilters($params));
            }
        } catch (\Exception $e) {
            $this->addFlash('error', 'Erro ao listar (' . $e->getMessage() . ')');
        }
        
        
        
        
        return $this->render('Financeiro/movimentacaoList.html.twig', array(
            'dados' => $dados,
            'filter' => $params['filter'],
            'filterDatas' => $this->buildFilterDatas()
        ));
    }

    /**
     *
     * @Route("/fin/movimentacao/delete/{id}/", name="movimentacao_delete", requirements={"id"="\d+"})
     * @Method("POST")
     *
     */
    public function delete(Request $request, Movimentacao $movimentacao)
    {
        if (! $this->isCsrfTokenValid('delete', $request->request->get('token'))) {
            $this->addFlash('error', 'Erro interno do sistema.');
        } else {
            try {
                $em = $this->getDoctrine()->getManager();
                $em->remove($movimentacao);
                $em->flush();
                $this->addFlash('success', 'post.deleted_successfully');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Erro ao deletar registro.');
            }
        }
        
        return $this->redirectToRoute('movimentacao_list');
    }

}