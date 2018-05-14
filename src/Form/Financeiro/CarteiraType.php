<?php
namespace App\Form\Financeiro;

use App\Entity\Financeiro\Carteira;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CarteiraType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('descricao', TextType::class, array(
            'label' => 'Descrição'
        ))
            ->add('dtConsolidado', DateType::class, array(
            'widget' => 'single_text',
            'label' => 'Dt Entrada'
        ))
            ->add('save', SubmitType::class, array(
            'label' => 'Salvar'
        ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Carteira::class
        ));
    }
}