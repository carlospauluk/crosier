<?php

namespace App\Controller\financeiro;

use App\Entity\financeiro\Carteira;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\financeiro\CarteiraType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class CarteiraController extends Controller {

	/**
	 *
	 * @Route("/fin/carteira/form")
	 */
	public function save(Request $request) {
		$carteira = new Carteira();
		
		$carteira->setInserted(new \DateTime('now'));
		$carteira->setUpdated(new \DateTime('now'));
		
		$form = $this->createForm(CarteiraType::class, $carteira);
		
		$form->handleRequest($request);
		
		if ($form->isSubmitted() && $form->isValid()) {
			// $form->getData() holds the submitted values
			// but, the original `$task` variable has also been updated
			$carteira = $form->getData();
			
			// ... perform some action, such as saving the task to the database
			// for example, if Task is a Doctrine entity, save it!
			// $entityManager = $this->getDoctrine()->getManager();
			// $entityManager->persist($task);
			// $entityManager->flush();
			
			return $this->redirectToRoute('task_success');
		} else {
			$form->getErrors(true, false);
		}
		
		return $this->render('financeiro/carteiraForm.html.twig', array (
				'form' => $form->createView() 
		));
	}

	/**
	 *
	 * @Route("/fin/carteira/list", name="carteira_list")
	 */
	public function list(Request $request) {
		$repo = $this->getDoctrine()->getRepository(Carteira::class);
		$dados = $repo->findAll();
		
		return $this->render('financeiro/carteiraList.html.twig', array (
				'dados' => $dados 
		));
	}

	/**
	 *
	 * @Route("/fin/carteira/{id}/delete", name="carteira_delete")
	 * @Method("POST")
	 *
	 */
	public function delete(Request $request, Carteira $carteira) {
		if (! $this->isCsrfTokenValid('delete', $request->request->get('token'))) {
			$this->addFlash('error', 'Erro interno do sistema.');
		} else {
			try {
				$em = $this->getDoctrine()->getManager();
				$em->remove($carteira);
				$em->flush();
				$this->addFlash('success', 'post.deleted_successfully');
			} catch ( \Exception $e ) {
				$this->addFlash('error', 'Erro ao deletar carteira.');
			}
		}
		
		return $this->redirectToRoute('carteira_list');
	}

	/**
	 *
	 * @Route("/teste")
	 *
	 */
	public function teste(Request $request): Response {
		return $this->render('index.html.twig');
	}
}