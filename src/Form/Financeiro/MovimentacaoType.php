<?php
namespace App\Form\Financeiro;

use App\Entity\Financeiro\Movimentacao;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\Financeiro\Carteira;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Financeiro\Modo;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class MovimentacaoType extends AbstractType
{

    private $doctrine;

    public function __construct(RegistryInterface $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $repoCarteira = $this->doctrine->getRepository(Carteira::class);
        $carteiras = $repoCarteira->findAll();
        
        $builder->add('carteira', EntityType::class, array(
            'class' => Carteira::class,
            'choices' => $carteiras,
            'choice_label' => function (Carteira $carteira) {
                return $carteira->getCodigo() . " - " . $carteira->getDescricao();
            }
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
        
        $builder->add('categoria_ac', TextType::class, array(
            'label' => 'Categoria',
            'mapped' => false
        ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Movimentacao::class
        ));
    }
}