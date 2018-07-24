<?php
namespace App\Form\Producao;

use App\Entity\Estoque\Grade;
use App\Entity\Producao\Confeccao;
use App\Entity\Producao\Insumo;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use App\Entity\Estoque\UnidadeProduto;

class ConfeccaoItemType extends AbstractType
{

    private $doctrine;

    public function __construct(RegistryInterface $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('confeccao', EntityType::class, array(
            'label' => 'Confecção',
            'class' => Confeccao::class,
            'choice_label' => 'descricao',
            'disabled' => true
        ));
        
        $builder->add('insumo', EntityType::class, array(
            'label' => 'Insumo',
            'class' => Insumo::class,
            'choice_label' => 'descricao',
            'disabled' => true
        ));
        
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $data = $event->getData();
            $form = $event->getForm();
            
            if ($data) {
                // Adiciona a qtde de campos referente aos tamanhos da grade
                
                $rUnidadeProduto = $this->doctrine->getRepository(UnidadeProduto::class);
                $unidadeProduto = $rUnidadeProduto->find($data['unidade_produto_id']);
                
                $rGrade = $this->doctrine->getRepository(Grade::class);
                $grade = $rGrade->find($data['grade_id']);
                
                if ($unidadeProduto->getCasasDecimais() > 0) {
                    $classType = NumberType::class;
                    $params = array(
                        'required' => false,
                        'scale' => $unidadeProduto->getCasasDecimais(),
                        'attr' => array(
                            // Adiciona o style css pra qtde de casas decimais
                            'class' => 'crsr-dec' . $unidadeProduto->getCasasDecimais()
                        )
                    );
                } else {
                    $classType = IntegerType::class;
                    $params = array(
                        'required' => false
                    );
                }
                // Monta a qtde de campos
                foreach ($grade->getTamanhos() as $gt) {
                    $params['label'] = $gt->getTamanho();
                    $form->add('qtde_gt_' . $gt->getOrdem(), $classType, $params);
                }
            }
        });
        
       
    }
    
    // public function configureOptions(OptionsResolver $resolver)
    // {
    // $resolver->setDefaults(array(
    // 'data_class' => ConfeccaoItem::class
    // ));
    // }
}