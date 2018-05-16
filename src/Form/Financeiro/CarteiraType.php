<?php
namespace App\Form\Financeiro;

use App\Entity\Financeiro\Carteira;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CarteiraType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('descricao', TextType::class, array(
            'label' => 'Descrição'
        ));
        
        $builder->add('dtEntrada', DateType::class, array(
            'widget' => 'single_text',
            'format' => 'dd/MM/yyyy',
            'label' => 'Dt Entrada',
            'attr' => array(
                'class' => 'datepicker'
            )
        ));
        
        $builder->add('concreta', ChoiceType::class, array(
            'choices'  => array(
                'Sim' => true,
                'Não' => false,
            ),
        ));
        
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Carteira::class
        ));
    }
}