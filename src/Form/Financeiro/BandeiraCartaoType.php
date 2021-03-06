<?php

namespace App\Form\Financeiro;

use App\Entity\Financeiro\BandeiraCartao;
use App\Entity\Financeiro\Modo;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BandeiraCartaoType extends AbstractType
{

    private $doctrine;

    public function __construct(RegistryInterface $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {


        $builder->add('descricao', TextType::class, array(
            'label' => 'Descrição'
        ));

        $builder->add('labels', TextType::class, array(
            'label' => 'Labels'
        ));

        $repoModo = $this->doctrine->getRepository(Modo::class);
        $modos = $repoModo->findAll();

        $builder->add('modo', EntityType::class, array(
            'class' => Modo::class,
            'choices' => $modos,
            'choice_label' => function (Modo $modo) {
                return $modo->getCodigo() . " - " . $modo->getDescricao();
            }
        ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => BandeiraCartao::class
        ));
    }
}