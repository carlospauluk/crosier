<?php
namespace App\Entity\Producao;

use App\Entity\Base\EntityId;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 *
 * @ORM\Entity(repositoryClass="App\Repository\Producao\ConfeccaoItemRepository")
 * @ORM\Table(name="prod_confeccao_item")
 */
class ConfeccaoItem extends EntityId
{
    /**
     *
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="bigint")
     */
    private $id;
    
    /**
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Producao\Confeccao")
     * @ORM\JoinColumn(name="confeccao_id", nullable=false)
     *
     * @var $confeccao Confeccao
     */
    private $confeccao;
    
    /**
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Producao\Insumo")
     * @ORM\JoinColumn(name="insumo_id", nullable=false)
     *
     * @var $insumo Insumo
     */
    private $insumo;
    
    public function __construct()
    {
        ORM\Annotation::class;
        Assert\All::class;
    }
    
    
}