<?php

namespace App\Form\Financeiro;

use App\Entity\Financeiro\CentroCusto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CentroCustoType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('codigo', IntegerType::class, array(
            'label' => 'Código'
        ));

        $builder->add('descricao', TextType::class, array(
            'label' => 'Descrição'
        ));

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => CentroCusto::class
        ));
    }
}