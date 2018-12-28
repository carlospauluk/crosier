<?php

namespace App\Repository\Financeiro;

use App\Entity\Financeiro\ImportExtratoCabec;
use App\Repository\FilterRepository;

/**
 * Repository para a entidade ImportExtratoCabec.
 *
 * @author Carlos Eduardo Pauluk
 *
 */
class ImportExtratoCabecRepository extends FilterRepository
{

    public function getEntityClass()
    {
        return ImportExtratoCabec::class;
    }

    
}
