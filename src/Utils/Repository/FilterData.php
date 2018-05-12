<?php


namespace App\Utils\Repository;

class FilterData {
    
    public $field;
    
    public $compar;
    
    public $val;
    
    public function __construct($field, $compar, $val) {
        $this->field = $field;
        $this->compar = $compar;
        $this->val = $val;
    }
    
}