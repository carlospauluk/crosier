<?php

namespace App\Controller\Estoque;

use App\Business\Estoque\OCBusiness;
use App\Controller\FormListController;
use App\Entity\Estoque\Produto;
use App\EntityHandler\EntityHandler;
use App\EntityHandler\Estoque\ProdutoEntityHandler;
use App\Form\Estoque\ProdutoType;
use App\Form\OC\OcProductType;
use App\Utils\ExceptionUtils;
use App\Utils\Repository\FilterData;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ProdutoController
 * @package App\Controller\Estoque
 */
class ProdutoController extends FormListController
{

    private $entityHandler;

    private $ocBusiness;

    /**
     *
     * @Route("/est/produto/form/{id}", name="est_produto_form", defaults={"id"=null}, requirements={"id"="\d+"})
     * @param Request $request
     * @ParamConverter("produto", class="App\Entity\Estoque\Produto")
     * @param Produto|null $produto
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function form(Request $request, Produto $produto = null)
    {
        // busca (se tiver) o produtod a loja virtual
        $ocProduct = $this->getOcProduct($produto);
        $ocProductForm = $this->createForm(OcProductType::class);
        $ocProductForm->setData($ocProduct);
        $ocProductForm->handleRequest($request);

        if ($ocProductForm->isSubmitted()) {
            if ($ocProductForm->isValid()) {
                $this->getLogger()->info('Iniciando o saveOcProduct()');
                try {
                    $ocProductArray = $ocProductForm->getData();
                    $this->getOcBusiness()->saveOcProduct($produto, $ocProductArray);
                    $this->addFlash('success', 'Registro salvo com sucesso!');
                    return $this->redirectToRoute('est_produto_form', array(
                        'id' => $produto->getId(),
                        '_fragment' => 'loja-virtual'
                    ));
                } catch (\Exception $e) {
                    $msg = ExceptionUtils::treatException($e);
                    $this->addFlash('error', $msg);
                    $this->addFlash('error', 'Erro ao salvar!');
                }
            } else {
                $errors = $ocProductForm->getErrors(true, true);
                foreach ($errors as $error) {
                    $this->addFlash('error', $error->getMessage());
                }
            }
        }
        $params['ocProductForm'] = $ocProductForm->createView();

        return $this->doForm($request, $produto, $params);
    }

    public function getOcProduct(?Produto $produto)
    {
        return $this->getOcBusiness()->getOcProductArrayByProduto($produto);
    }


    public function getFilterDatas($params)
    {
        return array(
            new FilterData('e.reduzidoEkt', 'EQ', $params['filter']['reduzido_ekt']),
            new FilterData('e.reduzido', 'EQ', $params['filter']['reduzido']),
            new FilterData('e.descricao', 'LIKE', $params['filter']['descricao']),
            new FilterData('e.atual', 'EQ_BOOL', $params['filter']['atual']),
            new FilterData('e.naLojaVirtual', 'EQ_BOOL', $params['filter']['naLojaVirtual']),
//            new FilterData('sd.depto', 'EQ', $params['filter']['p_depto']),
//            new FilterData('p.subdepto', 'EQ', $params['filter']['p_subdepto']),
        );
    }

    /**
     *
     * @Route("/est/produto/list/", name="est_produto_list")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function list(Request $request)
    {
//        $parameters['filter']['atual'] = true;
//        $request->query->add($parameters);
        return $this->doList($request);
    }

    /**
     * @return array|mixed
     */
    public function getNormalizeAttributes()
    {
        return array(
            'attributes' => array(
                'id',
                'descricao',
                'reduzido',
                'reduzidoEkt',
                'fornecedor' => [
                    'id',
                    'codigo',
                    'pessoa' => ['nome', 'nomeFantasia']
                ],
                'updated' => ['timestamp']
            )
        );
    }

    /**
     *
     * @Route("/est/produto/datatablesJsList/", name="est_produto_datatablesJsList")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function datatablesJsList(Request $request)
    {
        $jsonResponse = $this->doDatatablesJsList($request);
        return $jsonResponse;
    }

    /**
     *
     * @Route("/est/produto/delete/{id}/", name="est_produto_delete", requirements={"id"="\d+"})
     * @param Request $request
     * @param Produto $produto
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function delete(Request $request, Produto $produto)
    {
        return $this->doDelete($request, $produto);
    }

    public function getListView()
    {
        return 'Estoque/produtoList.html.twig';
    }

    public function getListRoute()
    {
        return 'est_produto_list';
    }


    public function getTypeClass()
    {
        return ProdutoType::class;
    }

    /**
     * @required
     * @param ProdutoEntityHandler $entityHandler
     */
    public function setEntityHandler(ProdutoEntityHandler $entityHandler)
    {
        $this->entityHandler = $entityHandler;
    }

    /**
     * @return EntityHandler|null
     */
    public function getEntityHandler(): EntityHandler
    {
        return $this->entityHandler;
    }

    public function getFormRoute()
    {
        return 'est_produto_form';
    }

    public function getFormView()
    {
        return 'Estoque/produtoForm.html.twig';
    }

    /**
     * @return mixed
     */
    public function getOcBusiness(): OCBusiness
    {
        return $this->ocBusiness;
    }

    /**
     * @required
     * @param mixed $ocBusiness
     */
    public function setOcBusiness(OCBusiness $ocBusiness): void
    {
        $this->ocBusiness = $ocBusiness;
    }


    /**
     *
     * @Route("/est/produto/corrigirEstProdutoOcProduct/", name="est_produto_corrigirEstProdutoOcProduct")
     * @param Request $request
     * @return Response
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function corrigirEstProdutoOcProduct(Request $request)
    {
        $r = $this->getOcBusiness()->corrigirEstProdutoOcProduct();
        return new Response($r);
    }

    /**
     *
     * @Route("/est/produto/saveOcImages/{produto}", name="est_produto_saveOcImages", requirements={"produto"="\d+"})
     * @param Request $request
     * @param Produto $produto
     * @return Response
     */
    public function saveOcImages(Request $request, Produto $produto)
    {
        try {
            $qtdeImagensAtualizadas = $this->getOcBusiness()->saveImages($produto);
            if ($qtdeImagensAtualizadas > 0) {
                $this->addFlash('success', $qtdeImagensAtualizadas . ' fotos atualizadas!');
            } else {
                $this->addFlash('warn', 'Nenhuma imagem para atualizar. Elas realmente estão na pasta? E os nomes?');
            }
        } catch (\Exception $e) {
            $this->addFlash('error', 'Erro ao salvar imagens do produtos');
        }
        return $this->redirectToRoute('est_produto_form', ['id' => $produto->getId(), '_fragment' => 'loja-virtual']);
    }

    /**
     *
     * @Route("/est/produto/corrigirOcNomeEDescricao/{produto}", name="est_produto_corrigirOcNomeEDescricao", requirements={"produto"="\d+"})
     * @param Request $request
     * @param Produto $produto
     * @return Response
     */
    public function corrigirOcNomeEDescricao(Request $request, Produto $produto)
    {
        try {
            $this->getOcBusiness()->corrigirOcNomeEDescricao($produto);
            $this->addFlash('success', 'Nome e descrição corrigidos no site com sucesso!');
        } catch (\Exception $e) {
            $this->addFlash('error', 'Erro ao corrigir nome e descrição do produto no site');
        }
        return $this->redirectToRoute('est_produto_form', ['id' => $produto->getId(), '_fragment' => 'loja-virtual']);

    }


}