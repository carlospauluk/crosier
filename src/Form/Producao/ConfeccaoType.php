<?php
namespace App\Form\Producao;

use App\Entity\Estoque\Grade;
use App\Entity\Producao\Confeccao;
use App\Entity\Producao\TipoArtigo;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;

class ConfeccaoType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('id', IntegerType::class, array(
            'label' => 'Código',
            'disabled' => true
        ));
        
        $builder->add('descricao', TextType::class, array(
            'label' => 'Descrição'
        ));
        
        $builder->add('oculta', ChoiceType::class, array(
            'choices' => array(
                'Sim' => true,
                'Não' => false
            )
        ));
        
        $builder->add('bloqueada', ChoiceType::class, array(
            'choices' => array(
                'Sim' => true,
                'Não' => false
            )
        ));
        
        $builder->add('tipoArtigo', EntityType::class, array(
            // looks for choices from this entity
            'class' => TipoArtigo::class,
            'query_builder' => function (EntityRepository $er) {
                return $er->createQueryBuilder('ta')
                    ->orderBy('ta.descricao', 'ASC');
            },
            'choice_label' => 'descricao'
        ));
        
        $builder->add('modo_calculo', ChoiceType::class, array(
            'choices' => array(
                'MODO_01' => 'MODO_01',
                'MODO_02' => 'MODO_02',
                'MODO_03' => 'MODO_03'
            )
        ));
        
        $builder->add('grade', EntityType::class, array(
            // looks for choices from this entity
            'class' => Grade::class,
            'choice_label' => function (Grade $grade) {
                return $grade->getCodigo() . " (" . $grade->getObs() . ")";
            }
        ));
        
        $builder->add('prazoPadrao', IntegerType::class, array(
            'label' => 'Prazo'
        ));
        
        $builder->add('margem_padrao', NumberType::class, array(
            'label' => 'Margem',
            'grouping' => 'true',
            'scale' => 2,
            'attr' => array(
                'class' => 'crsr-dec2'
            )
        ));
        
        $builder->add('custo_operacional_padrao', NumberType::class, array(
            'label' => 'Custo Operacional',
            'grouping' => 'true',
            'scale' => 3,
            'attr' => array(
                'class' => 'crsr-dec2'
            )
        ));
        
        $builder->add('custo_financeiro_padrao', NumberType::class, array(
            'label' => 'Custo Financeiro',
            'grouping' => 'true',
            'scale' => 3,
            'attr' => array(
                'class' => 'crsr-dec2'
            )
        ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Confeccao::class
        ));
    }
}