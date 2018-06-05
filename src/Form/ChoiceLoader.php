<?php
namespace App\Form;

use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\ChoiceList\ArrayChoiceList;
use Symfony\Component\Form\ChoiceList\Loader\ChoiceLoaderInterface;

/**
 * ChoiceLoader para trabalhar com autocomplete no componente select2.js.
 *
 *
 * @author Carlos Eduardo Pauluk
 *        
 */
class ChoiceLoader implements ChoiceLoaderInterface
{

    // protected $selected = [];
    private $builder;

    private $repo;

    private $getObjFunc;

    private $choiceGetLabelFunc;

    /**
     *
     * @param FormBuilderInterface $builder
     * @param \Doctrine\Common\Persistence\ObjectRepository $repo
     * @param string $getObjFunc
     */
    public function __construct($builder, $repo, $getObjFunc, $choiceGetLabelFunc)
    {
        if (is_object($builder) && ($builder instanceof FormBuilderInterface)) {
            
            // $builder->addEventListener(FormEvents::POST_SET_DATA, [
            // $this,
            // 'onFormPostSetData'
            // ]);
            
            // $builder->addEventListener(FormEvents::POST_SUBMIT, [
            // $this,
            // 'onFormPostSetData'
            // ]);
            
            $this->builder = $builder;
            $this->repo = $repo;
            $this->getObjFunc = $getObjFunc;
            $this->choiceGetLabelFunc = $choiceGetLabelFunc;
        }
    }

    public function getBuilder(): FormBuilder
    {
        return $this->builder;
    }

    // /**
    // * Evento chamado no POST_SET_DATA.
    // *
    // * @param FormEvent $event
    // */
    // public function onFormPostSetData(FormEvent $event)
    // {
    // $this->selected = [];
    
    // $entityId = $event->getData();
    // if (! is_object($entityId)) {
    // return;
    // }
    
    // // Retorna o objeto
    // $this->selected = call_user_func(array(
    // $entityId,
    // $this->getObjFunc
    // ));
    // }
    
    /**
     * Aqui irá retornar apenas o objeto já setado, para que o select2 possa deixa-lo selecionado.
     *
     * {@inheritdoc}
     * @see \Symfony\Component\Form\ChoiceList\Loader\ChoiceLoaderInterface::loadChoiceList()
     */
    public function loadChoiceList($value = null)
    {
        //
        $data = $this->getBuilder()->getData();
        
        $choices = array();
        
        if ($data) {
            
            $entity = call_user_func(array(
                $data,
                $this->getObjFunc
            ));
            
            if (is_object($entity)) {
                $choiceGetLabelFunc = $this->choiceGetLabelFunc;
                $label = $choiceGetLabelFunc($entity);
                $choices[$label] = $entity->getId();
            }
        }
        return new ArrayChoiceList($choices);
    }

    /**
     * Encontra a entidade pelo seu id.
     *
     * {@inheritdoc}
     * @see \Symfony\Component\Form\ChoiceList\Loader\ChoiceLoaderInterface::loadChoicesForValues()
     */
    public function loadChoicesForValues(array $values, $value = null)
    {
        $result = [];
        
        foreach ($values as $id) {
            $entityId = $this->repo->find($id);
            $result[] = $entityId;
        }
        
        return $result;
    }

    /**
     * Retorna o array com os ids dos objetos.
     *
     * {@inheritdoc}
     * @see \Symfony\Component\Form\ChoiceList\Loader\ChoiceLoaderInterface::loadValuesForChoices()
     */
    public function loadValuesForChoices(array $choices, $value = null)
    {
        $result = [];
        if ($choices and count($choices) > 0) {
            
            foreach ($choices as $entityId) {
                if (is_object($entityId)) {
                    $result[] = (string) $entityId->getId();
                }
            }
        }
        return $result;
    }
}