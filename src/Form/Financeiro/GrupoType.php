<?php

namespace App\Form\Financeiro;

use App\Entity\Financeiro\Carteira;
use App\Entity\Financeiro\Categoria;
use App\Entity\Financeiro\Grupo;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GrupoType extends AbstractType
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

        $builder->add('diaVencto', IntegerType::class, array(
            'label' => 'Dia de Vencto',
            'required' => false
        ));

        $builder->add('diaInicioAprox', IntegerType::class, array(
            'label' => 'Dia de Início (aprox)',
            'required' => false
        ));

        $builder->add('ativo', ChoiceType::class, array(
            'choices' => array(
                'Sim' => true,
                'Não' => false
            )
        ));

        $repoCarteira = $this->doctrine->getRepository(Carteira::class);
        $carteiras = $repoCarteira->findAll();
        $builder->add('carteiraPagantePadrao', EntityType::class, array(
            'label' => 'Carteira Pagante Padrão',
            'class' => Carteira::class,
            'choices' => $carteiras,
            'choice_label' => function (Carteira $carteira) {
                return $carteira->getCodigo() . " - " . $carteira->getDescricao();
            }
        ));

        $repoCategoria = $this->doctrine->getRepository(Categoria::class);
        $categorias = $repoCategoria->findAll();
        $builder->add('categoriaPadrao', EntityType::class, array(
            'label' => 'Categoria Padrão',
            'class' => Categoria::class,
            'choices' => $categorias,
            'choice_label' => 'descricaoMontada'
        ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Grupo::class
        ));
    }
}