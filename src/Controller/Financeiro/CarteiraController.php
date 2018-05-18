<?php
namespace App\Controller\Financeiro;

use App\Entity\Financeiro\Carteira;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\Financeiro\CarteiraType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use App\Utils\Repository\FilterData;

class CarteiraController extends Controller
{

    /**
     *
     * @Route("/fin/carteira/form/{id}", name="carteira_form", defaults={"id"=null}, requirements={"id"="\d+"})
     */
    public function form(Request $request, Carteira $carteira = null)
    {
        if (! $carteira) {
            $carteira = new Carteira();
            
            $carteira->setInserted(new \DateTime('now'));
            $carteira->setUpdated(new \DateTime('now'));
        }
        
        $form = $this->createForm(CarteiraType::class, $carteira);
        
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $carteira = $form->getData();
            
            // ... perform some action, such as saving the task to the database
            // for example, if Task is a Doctrine entity, save it!
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($carteira);
            $entityManager->flush();
            $this->addFlash('success', 'Registro salvo com sucesso!');
            return $this->redirectToRoute('carteira_form', array('id' => $carteira->getId()) );
        } else {
            $form->getErrors(true, false);
        }
        
        return $this->render('Financeiro/carteiraForm.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     *
     * @Route("/fin/carteira/list/", name="carteira_list")
     */
    public function list(Request $request)
    {
        $dados = null;
        $params = $request->query->all();
        
        if (! array_key_exists('filter', $params)) {
            $params['filter'] = null;
        }
        
        try {
            
            $repo = $this->getDoctrine()->getRepository(Carteira::class);
            
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
        
        return $this->render('Financeiro/carteiraList.html.twig', array(
            'dados' => $dados,
            'filter' => $params['filter']
        ));
    }

    /**
     *
     * @Route("/fin/carteira/list/search/", name="carteira_search")
     *
     * @param Request $request
     */
    public function pesquisar(Request $request)
    {}

    /**
     *
     * @Route("/fin/carteira/delete/{id}/", name="carteira_delete", requirements={"id"="\d+"})
     * @Method("POST")
     *
     */
    public function delete(Request $request, Carteira $carteira)
    {
        if (! $this->isCsrfTokenValid('delete', $request->request->get('token'))) {
            $this->addFlash('error', 'Erro interno do sistema.');
        } else {
            try {
                $em = $this->getDoctrine()->getManager();
                $em->remove($carteira);
                $em->flush();
                $this->addFlash('success', 'post.deleted_successfully');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Erro ao deletar carteira.');
            }
        }
        
        return $this->redirectToRoute('carteira_list');
    }

}