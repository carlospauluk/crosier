<?php

namespace App\OC\Form;

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


            $builder->add('id', HiddenType::class, array(
                'label' => 'Id',
                'required' => false,
                'disabled' => true
            ));


            // deve coincidir com o est_produto.reduzido
            $builder->add('sku', IntegerType::class, array(
                'label' => 'SKU',
                'required' => false
            ));

            $builder->add('produto', TextType::class, array(
                'label' => 'Produto'
            ));

            $builder->add('descricao', TextareaType::class, array(
                'label' => 'Descrição'
            ));

            $builder->add('modelo', TextType::class, array(
                'label' => 'Modelo'
            ));

            $builder->add('marca_id', ChoiceType::class, array(
                'label' => 'Marca',
                'choices' => [$ocProduct['marca_id']],
                'required' => false
            ));

            $builder->add('depto_id', TextType::class, array(
                'label' => 'Departamento',
                'required' => false
            ));

            $builder->add('preco', MoneyType::class, array(
                'label' => 'Preço',
                'currency' => 'BRL',
                'grouping' => 'true',
                'attr' => array(
                    'class' => 'crsr-money'
                ),
                'required' => false
            ));

            $builder->add('qtde', NumberType::class, array(
                'label' => 'Qtde',
                'grouping' => 'true',
                'scale' => 3,
                'attr' => array(
                    'class' => 'crsr-dec3'
                ),
                'required' => true
            ));

            $builder->add('dimensaoC', NumberType::class, array(
                'grouping' => 'true',
                'scale' => 3,
                'attr' => array(
                    'class' => 'crsr-dec3'
                ),
                'required' => true
            ));

            $builder->add('dimensaoL', NumberType::class, array(
                'grouping' => 'true',
                'scale' => 3,
                'attr' => array(
                    'class' => 'crsr-dec3'
                ),
                'required' => true
            ));

            $builder->add('dimensaoA', NumberType::class, array(
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


        });

    }

}