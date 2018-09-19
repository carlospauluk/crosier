<?php

namespace App\Controller\Config;

use App\Business\Config\EntMenuBusiness;
use App\Controller\FormListController;
use App\Entity\Config\EntMenu;
use App\EntityHandler\Config\EntMenuEntityHandler;
use App\EntityHandler\EntityHandler;
use App\Form\Config\EntMenuType;
use App\Utils\Repository\FilterData;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * Class EntMenuController.
 * @package App\Controller\Config
 * @author Carlos Eduardo Pauluk
 */
class EntMenuController extends FormListController
{

    private $entityHandler;

    private $entMenuBusiness;

    public function __construct(EntMenuEntityHandler $entityHandler, EntMenuBusiness $entMenuBusiness)
    {
        $this->entityHandler = $entityHandler;
        $this->entMenuBusiness = $entMenuBusiness;
    }

    public function getEntityHandler(): ?EntityHandler
    {
        return $this->entityHandler;
    }

    public function getFormRoute()
    {
        return 'cfg_entMenu_form';
    }

    public function getFormView()
    {
        return 'Config/entMenuForm.html.twig';
    }

    public function getFilterDatas($params)
    {
        return array(
            new FilterData(['label'], 'LIKE', isset($params['filter']['label']) ? $params['filter']['label'] : null)
        );
    }

    public function getListView()
    {
        return 'Config/entMenuList.html.twig';
    }

    public function getListRoute()
    {
        return 'cfg_entMenu_list';
    }


    public function getTypeClass()
    {
        return EntMenuType::class;
    }

    /**
     *
     * @Route("/cfg/entMenu/form/{id}", name="cfg_entMenu_form", defaults={"id"=null}, requirements={"id"="\d+"})
     * @param Request $request
     * @param EntMenu|null $entMenu
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function form(Request $request, EntMenu $entMenu = null)
    {
        if (!$entMenu) {
            $entMenu = new EntMenu();
        }

        $paiId = $request->query->get('pai');
        $pai = $this->getDoctrine()->getRepository(EntMenu::class)->find($paiId);
        $entMenu->setPai($pai);

        $form = $this->createForm(EntMenuType::class, $entMenu, ['action' => $this->generateUrl('cfg_entMenu_form', ['id' => $entMenu->getId(), 'pai' => $paiId])]);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $entity = $form->getData();
                $this->getEntityHandler()->persist($entity);
                $this->addFlash('success', 'Registro salvo com sucesso!');
                return $this->redirectToRoute($this->getFormRoute(), array('id' => $entMenu->getId(), 'pai' => $paiId));
            } else {
                $form->getErrors(true, false);
            }
        }

        // Pode ou não ter vindo algo no $parameters. Independentemente disto, só adiciono form e foi-se.
        $parameters['form'] = $form->createView();
        $parameters['menu'] = 'outro';
        return $this->render($this->getFormView(), $parameters);
    }

    /**
     *
     * @Route("/cfg/entMenu/list/", name="cfg_entMenu_list")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function list(Request $request)
    {
        $dados = null;
//        $params = $request->query->all();
//
//        if (!array_key_exists('filter', $params)) {
//            $params['filter'] = array();
//        }
//
//        try {
//            $repo = $this->getDoctrine()->getRepository(EntMenu::class);
//
//            $filterDatas = $this->getFilterDatas($params);
//            $dados = $repo->findByFilters($filterDatas);
//
//        } catch (\Exception $e) {
//            $this->addFlash('error', 'Erro ao listar (' . $e->getMessage() . ')');
//        }

        $repo = $this->getDoctrine()->getRepository(EntMenu::class);
        $dados = $repo->findBy([],['ordem'=>'ASC','id'=>'DESC']);

        $vParams['dados'] = $dados;
//        $vParams['filter'] = $params['filter'];

        return $this->render($this->getListView(), $vParams);
    }

    /**
     *
     * @Route("/cfg/entMenu/delete/{id}/", name="cfg_entMenu_delete", requirements={"id"="\d+"})
     * @param Request $request
     * @param EntMenu $entMenu
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function delete(Request $request, EntMenu $entMenu)
    {
        return $this->doDelete($request, $entMenu);
    }


    /**
     *
     * @Route("/cfg/entMenu/saveOrdem/", name="cfg_entMenu_saveOrdem")
     * @param Request $request
     * @param EntMenu $entMenu
     * @return Response|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function save(Request $request)
    {
        $ordArr = json_decode($request->request->get('jsonSortable'));
        $this->entMenuBusiness->saveOrdem($ordArr);
        return new Response('');

    }


    /**
     *
     * @Route("/cfg/entMenu/getMainMenu", name="cfg_entMenu_getMainMenu")
     * @param Request $request
     * @return Response
     */
    public function getMainMenu(Request $request)
    {
        $session = new Session();

        $normalizer = new ObjectNormalizer();
        $encoder = new JsonEncoder();
        $serializer = new Serializer([$normalizer], [$encoder]);


        if (!$session->get('mainmenu_pais')) {
            $pais = $this->getDoctrine()->getRepository(EntMenu::class)->findBy(['pai' => null], ['ordem' => 'ASC']);

            $attrs = ['id',
                'label',
                'icon',
                'tipo',
                'cssStyle',
                'app' => ['id', 'route', 'descricao'],
                'filhos' => ['id', 'label', 'icon', 'tipo', 'cssStyle', 'app' => ['id', 'route', 'descricao']]];

            $data = $serializer->normalize($pais, 'json', ['attributes' => $attrs]);
            $session->set('mainmenu_pais', $data);
        } else {
            $pais = $session->get('mainmenu_pais');
        }

        return $this->render(
            '/Config/mainmenu.html.twig',
            array('pais' => $pais)
        );
    }

    /**
     *
     * @Route("/cfg/entMenu/clear", name="cfg_entMenu_clear")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function clear(Request $request)
    {
        $session = new Session();
        $session->remove('mainmenu_pais');
        return $this->redirectToRoute('cfg_entMenu_list');
    }


}