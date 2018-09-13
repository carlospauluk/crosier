<?php

namespace App\Form\Financeiro;

use App\Entity\Financeiro\RegistroConferencia;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegistroConferenciaType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add('descricao', TextType::class, array(
            'label' => 'Descrição'
        ));

        $builder->add('dtRegistro', DateType::class, array(
            'label' => 'Dt Registro',
            'widget' => 'single_text',
            'format' => 'dd/MM/yyyy',
            'attr' => array('class' => 'crsr-date')
        ));

        $repoCarteira = $this->doctrine->getRepository(Carteira::class);
        $carteiras = $repoCarteira->findAll();
        $builder->add('carteira', EntityType::class, array(
            'class' => Carteira::class,
            'choices' => $carteiras,
            'choice_label' => function (Carteira $carteira) {
                return $carteira->getCodigo() . " - " . $carteira->getDescricao();
            }
        ));

        $builder->add('valor', MoneyType::class, array(
            'label' => 'Valor',
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
            'data_class' => RegistroConferencia::class
        ));
    }
}