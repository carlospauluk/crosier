<?php
namespace App\Form;

use App\Form\DataTransformer\EntityIdTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class EntityIdAutoCompleteType extends AbstractType
{
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $transformer = new EntityIdTransformer();
        $transformer->setRepo($options['repo']);
        $builder->getModelTransformers();
        $builder->addModelTransformer($transformer);
    }
    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefined(array('repo'));
        $resolver->setDefaults(array(
            'invalid_message' => 'The selected entity does not exist'
        ));
    }
    
    public function getParent()
    {
        return EntityType::class;
    }
}