<?php
namespace App\Form\Producao;

use App\Entity\Producao\Confeccao;
use App\Entity\Producao\ConfeccaoItem;
use App\Form\DataTransformer\EntityIdTransformer;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class ConfeccaoItemType extends AbstractType
{

    private $doctrine;

    public function __construct(RegistryInterface $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('confeccao', EntityType::class, array(
            'label' => 'Confecção',
            'class' => Confeccao::class,
            'choice_label' => 'descricao',
            'disabled' => true
        ));
        
//         $repo = $this->doctrine->getRepository(ConfeccaoItem::class);
//         $builder->get('confeccao')->addModelTransformer(new EntityIdTransformer($repo));
        
        // $builder->add('confeccao:insumo:descricao', TextType::class, array(
        // 'label' => 'Insumo'
        // ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => ConfeccaoItem::class
        ));
    }
}