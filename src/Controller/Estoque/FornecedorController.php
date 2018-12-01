<?php

namespace App\Controller\Estoque;

use App\Business\Base\PessoaBusiness;
use App\Business\Estoque\FornecedorBusiness;
use App\Controller\Base\EnderecoController;
use App\Controller\FormListController;
use App\Entity\Base\Endereco;
use App\Entity\Base\Pessoa;
use App\Entity\Estoque\Fornecedor;
use App\EntityHandler\Base\EnderecoEntityHandler;
use App\EntityHandler\EntityHandler;
use App\EntityHandler\Estoque\FornecedorEntityHandler;
use App\Form\Base\EnderecoType;
use App\Form\Estoque\FornecedorType;
use App\Utils\Repository\FilterData;
use App\Utils\Repository\WhereBuilder;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class FornecedorController extends FormListController
{

    private $entityHandler;

    private $enderecoEntityHandler;

    private $fornecedorBusiness;

    private $pessoaBusiness;

    private $enderecoController;

    public function __construct(FornecedorEntityHandler $entityHandler,
                                EnderecoEntityHandler $enderecoEntityHandler,
                                FornecedorBusiness $fornecedorBusiness,
                                PessoaBusiness $pessoaBusiness,
                                EnderecoController $enderecoController)
    {
        $this->entityHandler = $entityHandler;
        $this->enderecoEntityHandler = $enderecoEntityHandler;
        $this->fornecedorBusiness = $fornecedorBusiness;
        $this->pessoaBusiness = $pessoaBusiness;
        $this->enderecoController = $enderecoController;
        $this->enderecoController->setRouteToRedirect('est_fornecedor_form');
    }

    public function getEntityHandler(): ?EntityHandler
    {
        return $this->entityHandler;
    }

    public function getEnderecoEntityHandler(): ?EnderecoEntityHandler
    {
        return $this->enderecoEntityHandler;
    }


    public function getFormRoute()
    {
        return 'est_fornecedor_form';
    }

    public function getFormView()
    {
        return 'Estoque/fornecedorForm.html.twig';
    }

    public function getFilterDatas($params)
    {
        return array(
            new FilterData('e.codigo', 'LIKE', isset($params['filter']['codigo']) ? $params['filter']['codigo'] : null),
            new FilterData(['p.nome', 'p.nomeFantasia'], 'LIKE', isset($params['filter']['nome']) ? $params['filter']['nome'] : null)
        );
    }

    public function getListView()
    {
        return 'Estoque/fornecedorList.html.twig';
    }

    public function getListRoute()
    {
        return 'est_fornecedor_list';
    }


    public function getTypeClass()
    {
        return FornecedorType::class;
    }

    /**
     *
     * @Route("/est/fornecedor/form/{id}", name="est_fornecedor_form", defaults={"id"=null}, requirements={"id"="\d+"})
     * @param Request $request
     * @param Fornecedor|null $fornecedor
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function form(Request $request, Fornecedor $fornecedor = null)
    {
        if (!$fornecedor) {
            $fornecedor = new Fornecedor();
            $pessoa = new Pessoa();
            $pessoa->setTipoPessoa('PESSOA_JURIDICA');
            $fornecedor->setPessoa($pessoa);
        }

        // Se foi passado via post
        $data = $this->fornecedorBusiness->fornecedor2FormData($fornecedor);
        $dataPosted = $request->request->get('fornecedor');
        if (is_array($dataPosted)) {
            $this->fornecedorBusiness->parseFormData($dataPosted);
            $data = array_merge($data, $dataPosted);
        }

        $form = $this->createForm($this->getTypeClass(), $data);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $fornecedor = $this->fornecedorBusiness->formData2Fornecedor($data);
                $this->getEntityHandler()->save($fornecedor);
                $this->addFlash('success', 'Registro salvo com sucesso!');
                return $this->redirectToRoute($this->getFormRoute(), array('id' => $fornecedor->getId()));
            } else {
                $form->getErrors(true, false);
            }
        }

        $formEnderecoView = null;
        if ($fornecedor->getId()) {
            $endereco = new Endereco();
            $formEndereco = $this->createForm(EnderecoType::class, $endereco,
                array('action' => $this->generateUrl('est_fornecedor_enderecoForm', array('endereco' => $endereco->getId(), 'ref' => $fornecedor->getId()))));
            $formEnderecoView = $formEndereco->createView();
        }

        return $this->render($this->getFormView(), array(
            'ref' => $fornecedor,
            'form' => $form->createView(),
            'formEndereco' => $formEnderecoView
        ));
    }

    /**
     *
     * @Route("/est/fornecedor/enderecoForm/{ref}/{endereco}", name="est_fornecedor_enderecoForm", defaults={"endereco"=null})
     * @param Request $request
     * @param Fornecedor $ref
     * @param Endereco|null $endereco
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @ParamConverter("ref", class="App\Entity\Estoque\Fornecedor", options={"mapping": {"ref": "id"}})
     */
    public function enderecoForm(Request $request, Fornecedor $ref, Endereco $endereco = null)
    {
        return $this->enderecoController->doEnderecoForm($this, $request, $ref, $endereco);
    }

    /**
     *
     * @Route("/est/fornecedor/enderecoDelete/{ref}/{endereco}", name="est_fornecedor_enderecoDelete", requirements={"ref"="\d+","endereco"="\d+"})
     * @param Request $request
     * @param Fornecedor $ref
     * @param Endereco|null $endereco
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function enderecoDelete(Request $request, Fornecedor $ref, Endereco $endereco)
    {
        return $this->enderecoController->doEnderecoDelete($this, $request, $ref, $endereco);
    }

    /**
     *
     * @Route("/est/fornecedor/list/", name="est_fornecedor_list")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function list(Request $request)
    {
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
                'codigo',
                'pessoa' => [
                    'id',
                    'nome',
                    'nome_fantasia'
                ],
                'updated' => ['timestamp']
            )
        );
    }

    /**
     *
     * @Route("/est/fornecedor/datatablesJsList/", name="est_fornecedor_datatablesJsList")
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
     * @Route("/est/fornecedor/delete/{id}/", name="est_fornecedor_delete", requirements={"id"="\d+"})
     * @param Request $request
     * @param Fornecedor $fornecedor
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function delete(Request $request, Fornecedor $fornecedor)
    {
        return $this->doDelete($request, $fornecedor);
    }

    /**
     *
     * @Route("/est/fornecedor/findByDocumento/{documento}", name="est_fornecedor_findByDocumento")
     * @param null $documento
     * @return Response|void
     * @throws \Exception
     */
    public function findByDocumento($documento = null)
    {
        if ($documento == null) {
            return;
        }
        $repo = $this->getDoctrine()->getRepository(Fornecedor::class);
        $fornecedor = $repo->findByDocumento(preg_replace('/\D/', '', $documento));

        if (!$fornecedor) {
            return null;
        }

        $normalizer = new ObjectNormalizer();
        $encoder = new JsonEncoder();

        if ($fornecedor->getPessoa()) {
            $this->pessoaBusiness->fillTransients($fornecedor->getPessoa());
        }

        $attributes = [
            'id',
            'codigo',
            'fone1',
            'fone2',
            'fone3',
            'fone4',
            'inscricao_estadual',
            'pessoa' => ['id', 'nome', 'nomeFantasia', 'documento',
                'endereco' => ['id', 'bairro', 'cep', 'cidade', 'estado', 'complemento', 'logradouro', 'numero']
            ]];

        $serializer = new Serializer(array($normalizer), array($encoder));
        $json = $serializer->serialize($fornecedor, 'json', ['attributes' => $attributes]);

        return new JsonResponse($json);
    }

    /**
     *
     * @Route("/est/fornecedor/findByCodigoOuNome/{str}", name="est_fornecedor_findByCodigoOuNome", defaults={"str"=null})
     * @param $str
     * @return JSONResponse
     * @throws \Exception
     */
    public function findByCodigoOuNome($str = null)
    {
//        if ($str == null) {
//            return;
//        }
        if (is_numeric($str)) {
            $params['filter']['codigo'] = $str;
        }
        $params['filter']['nome'] = $str;

        $filterDatas = $this->doGetFilterDatas($params);

        $fornecedores = $this->getDoctrine()->getRepository(Fornecedor::class)->findByFilters($filterDatas, WhereBuilder::buildOrderBy('codigo ASC'), 0, null);
        if (!$fornecedores or count($fornecedores) < 0) {
            return null;
        }

        $rs = [];

        foreach ($fornecedores as $fornecedor) {
            if ($fornecedor->getPessoa()) {
                $this->pessoaBusiness->fillTransients($fornecedor->getPessoa());
            }


            $r['id'] = $fornecedor->getId();
            $r['codigo'] = $fornecedor->getCodigo();
            $r['pessoa']['nome'] = $fornecedor->getPessoa()->getNome();
            $r['pessoa']['nomeFantasia'] = $fornecedor->getPessoa()->getNomeFantasia();

            $rs[] = $r;
        }

        return new JsonResponse($rs);
    }

    /**
     *
     * @Route("/est/fornecedor/findById/{fornecedor}", name="est_fornecedor_findById", requirements={"fornecedor"="\d+"})
     * @param Fornecedor $fornecedor
     * @return JSONResponse
     */
    public function findById(Fornecedor $fornecedor)
    {
        $r['id'] = $fornecedor->getId();
        $r['codigo'] = $fornecedor->getCodigo();
        $r['pessoa']['nome'] = $fornecedor->getPessoa()->getNome();
        $r['pessoa']['nomeFantasia'] = $fornecedor->getPessoa()->getNomeFantasia();
        return new JsonResponse($r);
    }


}