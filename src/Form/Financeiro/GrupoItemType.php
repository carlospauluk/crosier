<?php

namespace App\Form\Financeiro;

use App\Entity\Financeiro\Carteira;
use App\Entity\Financeiro\GrupoItem;
use App\Utils\Repository\WhereBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GrupoItemType extends AbstractType
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

        $builder->add('dtVencto', DateType::class, array(
            'widget' => 'single_text',
            'format' => 'dd/MM/yyyy',
            'label' => 'Dt Vencto',
            'attr' => array(
                'class' => 'crsr-date'
            )
        ));

        $builder->add('valorInformado', MoneyType::class, array(
            'label' => 'Valor',
            'currency' => 'BRL',
            'grouping' => 'true',
            'required' => false,
            'attr' => array(
                'class' => 'crsr-money'
            )
        ));

        $builder->add('carteiraPagante', EntityType::class, array(
            'label' => 'Carteira',
            'class' => Carteira::class,
            'choices' => $this->doctrine->getRepository(Carteira::class)->findAll(WhereBuilder::buildOrderBy('codigo')),
            'choice_label' => function (Carteira $carteira) {
                return $carteira->getDescricaoMontada();
            }
        ));

        $builder->add('fechado', ChoiceType::class, array(
            'choices' => array(
                'Sim' => true,
                'Não' => false
            )
        ));

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => GrupoItem::class
        ));
    }
}