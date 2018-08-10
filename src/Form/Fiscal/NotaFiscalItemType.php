<?php
namespace App\Form\Fiscal;

use App\Entity\Fiscal\NotaFiscalItem;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

/**
 *
 * @author Carlos Eduardo Pauluk
 *        
 */
class NotaFiscalItemType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        
        $builder->add('codigo', TextType::class, array(
            'label' => 'Código',
            'required' => true
        ));
        $builder->add('descricao', TextType::class, array(
            'label' => 'Descrição',
            'required' => true
        ));
        $builder->add('cfop', TextType::class, array(
            'label' => 'CFOP',
            'required' => true
        ));
        $builder->add('icmsAliquota', NumberType::class, array(
            'label' => 'ICMS Aliq',
            'grouping' => 'true',
            'required' => false,
            'attr' => array(
                'class' => 'crsr-dec2'
            )
        ));
        $builder->add('icms_valor', NumberType::class, array(
            'label' => 'ICMS Valor',
            'grouping' => 'true',
            'required' => false,
            'attr' => array(
                'class' => 'crsr-dec2'
            )
        ));
        $builder->add('icms_valor_bc', NumberType::class, array(
            'label' => 'ICMS BC',
            'grouping' => 'true',
            'required' => false,
            'attr' => array(
                'class' => 'crsr-dec2'
            )
        ));
        $builder->add('ncm', TextType::class, array(
            'label' => 'NCM',
            'required' => true
        ));
        $builder->add('qtde', NumberType::class, array(
            'label' => 'Qtde',
            'grouping' => 'true',
            'scale' => 3,
            'attr' => array(
                'class' => 'crsr-dec3'
            ),
            'required' => true
        ));
        $builder->add('unidade', TextType::class, array(
            'label' => 'Unidade',
            'required' => true
        ));
        $builder->add('valor_unit', MoneyType::class, array(
            'label' => 'Valor Unit',
            'currency' => 'BRL',
            'grouping' => 'true',
            'required' => true,
            'attr' => array(
                'class' => 'crsr-money'
            )
        ));
        $builder->add('sub_total', MoneyType::class, array(
            'label' => 'Subtotal',
            'currency' => 'BRL',
            'grouping' => 'true',
            'required' => true,
            'attr' => array(
                'class' => 'crsr-money'
            )
        ));
        $builder->add('valor_desconto', MoneyType::class, array(
            'label' => 'Valor Desconto',
            'currency' => 'BRL',
            'grouping' => 'true',
            'required' => false,
            'attr' => array(
                'class' => 'crsr-money'
            )
        ));
        $builder->add('valor_total', MoneyType::class, array(
            'label' => 'Valor Total',
            'currency' => 'BRL',
            'grouping' => 'true',
            'required' => true,
            'attr' => array(
                'class' => 'crsr-money'
            )
        ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => NotaFiscalItem::class
        ));
    }
}