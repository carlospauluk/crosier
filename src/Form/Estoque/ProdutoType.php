<?php

namespace App\Form\Estoque;

use App\Entity\Estoque\Fornecedor;
use App\Entity\Estoque\Produto;
use App\Entity\Financeiro\Banco;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProdutoType extends AbstractType
{

    private $doctrine;

    public function __construct(RegistryInterface $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add('id', IntegerType::class, array(
            'label' => 'Id',
            'required' => false,
            'disabled' => true
        ));


        $builder->add('reduzido', IntegerType::class, array(
            'label' => 'Reduzido',
            'required' => false
        ));

        $builder->add('reduzidoEkt', IntegerType::class, array(
            'label' => 'Reduzido (EKT)',
            'required' => false
        ));

        $builder->add('descricao', TextType::class, array(
            'label' => 'Descrição'
        ));

        $builder->add('referencia', TextType::class, array(
            'label' => 'Referência',
            'required' => false
        ));

        $builder->add('fornecedor', EntityType::class, array(
            'label' => 'Fornecedor',
            'class' => Fornecedor::class,
            'choices' => [],
            'choice_label' => function (?Fornecedor $fornecedor) {
                if ($fornecedor and $fornecedor->getPessoa()) {
                    return $fornecedor->getCodigo() . ' - ' . $fornecedor->getPessoa()->getNomeFantasia();
                }
            },
            'required' => true
        ));

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Produto::class
        ));
    }
}