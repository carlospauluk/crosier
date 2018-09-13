<?php

namespace App\Form\Financeiro;

use App\Entity\Financeiro\OperadoraCartao;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OperadoraCartaoType extends AbstractType
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

        $repoCarteira = $this->doctrine->getRepository(Carteira::class);
        $carteiras = $repoCarteira->findAll();
        $builder->add('carteira', EntityType::class, array(
            'class' => Carteira::class,
            'choices' => $carteiras,
            'choice_label' => function (Carteira $carteira) {
                return $carteira->getCodigo() . " - " . $carteira->getDescricao();
            }
        ));


    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => OperadoraCartao::class
        ));
    }
}