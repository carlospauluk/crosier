<?php
namespace App\Repository\Base;

use App\Entity\Base\DiaUtil;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Repository para a entidade Config.
 *
 * @author Carlos Eduardo Pauluk
 *        
 */
class ConfigRepository extends ServiceEntityRepository
{

    private $logger;

    public function __construct(RegistryInterface $registry, LoggerInterface $logger)
    {
        parent::__construct($registry, DiaUtil::class);
        $this->logger = $logger;
    }

    public function findByChave($chave) {
        
        // TODO: parametrizar o estabelecimento conforme o login
        $ql = "SELECT c FROM App\Entity\Base\Config c WHERE c.chave = :chave AND c.estabelecimento = 1";
        $query = $this->getEntityManager()->createQuery($ql);
        $query->setParameters(array(
            'chave' => $chave
        ));
        
        $results = $query->getResult();
        
        if (count($results) > 1) {
            throw new \Exception('Mais de um Config encontrado para [' . $chave . ']');
        }
        
        return count($results) == 1 ? $results[0] : null;
    }
}
