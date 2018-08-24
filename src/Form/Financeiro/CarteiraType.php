<?php

namespace App\Form\Financeiro;

use App\Entity\Financeiro\Banco;
use App\Entity\Financeiro\Carteira;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CarteiraType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('codigo', IntegerType::class, array(
            'label' => 'Código'
        ));

        $builder->add('descricao', TextType::class, array(
            'label' => 'Descrição'
        ));

        $builder->add('dtConsolidado', DateType::class, array(
            'widget' => 'single_text',
            'format' => 'dd/MM/yyyy',
            'label' => 'Dt Consolidado',
            'attr' => array('class' => 'crsr-date')
        ));

        $builder->add('concreta', ChoiceType::class, array(
            'choices' => array(
                'Sim' => true,
                'Não' => false
            )
        ));

        $builder->add('abertas', ChoiceType::class, array(
            'choices' => array(
                'Sim' => true,
                'Não' => false
            )
        ));

        $builder->add('caixa', ChoiceType::class, array(
            'choices' => array(
                'Sim' => true,
                'Não' => false
            )
        ));

        $builder->add('cheque', ChoiceType::class, array(
            'choices' => array(
                'Sim' => true,
                'Não' => false
            )
        ));

        $builder->add('banco', EntityType::class, array(
            // looks for choices from this entity
            'class' => Banco::class,

            // uses the User.username property as the visible option string
            'choice_label' => function (Banco $banco) {
                return $banco->getCodigoBanco() . " - " . $banco->getNome();
            }

            // used to render a select box, check boxes or radios
            // 'multiple' => true,
            // 'expanded' => true,
        ));

        $builder->add('agencia', TextType::class, array(
            'label' => 'Agência',
            'required' => false
        ));

        $builder->add('conta', TextType::class, array(
            'label' => 'Conta',
            'required' => false
        ));

        $builder->add('limite', MoneyType::class, array(
            'label' => 'Limite',
            'currency' => 'BRL',
            'grouping' => 'true',
            'required' => false,
            'attr' => array(
                'class' => 'crsr-money'
            )
        ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Carteira::class
        ));
    }
}