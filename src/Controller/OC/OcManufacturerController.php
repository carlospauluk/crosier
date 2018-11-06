<?php

namespace App\Controller\Estoque;

use App\Business\Estoque\ManufacturerBusiness;
use App\Controller\FormListController;
use App\Entity\Estoque\Manufacturer;
use App\EntityHandler\EntityHandler;
use App\EntityHandler\Estoque\ManufacturerEntityHandler;
use App\Form\Estoque\ManufacturerType;
use App\OC\Form\OcProductType;
use App\Utils\ExceptionUtils;
use App\Utils\Repository\FilterData;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ManufacturerController
 * @package App\Controller\Estoque
 */
class ManufacturerController extends FormListController
{

    private $entityHandler;

    private $manufacturerBusiness;

    /**
     *
     * @Route("/est/manufacturer/form/{id}", name="est_manufacturer_form", defaults={"id"=null}, requirements={"id"="\d+"})
     * @param Request $request
     * @ParamConverter("manufacturer", class="App\Entity\Estoque\Manufacturer")
     * @param Manufacturer|null $manufacturer
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function form(Request $request, Manufacturer $manufacturer = null)
    {
        $ocProduct = $this->getOcProduct($manufacturer);

        $ocProductForm = $this->createForm(OcProductType::class);
        $ocProductForm->setData($ocProduct);
        $ocProductForm->handleRequest($request);

        if ($ocProductForm->isSubmitted()) {
            if ($ocProductForm->isValid()) {
                try {
                    $entity = $ocProductForm->getData();
                    $this->getEntityHandler()->save($entity);
                    $this->addFlash('success', 'Registro salvo com sucesso!');
                    // return $this->redirectToRoute($this->getFormRoute(), array('id' => $entityId->getId()));
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

        return $this->doForm($request, $manufacturer, $params);
    }

    public function saveOcProduct()
    {

    }

    public function getOcProduct(?Manufacturer $manufacturer)
    {
        return $this->getManufacturerBusiness()->getOcProductArrayByManufacturer($manufacturer);
    }


    public function getFilterDatas($params)
    {
        return array(
            new FilterData('e.reduzidoEkt', 'EQ', $params['filter']['reduzido_ekt']),
            new FilterData('e.reduzido', 'EQ', $params['filter']['reduzido']),
            new FilterData('e.descricao', 'LIKE', $params['filter']['descricao']),
//            new FilterData('sd.depto', 'EQ', $params['filter']['p_depto']),
//            new FilterData('p.subdepto', 'EQ', $params['filter']['p_subdepto']),
        );
    }

    /**
     *
     * @Route("/est/manufacturer/list/", name="est_manufacturer_list")
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
     * @Route("/est/manufacturer/datatablesJsList/", name="est_manufacturer_datatablesJsList")
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
     * @Route("/est/manufacturer/delete/{id}/", name="est_manufacturer_delete", requirements={"id"="\d+"})
     * @param Request $request
     * @param Manufacturer $manufacturer
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function delete(Request $request, Manufacturer $manufacturer)
    {
        return $this->doDelete($request, $manufacturer);
    }

    public function getListView()
    {
        return 'Estoque/manufacturerList.html.twig';
    }

    public function getListRoute()
    {
        return 'est_manufacturer_list';
    }


    public function getTypeClass()
    {
        return ManufacturerType::class;
    }

    /**
     * @required
     * @param ManufacturerEntityHandler $entityHandler
     */
    public function setEntityHandler(ManufacturerEntityHandler $entityHandler)
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

    /**
     * @return mixed
     */
    public function getManufacturerBusiness(): ManufacturerBusiness
    {
        return $this->manufacturerBusiness;
    }

    /**
     * @required
     * @param mixed $manufacturerBusiness
     */
    public function setManufacturerBusiness(ManufacturerBusiness $manufacturerBusiness): void
    {
        $this->manufacturerBusiness = $manufacturerBusiness;
    }


    public function getFormRoute()
    {
        return 'est_manufacturer_form';
    }

    public function getFormView()
    {
        return 'Estoque/manufacturerForm.html.twig';
    }


}