<?php

namespace App\Repository\Producao;

use App\Entity\Producao\Confeccao;
use App\Entity\Producao\ConfeccaoItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Repository para a entidade ConfeccaoItem.
 *
 * @author Carlos Eduardo Pauluk
 *
 */
class ConfeccaoItemRepository extends ServiceEntityRepository
{

    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ConfeccaoItem::class);
    }

    public function findAllByConfeccao(Confeccao $confeccao)
    {
        $ql = "SELECT ci FROM App\Entity\Producao\ConfeccaoItem ci JOIN ci.insumo i JOIN ci.confeccao c WHERE c.id = :confeccao_id ORDER BY i.descricao";
        $query = $this->getEntityManager()->createQuery($ql);
        $query->setParameters(array(
            'confeccao_id' => $confeccao->getId()
        ));

        $results = $query->getResult();

        return $results;
    }

    public function findGradeMontada(ConfeccaoItem $ci)
    {
        if (!$ci->getConfeccao()->getGrade())
            return;

        // grade[ordem] = qtde

        $mGrade = array();

        foreach ($ci->getConfeccao()->getGrade()->getTamanhos() as $gt) {
            $qtde = null;
            foreach ($ci->getQtdesGrade() as $qtdeGrade) {
                if ($gt->getId() == $qtdeGrade->getGradeTamanho()->getId()) {
                    $qtde = $qtdeGrade->getQtde();
                    break;
                }
            }
            $mGrade[$gt->getOrdem()] = $qtde;
        }

        return $mGrade;
    }

    public function deleteAllQtdes(ConfeccaoItem $ci)
    {
        $ql = "DELETE FROM App\Entity\Producao\ConfeccaoItemQtde ciq WHERE ciq.confeccaoItem = :confeccaoItem";
        $query = $this->getEntityManager()->createQuery($ql);
        $query->setParameters(array(
            'confeccaoItem' => $ci
        ));
        $query->execute();
    }
}
