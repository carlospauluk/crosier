<?php

namespace App\Repository\Fiscal;

use App\Entity\Config\Config;
use App\Entity\Fiscal\NotaFiscal;
use App\Repository\FilterRepository;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\QueryBuilder;

/**
 * Repository para a entidade NotaFiscal.
 *
 * @author Carlos Eduardo Pauluk
 *
 */
class NotaFiscalRepository extends FilterRepository
{


    public function getEntityClass()
    {
        return NotaFiscal::class;
    }


    public function findProxNumFiscal($producao, $serie, $tipoNotaFiscal)
    {
        try {

            $ambiente = $producao ? 'PROD' : 'HOM';

            $this->getEntityManager()->beginTransaction();

            $chave = $producao ? "bonsucesso.fiscal.prod" : "bonsucesso.fiscal.hom";
            $chave .= ".sequencia-" . strtolower($tipoNotaFiscal);
            $chave .= "." . $serie;

            $config = $this->getEntityManager()
                ->getRepository(Config::class)
                ->findByChave($chave);

            // FOR UPDATE para garantir que ninguém vai alterar este valor antes de terminar esta transação
            $sql = "SELECT * FROM cfg_config WHERE chave LIKE ? FOR UPDATE";
            $rsm = new ResultSetMapping();
            $rsm->addEntityResult('App\Entity\Config\Config', 'c');
            $rsm->addFieldResult('c', 'id', 'id');
            $rsm->addFieldResult('c', 'valor', 'valor');
            $query = $this->getEntityManager()->createNativeQuery($sql, $rsm);
            $query->setParameter(1, $chave);
            $rs = $query->getResult();

            if (!$rs or !$rs[0]) {
                throw new \Exception("Erro ao buscar a chave [" . $chave . "]");
            }
            $prox = $rs[0]->getValor();

            // Verificação se por algum motivo a numeração na fis_nf já não está pra frente...
            $ultimoNaBase = null;
            $sqlUltimo = "SELECT nf FROM App\Entity\Fiscal\NotaFiscal nf WHERE nf.ambiente = :ambiente AND nf.serie = :serie AND nf.tipoNotaFiscal = :tipoNotaFiscal ORDER BY nf.numero DESC";
            $query = $this->getEntityManager()->createQuery($sqlUltimo);
            $query->setParameters(array(
                'ambiente' => $ambiente,
                'serie' => $serie,
                'tipoNotaFiscal' => $tipoNotaFiscal
            ));
            $query->setMaxResults(1);
            $results = $query->getResult();
            if ($results) {
                $u = $results[0];
                $ultimoNaBase = $u->getNumero();
                if ($ultimoNaBase and $ultimoNaBase != $prox) {
                    $prox = $ultimoNaBase;
                }
            } else {
                $prox = 0;
            }
            $prox++;

            $config->setValor($prox);
            $this->getEntityManager()->persist($config);
            $this->getEntityManager()->flush();

            $this->getEntityManager()->commit();

            return $prox;
        } catch (\Exception $e) {
            $this->getEntityManager()->rollback();
            $this->getLogger()->error($e);
            $this->getLogger()->error("Erro ao pesquisar próximo número de nota fiscal para [" . $producao . "] [" . $serie . "] [" . $tipoNotaFiscal . "]");
            throw new \Exception("Erro ao pesquisar próximo número de nota fiscal para [" . $producao . "] [" . $serie . "] [" . $tipoNotaFiscal . "]");
        }
    }

    public function handleFrombyFilters(QueryBuilder &$qb)
    {
        return $qb->from($this->getEntityClass(), 'e')
            ->join('App\Entity\Base\Pessoa', 'p', 'WITH', 'e.pessoaDestinatario = p');
    }

    public function getDefaultOrders()
    {
        return array(
            ['column' => 'e.id', 'dir' => 'desc'],
            ['column' => 'e.dtEmissao', 'dir' => 'desc']
        );
    }


}
