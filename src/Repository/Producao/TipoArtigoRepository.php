<?php
namespace App\Repository\Producao;

use App\Entity\Producao\TipoArtigo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use App\Entity\Producao\Instituicao;

/**
 * Repository para a entidade TipoArtigo .
 *
 * @author Carlos Eduardo Pauluk
 *        
 */
class TipoArtigoRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TipoArtigo::class);
    }

    public function findAllByInstituicao(Instituicao $instituicao)
    {
        $ql = "SELECT ta_ FROM App\Entity\Producao\TipoArtigo ta_ WHERE ta_ IN (SELECT distinct(ta) FROM App\Entity\Producao\Confeccao c JOIN c.tipoArtigo ta JOIN c.instituicao i WHERE i.id = :instituicao_id) ORDER BY ta_.descricao";
        $query = $this->getEntityManager()->createQuery($ql);
        $query->setParameters(array(
            'instituicao_id' => $instituicao->getId()
        ));
        
        $results = $query->getResult();
        
        return $results;
    }

    public function findAll($descricao = null)
    {
        $ql = "SELECT ta FROM App\Entity\Producao\TipoArtigo ta";
        if ($descricao) {
            $ql .= " WHERE ta.descricao LIKE :descricao";
        }
        $ql .= " ORDER BY TRIM(ta.descricao)";
        $query = $this->getEntityManager()->createQuery($ql);
        if ($descricao) {
            $query->setParameter("descricao", "%" . $descricao . "%");
        }
        $results = $query->getResult();
        return $results;
    }
}
