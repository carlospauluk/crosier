<?php

namespace App\Form;

use Symfony\Component\Form\ChoiceList\ArrayChoiceList;
use Symfony\Component\Form\ChoiceList\Loader\ChoiceLoaderInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormBuilder;
use App\Entity\Base\Pessoa;

class ChoiceLoader implements ChoiceLoaderInterface
{
    // Currently selected choices
    protected $selected = [ ];
    
    private $builder;
    
    private $repo;
    
    /**
     * Constructor
     */
    public function __construct($builder, $repo)
    {
        if (is_object($builder) && ($builder instanceof FormBuilderInterface))
        {
            // Let the form builder notify us about initial/submitted choices

            $builder->addEventListener
            (
                FormEvents::POST_SET_DATA, 
                [ $this, 'onFormPostSetData' ]
            );

            $builder->addEventListener
            (
                FormEvents::POST_SUBMIT, 
                [ $this, 'onFormPostSetData' ]
            );
            
            $this->builder = $builder;
            
            $this->repo = $repo;
        }
    }
    
    public function getBuilder(): FormBuilder {
    	return $this->builder;
    }
    
    
    /**
     * Form submit event callback
     * Here we get notified about the submitted choices.
     * Remember them so we can add them in loadChoiceList().
     */
    public function onFormPostSetData(FormEvent $event)
    {
        $this->selected = [ ];
        
        $entityId = $event->getData();
//         $event->getForm()->getConfig()->get
        if (! is_object($entityId))
        {
            return;
        }
        
        $this->selected = $entityId->getPessoa();
    }

    /**
     * Choices to be displayed in the SELECT element.
     * It's okay to not return all available choices, but the
     * selected/submitted choices (model values) must be
     * included.
     * Required by ChoiceLoaderInterface.
     */
    public function loadChoiceList($value = null)
    {
        // Get first n choices
        
//         $choices = $this->getChoicesList(false);
        $data = $this->getBuilder()->getData();
        
        $choices = array($data->getPessoa());
        
        
        return new ArrayChoiceList($choices);
    }

    /**
     * Validate submitted choices, and turn them from strings
     * (HTML option values) into other datatypes if needed
     * (not needed here since our choices are strings).
     * We're also using this place for creating new choices
     * from new values typed into the autocomplete field.
     * Required by ChoiceLoaderInterface.
     */
    public function loadChoicesForValues(array $values, $value = null)
    {
        $result = [ ];
        
        
        
        
        
        foreach ($values as $id)
        {
        	$pessoa = $this->repo->find($id);
                $result[ ] = $pessoa;
        }

        return $result;
    }

    /**
     * Turn choices from other datatypes into strings (HTML option
     * values) if needed - we can simply return the choices as 
     * they're strings already.
     * Required by ChoiceLoaderInterface.
     */
    public function loadValuesForChoices(array $choices, $value = null)
    {
        $result = [ ];
        
        foreach ($choices as $entityId)
        {
//             if ($this->choiceExists($id))
//             {
                $result[ ] = $entityId->getId();
//             }
        }

        return $result;
    }
    
    /**
     * Get first n choices
     */
    public function getChoicesList($filter)
    {
//         // Init our dummy list - not needed if you're
//         // working with a proper database
//         $this->initChoices();
//         ksort($_SESSION[ 'tag_choices' ]);
        
//         // Get choices list from the session; you'll use
//         // something like SQL here instead
        
//         $result = [ ];
//         $cnt = 0;
//         $limit = 10;
//         $filter = mb_strtolower($filter);
//         $filter_len = mb_strlen($filter);
        
//         foreach ($_SESSION[ 'tag_choices' ] as $label => $id)
//         {
//             if ($filter_len > 0)
//             {
//                 if (mb_substr(mb_strtolower($label), 0, $filter_len) !== $filter)
//                 {
//                     continue;
//                 }
//             }
            
//             $result[ $label ] = $id;
            
//             if (++$cnt >= $limit)
//             {
//                 break;
//             }
//         }
        
//         return $result;
return;
    }
    
    /**
     * Validate whether a choice exists
     */
    protected function choiceExists($id)
    {
//         // Init our dummy list - not needed if you're
//         // working with a proper database
//         $this->initChoices();

//         // Check whether this choice exists in the session; 
//         // you'll use something like SQL here instead

//         $label = array_search($id, $_SESSION[ 'tag_choices' ], true);

//         return 
//         (
//             $label === false 
//             ? false 
//             : true
//         );
    }

    /**
     * Get choice label
     */
    protected function getChoiceLabel($id)
    {
//         // Init our dummy list - not needed if you're
//         // working with a proper database
//         $this->initChoices();

//         // Get choice from the session; 
//         // you'll use something like SQL here instead

//         $label = array_search($id, $_SESSION[ 'tag_choices' ], true);

//         return 
//         (
//             $label === false 
//             ? false 
//             : $label
//         );
    }

    /**
     * Create a new choice
     */
    protected function createChoice($label)
    {
//         // Init our dummy list - not needed if you're
//         // working with a proper database
//         $this->initChoices();

//         // Add choice to the session; 
//         // you'll use something like SQL here instead
        
//         $id = sprintf
//         (
//             'choice%s (%s)', 
//             count($_SESSION[ 'tag_choices' ]), 
//             $label
//         );
        
//         $_SESSION[ 'tag_choices' ][ $label ] = $id;
        
//         return $id;
    }
    
    /**
     * Initialize a list of dummy choices
     */
    protected function initChoices()
    {
//         if (isset($_SESSION[ 'tag_choices' ]))
//         {
//             return;
//         }

//         $_SESSION[ 'tag_choices' ] = [ ];
        
//         for ($code = 65; $code <= 90; $code++)
//         {
//             $id = chr($code);
//             $label = $id . ' tag';
            
//             $_SESSION[ 'tag_choices' ][ $label ] = $id;
//         }
    }
}