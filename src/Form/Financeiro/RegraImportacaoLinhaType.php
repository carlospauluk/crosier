<?php

namespace App\Form\Financeiro;

use App\Entity\Financeiro\CentroCusto;
use App\Entity\Financeiro\RegraImportacaoLinha;
use App\Entity\Financeiro\TipoLancto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegraImportacaoLinhaType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add('regexJava', TextType::class, array(
            'label' => 'Regex Java'
        ));

        $builder->add('tipoLancto', TextType::class, array(
            'choices' => TipoLancto::getChoices()
        ));

        $builder->add('status', ChoiceType::class, array(
            'choices' => Status::getChoices()
        ));

        $repoCarteira = $this->doctrine->getRepository(Carteira::class);
        $carteiras = $repoCarteira->findAll();

        $builder->add('carteira', EntityType::class, array(
            'label' => 'Carteira',
            'class' => Carteira::class,
            'choices' => $carteiras,
            'choice_label' => function (Carteira $carteira) {
                return $carteira->getCodigo() . " - " . $carteira->getDescricao();
            }
        ));

        $builder->add('carteiraDestino', EntityType::class, array(
            'label' => 'Carteira Destino',
            'class' => Carteira::class,
            'choices' => $carteiras,
            'choice_label' => function (Carteira $carteira) {
                return $carteira->getCodigo() . " - " . $carteira->getDescricao();
            }
        ));

        $repoCentroCusto = $this->doctrine->getRepository(CentroCusto::class);
        $centrosCusto = $repoCentroCusto->findAll();
        $builder->add('centroCusto', EntityType::class, array(
            'class' => CentroCusto::class,
            'choices' => $centrosCusto,
            'choice_label' => function (CentroCusto $centroCusto) {
                return $centroCusto->getDescricao();
            }
        ));

        $repoModo = $this->doctrine->getRepository(Modo::class);
        $modos = $repoModo->findAll();
        $builder->add('modo', EntityType::class, array(
            'class' => Modo::class,
            'choices' => $modos,
            'choice_label' => function (Modo $modo) {
                return $modo->getCodigo() . " - " . $modo->getDescricao();
            }
        ));

        $builder->add('padraoDescricao', TextType::class, array(
            'label' => 'Padrão da Descrição'
        ));

        $repoCategoria = $this->doctrine->getRepository(Categoria::class);
        $categorias = $repoCategoria->findAll();
        $builder->add('categoria', EntityType::class, array(
            'class' => Categoria::class,
            'choices' => $categorias,
            'choice_label' => 'descricaoMontada'
        ));

        $builder->add('sinalValor', ChoiceType::class, array(
            'choices' => array(
                'Ambos' => 0,
                'Positivo' => 1,
                'Negativo' => -1
            )
        ));

        $builder->add('chequeBanco', EntityType::class, array(
            'required' => false,
            'label' => 'Cheque - Banco',
            'class' => Banco::class,
            'choices' => $this->doctrine->getRepository(Banco::class)
                ->findAll(),
            'choice_label' => function (Banco $banco) {
                return sprintf("%03d", $banco->getCodigoBanco()) . " - " . $banco->getNome();
            }
        ));

        $builder->add('chequeAgencia', TextType::class, array(
            'label' => 'Cheque - Agência'
        ));

        $builder->add('chequeConta', TextType::class, array(
            'label' => 'Cheque - Conta'
        ));

        $builder->add('chequeNumCheque', TextType::class, array(
            'label' => 'Cheque - Número'
        ));


    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => RegraImportacaoLinha::class
        ));
    }
}