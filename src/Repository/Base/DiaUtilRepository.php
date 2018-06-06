<?php
namespace App\Repository\Base;

use App\Entity\Base\DiaUtil;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;

/**
 * Repository para a entidade DiaUtil.
 *
 * @author Carlos Eduardo Pauluk
 *        
 */
class DiaUtilRepository extends ServiceEntityRepository
{

    private $logger;

    public function __construct(ManagerRegistry $registry, LoggerInterface $logger)
    {
        parent::__construct($registry, DiaUtil::class);
        $this->logger = $logger;
    }

    public function findDiasUteisBy(\DateTime $ini, \DateTime $fim, $comercial = null, $financeiro = null)
    {
        $params = array();
        
        $dql = "SELECT d FROM App\Entity\Base\DiaUtil d WHERE d.dia BETWEEN :ini AND :fim ";
        
        $params['ini'] = $ini;
        $params['fim'] = $fim;
        
        if ($comercial !== null) {
            $dql .= " AND d.comercial = :comercial";
            $params['comercial'] = $comercial ? true : false;
        }
        if ($financeiro !== null) {
            $dql .= " AND d.financeiro = :financeiro";
            $params['financeiro'] = $financeiro ? true : false;
        }
        $dql .= " ORDER BY d.dia";
        
        $em = $this->getEntityManager();
        
        $query = $em->createQuery($dql);
        $query->setParameters($params);
        
        // qry.setParameter("ini", CalendarUtil.zeroDate(ini));
        // qry.setParameter("fim", CalendarUtil.to235959(fim));
        $results = $query->getResult();
        
        return $results;
    }

    public function findDiasUteisByMesAno(\DateTime $mesAno)
    {
        $ini = $mesAno->modify('first day of this month');
        $fim = clone $mesAno;
        $fim = $fim->modify('last day of this month');
        return $this->findDiasUteisBy($ini, $fim);
    }

    public function findDiasUteisFinanceirosBy(\DateTime $ini, \DateTime $fim)
    {
        return $this->findDiasUteisBy($ini, $fim, null, true);
    }

    public function findDiasUteisFinanceirosByMesAno(\DateTime $mesAno)
    {
        $ini = $mesAno->modify('first day of this month');
        $fim = clone $mesAno;
        $fim = $fim->modify('last day of this month');
        return $this->findDiasUteisFinanceirosBy($ini, $fim);
    }

    public function findDiasUteisComerciaisBy(\DateTime $ini, \DateTime $fim)
    {
        return $this->findDiasUteisBy($ini, $fim, true, null);
    }

    public function findDiasUteisComerciaisByMesAno(\DateTime $mesAno)
    {
        $ini = $mesAno->modify('first day of this month');
        $fim = clone $mesAno;
        $fim = $fim->modify('last day of this month');
        return $this->findDiasUteisComerciaisBy($ini, $fim);
    }

    public function doFindBy(\DateTime $dia): ?DiaUtil
    {
        $dql = "SELECT d FROM DiaUtil d WHERE d.dia = :dia";
        
        $em = $this->getEntityManager();
        $query = $em->createQuery($dql);
        $query->setParameters(array(
            $dia
        ));
        
        $results = $query->getResult();
        
        return $results;
    }

    /**
     * Retorna o próximo dia útil financeiro (incluindo o dia passado).
     */
    public function findProximoDiaUtilFinanceiro(\DateTime $dia): ?DiaUtil
    {
        $fim = clone $dia;
        $fim->add(new \DateInterval('P20D'));
        
        $lista = $this->findDiasUteisFinanceirosBy($dia, $fim);
        
        return isset($lista[0]) ? $lista[0] : null;
    }

    /**
     * Retorna o dia útil financeiro anterior ao dia passado (incluindo o dia passado).
     */
    public function findAnteriorDiaUtilFinanceiro(\DateTime $dia): ?DiaUtil
    {
        $ini = clone $dia;
        $ini->sub(new \DateInterval('P20D'));
        
        $lista = $this->findDiasUteisFinanceirosBy($ini, $dia);
        
        return isset($lista[count($lista) - 1]) ? $lista[count($lista) - 1] : null;
    }

    /**
     * Retorna o próximo dia útil comercial (incluindo o dia passado).
     */
    public function findProximoDiaUtilComercial(\DateTime $dia): ?DiaUtil
    {
        $fim = clone $dia;
        $fim->add(new \DateInterval('P20D'));
        
        $lista = $this->findDiasUteisComerciaisBy($dia, $fim);
        
        return isset($lista[0]) ? $lista[0] : null;
    }

    /**
     * Retorna o dia útil comercial anterior ao dia passado (incluindo o dia passado).
     */
    public function findAnteriorDiaUtilComercial(\DateTime $dia): ?DiaUtil
    {
        $ini = clone $dia;
        $ini->sub(new \DateInterval('P20D'));
        
        $lista = $this->findDiasUteisComerciais($ini, $dia);
        
        return isset($lista[count($lista) - 1]) ? $lista[count($lista) - 1] : null;
    }

    /**
     * Encontra o enésimo dia útil financeiro do mês.
     *
     * @param \DateTime $mesAno
     * @param int $ordinal
     * @return DiaUtil|NULL
     */
    public function findEnesimoDiaUtilFinanceiroNoMesAno(\DateTime $mesAno, $ordinal): ?DiaUtil
    {
        $diasUteisNoMesAno = $this->findDiasUteisFinanceiroBy($mesAno);
        return isset($diasUteisNoMesAno[$ordinal]) ? $diasUteisNoMesAno[$ordinal] : null;
    }
}