<?php
namespace App\Repository\Producao;

use App\Entity\Producao\Confeccao;
use App\Entity\Producao\Instituicao;
use App\Entity\Producao\TipoArtigo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * Repository para a entidade Confeccao.
 *
 * @author Carlos Eduardo Pauluk
 *        
 */
class ConfeccaoRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Confeccao::class);
    }
    
    public function findAllByTipoArtigoInstituicao(Instituicao $instituicao, TipoArtigo $tipoArtigo)
    {
        $ql = "SELECT c FROM App\Entity\Producao\Confeccao c JOIN c.tipoArtigo ta JOIN c.instituicao i WHERE i.id = :instituicao_id AND ta.id = :tipo_artigo_id";
        $query = $this->getEntityManager()->createQuery($ql);
        $query->setParameters(array('instituicao_id' => $instituicao->getId(),
            'tipo_artigo_id' => $tipoArtigo->getId()
        ));
        
        $results = $query->getResult();
        
        return $results;
    }
    
}
