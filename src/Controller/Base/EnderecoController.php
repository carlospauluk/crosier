<?php

namespace App\Controller\Base;

use App\Entity\Base\Endereco;
use App\Entity\Base\EntityId;
use App\EntityHandler\Base\EnderecoEntityHandler;
use App\Form\Base\EnderecoType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class EnderecoController extends Controller
{

    private $routeToRedirect;

    private $entityHandler;

    public function __construct(EnderecoEntityHandler $enderecoEntityHandler)
    {
        $this->entityHandler = $enderecoEntityHandler;
    }

    /**
     * @return mixed
     */
    public function getRouteToRedirect()
    {
        return $this->routeToRedirect;
    }

    /**
     * @param mixed $routeToRedirect
     */
    public function setRouteToRedirect($routeToRedirect): void
    {
        $this->routeToRedirect = $routeToRedirect;
    }


    public function doEnderecoForm(Controller $controller, Request $request, EntityId $ref, Endereco $endereco = null)
    {
        if (!$endereco) {
            $endereco = new Endereco();
        }
        $formEndereco = $controller->createForm(EnderecoType::class, $endereco);
        $formEndereco->handleRequest($request);

        if ($formEndereco->isSubmitted()) {
            if ($formEndereco->isValid()) {
                $endereco = $formEndereco->getData();
                $ref->addEndereco($endereco);
                $controller->getEntityHandler()->save($ref);
                $controller->addFlash('success', 'Registro salvo com sucesso!');
                return $controller->redirectToRoute($this->getRouteToRedirect(), array('id' => $ref->getId(), '_fragment' => 'enderecos'));
            } else {
                $formEndereco->getErrors(true, false);
            }
        }

        return $controller->render('Base/enderecoForm.html.twig', array(
            'ref' => $ref,
            'routeToRedirect' => $this->getRouteToRedirect(),
            'formEndereco' => $formEndereco->createView()
        ));


    }

    public function doEnderecoDelete(Controller $controller, Request $request, EntityId $ref, Endereco $endereco)
    {
        if (!$controller->isCsrfTokenValid('delete', $request->request->get('token'))) {
            $controller->addFlash('error', 'Erro interno do sistema.');
        } else {
            try {
                $controller->getEnderecoEntityHandler()->delete($endereco);
                $controller->addFlash('success', 'Registro deletado com sucesso.');
            } catch (\Exception $e) {
                $controller->addFlash('error', 'Erro ao deletar registro.');
            }
        }

        return $controller->redirectToRoute($this->getRouteToRedirect(), array('id' => $ref->getId(), '_fragment' => 'enderecos'));
    }

}