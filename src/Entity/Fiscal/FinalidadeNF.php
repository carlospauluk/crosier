<?php

namespace App\Entity\Fiscal;

class FinalidadeNF {
    
    const NORMAL = array(
        'codigo' => 1,
        'label' => 'NF-e normal'
    );
    
    const COMPLEMENTAR = array(
        'codigo' => 2,
        'label' => 'NF-e complementar'
    );
    
    const AJUSTE = array(
        'codigo' => 3,
        'label' => 'NF-e de ajuste'
    );
    
    const DEVOLUCAO = array(
        'codigo' => 4,
        'label' => 'Devolução de mercadoria'
    );
    
}