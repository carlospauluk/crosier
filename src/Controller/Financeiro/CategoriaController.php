<?php
namespace App\Controller\Financeiro;

use App\Entity\Financeiro\Categoria;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Utils\Repository\FilterData;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class CategoriaController extends Controller
{

    /**
     *
     * @Route("/fin/categoria/form/{id}", name="categoria_form", defaults={"id"=null}, requirements={"id"="\d+"})
     */
    public function form(Request $request, Categoria $categoria = null)
    {
//         if (! $categoria) {
//             $categoria = new Categoria();
            
//             $categoria->setInserted(new \DateTime('now'));
//             $categoria->setUpdated(new \DateTime('now'));
//         }
        
//         $form = $this->createForm(CategoriaType::class, $categoria);
        
//         $form->handleRequest($request);
        
//         if ($form->isSubmitted() && $form->isValid()) {
//             // $form->getData() holds the submitted values
//             // but, the original `$task` variable has also been updated
//             $categoria = $form->getData();
            
//             // ... perform some action, such as saving the task to the database
//             // for example, if Task is a Doctrine entity, save it!
//             $entityManager = $this->getDoctrine()->getManager();
//             $entityManager->persist($categoria);
//             $entityManager->flush();
//             $this->addFlash('success', 'Registro salvo com sucesso!');
//             return $this->redirectToRoute('categoria_form', array('id' => $categoria->getId()) );
//         } else {
//             $form->getErrors(true, false);
//         }
        
//         return $this->render('Financeiro/categoriaForm.html.twig', array(
//             'form' => $form->createView()
//         ));
    }

    /**
     *
     * @Route("/fin/categoria/list/", name="categoria_list")
     */
    public function list(Request $request)
    {
        $dados = null;
        $params = $request->query->all();
        
        if (! array_key_exists('filter', $params)) {
            $params['filter'] = null;
        }
        
        try {
            
            $repo = $this->getDoctrine()->getRepository(Categoria::class);
            
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
        
        return $this->render('Financeiro/categoriaList.html.twig', array(
            'dados' => $dados,
            'filter' => $params['filter']
        ));
    }

    /**
     *
     * @Route("/fin/categoria/delete/{id}/", name="categoria_delete", requirements={"id"="\d+"})
     *
     */
    public function delete(Request $request, Categoria $categoria)
    {
        if (! $this->isCsrfTokenValid('delete', $request->request->get('token'))) {
            $this->addFlash('error', 'Erro interno do sistema.');
        } else {
            try {
                $em = $this->getDoctrine()->getManager();
                $em->remove($categoria);
                $em->flush();
                $this->addFlash('success', 'post.deleted_successfully');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Erro ao deletar categoria.');
            }
        }
        
        return $this->redirectToRoute('categoria_list');
    }
    
    /**
     *
     * @Route("/fin/categoria/treelist/", name="categoria_treelist")
     *
     */
    public function getTreeList() {
        
        $repo = $this->getDoctrine()->getRepository(Categoria::class);
        $categorias = $repo->buildTreeList();
       
        $normalizer = new ObjectNormalizer();
        $normalizer->setIgnoredAttributes(array('pai', 'subCategs'));
        $encoder = new JsonEncoder();
        
        $serializer = new Serializer(array($normalizer), array($encoder));
        $json = $serializer->serialize($categorias, 'json'); 
        
        return new Response($json);
    }

}