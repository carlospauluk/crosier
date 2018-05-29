<?php
namespace App\Form;

use App\Form\DataTransformer\EntityIdTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EntityIdAutoCompleteType extends TextType
{
    private $transformer;
    
    public function __construct(EntityIdTransformer $transformer)
    {
        $this->transformer = $transformer;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer($this->transformer);
    }
    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'invalid_message' => 'The selected entity does not exist',
        ));
    }
    
    public function getParent()
    {
        return TextType::class;
    }
}