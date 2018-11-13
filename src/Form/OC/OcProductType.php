<?php

namespace App\Form\OC;

use App\EntityOC\OcCategoryDescription;
use App\EntityOC\OcFilterDescription;
use App\EntityOC\OcFilterGroupDescription;
use App\EntityOC\OcManufacturer;
use App\EntityOC\OcStockStatus;
use App\Utils\Repository\WhereBuilder;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class OcProductType extends AbstractType
{

    private $doctrine;

    public function __construct(RegistryInterface $doctrine)
    {
        $this->doctrine = $doctrine;
    }


    public function buildForm(FormBuilderInterface $builder, array $options)
    {



        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $ocProduct = $event->getData();
            $builder = $event->getForm();
            $ocEntityManager = $this->doctrine->getEntityManager('oc');

            $builder->add('productId', HiddenType::class, array(
                'label' => 'Id',
                'required' => false,
                'disabled' => true
            ));


            // deve coincidir com o est_produto.reduzido
            $builder->add('sku', IntegerType::class, array(
                'label' => 'SKU',
                'required' => false
            ));

            $builder->add('name', TextType::class, array(
                'label' => 'Produto',
                'attr' => ['style' => 'text-transform: none']
            ));

            $builder->add('description', TextareaType::class, array(
                'label' => 'Descrição'
            ));

            $builder->add('model', TextType::class, array(
                'label' => 'Modelo',
                'attr' => ['style' => 'text-transform: none']
            ));

            $ocManufacturers = $ocEntityManager->getRepository(OcManufacturer::class)->findAll(WhereBuilder::buildOrderBy('name ASC'));
            $marcas = [];
            foreach ($ocManufacturers as $ocManufacturer) {
                $marcas[$ocManufacturer->getName()] = $ocManufacturer->getManufacturerId();
            }
            $builder->add('manufacturerId', ChoiceType::class, array(
                'label' => 'Marca',
                'choices' => $marcas,
                'required' => false
            ));

            $ocCategs = $ocEntityManager->getRepository(OcCategoryDescription::class)->findAll(WhereBuilder::buildOrderBy('name ASC'));
            $categs = [];
            foreach ($ocCategs as $ocCateg) {
                $categs[$ocCateg->getName()] = $ocCateg->getCategoryId();
            }
            $builder->add('categoryId', ChoiceType::class, array(
                'label' => 'Departamento',
                'choices' => $categs,
                'required' => false
            ));

            $ocStockStatus = $ocEntityManager->getRepository(OcStockStatus::class)->findAll(WhereBuilder::buildOrderBy('name ASC'));
            $statusEstoque = [];
            foreach ($ocStockStatus as $s) {
                $statusEstoque[$s->getName()] = $s->getStockStatusId();
            }
            $builder->add('stockStatusId', ChoiceType::class, array(
                'label' => 'Status Estoque',
                'choices' => $statusEstoque,
                'required' => false
            ));

            $builder->add('price', MoneyType::class, array(
                'label' => 'Preço',
                'currency' => 'BRL',
                'grouping' => 'true',
                'attr' => array(
                    'class' => 'crsr-money'
                ),
                'required' => false
            ));

            $builder->add('quantity', NumberType::class, array(
                'label' => 'Qtde',
                'grouping' => 'true',
                'scale' => 3,
                'attr' => array(
                    'class' => 'crsr-dec3'
                ),
                'required' => true
            ));

            $builder->add('weight', NumberType::class, array(
                'label' => 'Peso',
                'grouping' => 'true',
                'scale' => 3,
                'attr' => array(
                    'class' => 'crsr-dec3'
                ),
                'required' => true
            ));

            $builder->add('length', NumberType::class, array(
                'label' => 'Comprimento',
                'grouping' => 'true',
                'scale' => 3,
                'attr' => array(
                    'class' => 'crsr-dec3'
                ),
                'required' => true
            ));

            $builder->add('width', NumberType::class, array(
                'label' => 'Largura',
                'grouping' => 'true',
                'scale' => 3,
                'attr' => array(
                    'class' => 'crsr-dec3'
                ),
                'required' => true
            ));

            $builder->add('height', NumberType::class, array(
                'label' => 'Altura',
                'grouping' => 'true',
                'scale' => 3,
                'attr' => array(
                    'class' => 'crsr-dec3'
                ),
                'required' => true
            ));

            $builder->add('status', ChoiceType::class, array(
                'label' => 'Status',
                'choices' => array(
                    'Inativo' => 0,
                    'Ativo' => 1
                )
            ));

            $ocFilterGroupDescriptions = $ocEntityManager->getRepository(OcFilterGroupDescription::class)->findAll(WhereBuilder::buildOrderBy('filterGroupId ASC'));
            $filterGroups = [];
            foreach ($ocFilterGroupDescriptions as $ocFilterDescription) {
                $filterGroups[$ocFilterDescription->getFilterGroupId()] = $ocFilterDescription->getName();
            }

            $ocFilterDescriptions = $ocEntityManager->getRepository(OcFilterDescription::class)->findAll(WhereBuilder::buildOrderBy('filterGroupId ASC'));
            $filters = [];
            foreach ($ocFilterDescriptions as $ocFilterDescription) {
                $filters[$filterGroups[$ocFilterDescription->getFilterGroupId()] . ' > ' . $ocFilterDescription->getName()] = $ocFilterDescription->getFilterId();
            }
            $builder->add('filters', ChoiceType::class, array(
                'label' => 'Filtros',
                'choices' => $filters,
                'multiple' => true,
                'expanded' => true,
                'required' => false
            ));


        });

    }

}