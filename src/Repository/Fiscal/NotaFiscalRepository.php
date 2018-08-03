<?php
namespace App\Repository\Fiscal;

use App\Entity\Base\Config;
use App\Entity\Fiscal\NotaFiscal;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Repository para a entidade NotaFiscal.
 *
 * @author Carlos Eduardo Pauluk
 *        
 */
class NotaFiscalRepository extends ServiceEntityRepository
{

    private $logger;
    
    public function __construct(RegistryInterface $registry, LoggerInterface $logger)
    {
        parent::__construct($registry, NotaFiscal::class);
        $this->logger = $logger;
    }
    
    public function findProxNumFiscal($producao, $serie, $tipoNotaFiscal) {
        try {
            $this->getEntityManager()->getConnection()->beginTransaction();
            
            $chave = $producao ? "bonsucesso.fiscal.prod" : "bonsucesso.fiscal.hom";
            $chave .=  ".sequencia-" . $tipoNotaFiscal;
            $chave .= "." . $serie;
            
            $config = $this->doctrine->getRepository(Config::class)->findByChave($chave);
            
            // FOR UPDATE para garantir que ninguém vai alterar este valor antes de terminar esta transação
            $sql = "SELECT valor FROM cfg_config WHERE chave = ? FOR UPDATE";
            $rsm = new ResultSetMapping();
            $rsm->addEntityResult('Config', 'c');
            $rsm->addFieldResult('c', 'valor', 'valor');
            $query = $this->getEntityManager()->createNativeQuery($sql, $rsm);
            $query->setParameter(1, $chave);
            $rs = $query->getResult();
            
            $prox = $rs[0];
            
            // Verificação se por algum motivo a numeração na fis_nf já não está pra frente...
            $ultimoNaBase = null;
            $sqlUltimo = "SELECT nf FROM NotaFiscal nf WHERE nf.ambiente = :ambiente AND nf.serie = :serie AND nf.tipo = :tipoNotaFiscal ORDER BY nf.numero DESC";
            $query = $this->getEntityManager()->createQuery($sqlUltimo);
            $query->setParameters(array(
                'ambiente' => $ambiente,
                'serie' => $serie,
                'tipo' => $tipoNotaFiscal
            ));
            $query->setMaxResults(1);
            $results = $query->getResult();
            if ($results) {
                $u = $results[0];
                $ultimoNaBase = $u->getNumero();
            }
                
            if ($ultimoNaBase AND $ultimoNaBase != $prox) {
                $prox = ultimoNaBase;
            }
            
            $prox++;
            
            $config->setValor($prox);
            $this->getEntityManager()->persist($config);
            $this->getEntityManager()->flush();
            
            $this->getEntityManager()->getConnection()->commitTransaction();
            
            return $prox;
        } catch (\Exception $e) {
            $this->getEntityManager()->getConnection()->rollbackTransaction();
            $this->logger->error($e);
            $this->logger->error("Erro ao pesquisar próximo número de nota fiscal para [" . $producao . "] [" . $serie . "] [" . $tipoNotaFiscal . "]");
            throw new \Exception("Erro ao pesquisar próximo número de nota fiscal para [" . $producao . "] [" . $serie . "] [" . $tipoNotaFiscal . "]");
        }
    }
}
