<?php

namespace App\Form\Financeiro;

use App\Business\Financeiro\MovimentacaoBusiness;
use App\Entity\Base\Pessoa;
use App\Entity\Financeiro\Banco;
use App\Entity\Financeiro\BandeiraCartao;
use App\Entity\Financeiro\Carteira;
use App\Entity\Financeiro\Categoria;
use App\Entity\Financeiro\CentroCusto;
use App\Entity\Financeiro\Modo;
use App\Entity\Financeiro\Movimentacao;
use App\Entity\Financeiro\OperadoraCartao;
use App\Utils\Repository\WhereBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class MovimentacaoType.
 *
 * Form para movimentações.
 *
 * @package App\Form\Financeiro
 */
class MovimentacaoType extends AbstractType
{

    private $doctrine;

    private $movimentacaoBusiness;

    public function __construct(RegistryInterface $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * @return mixed
     */
    public function getMovimentacaoBusiness(): MovimentacaoBusiness
    {
        return $this->movimentacaoBusiness;
    }

    /**
     * @required
     * @param mixed $movimentacaoBusiness
     */
    public function setMovimentacaoBusiness(MovimentacaoBusiness $movimentacaoBusiness): void
    {
        $this->movimentacaoBusiness = $movimentacaoBusiness;
    }


    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $movimentacao = $event->getData();
            $builder = $event->getForm();

            $builder->add('id', IntegerType::class, array(
                'label' => 'Id',
                'required' => false,
                'disabled' => true
            ));

            // Adiciono este por default, sabendo que será alterado no beforeSave
            $builder->add('status', HiddenType::class, array(
                'data' => 'A_PAGAR_RECEBER'
            ));

            // Já retorna os valores dentro das regras
            $builder->add('tipoLancto', ChoiceType::class, array(
                'label' => 'Tipo Lancto',
                'choices' => []
            ));

            $builder->add('carteira', EntityType::class, array(
                'label' => 'Carteira',
                'class' => Carteira::class,
                'choices' => $this->doctrine->getRepository(Carteira::class)->findAll(WhereBuilder::buildOrderBy('codigo')),
                'choice_label' => function (Carteira $carteira) {
                    return $carteira->getDescricaoMontada();
                }
            ));

            // Monta o campo de Categoria conforme as regras
            $categoriaChoices = null;
            $categoriaData = null;
            if ($movimentacao and $movimentacao->getTipoLancto() == 'TRANSF_PROPRIA') {
                $categoriaChoices = [$this->doctrine->getRepository(Categoria::class)->findOneBy(['codigo' => 299])];
                $categoriaData = $this->doctrine->getRepository(Categoria::class)->findOneBy(['codigo' => 299]);
            } else {
                $categoriaChoices = $this->doctrine->getRepository(Categoria::class)->findAll(WhereBuilder::buildOrderBy('codigoOrd'));
            }
            $categoriaParams = array(
                'label' => 'Categoria',
                'class' => Categoria::class,
                'choices' => $categoriaChoices,
                'choice_label' => 'descricaoMontadaTree'
            );
            if ($categoriaData) {
                $categoriaParams['data'] = $categoriaData;
            }
            $builder->add('categoria', EntityType::class, $categoriaParams);


            // só é obrigatório nos casos de tipoLancto = 'TRANSF_PROPRIA'
            $builder->add('carteiraDestino', EntityType::class, array(
                'label' => 'Destino',
                'class' => Carteira::class,
                'choices' => array_merge([null], $this->doctrine->getRepository(Carteira::class)->findAll(WhereBuilder::buildOrderBy('codigo'))),
                'choice_label' => function ($carteira) {
                    if ($carteira) {
                        return $carteira->getDescricaoMontada();
                    }
                },
                'required' => $movimentacao->getTipoLancto() == 'TRANSF_PROPRIA'
            ));

            $builder->add('modo', EntityType::class, array(
                'label' => 'Modo',
                'class' => Modo::class,
                'choices' => $this->doctrine->getRepository(Modo::class)->findAll(WhereBuilder::buildOrderBy('codigo')),
                'choice_label' => function (Modo $modo) {
                    return $modo->getDescricaoMontada();
                }
            ));

            $builder->add('bandeiraCartao', EntityType::class, array(
                'label' => 'Bandeira',
                'class' => BandeiraCartao::class,
                'choices' => array_merge([null], $this->doctrine->getRepository(BandeiraCartao::class)->findAll(WhereBuilder::buildOrderBy('descricao'))),
                'choice_label' => function (?BandeiraCartao $bandeiraCartao) {
                    return $bandeiraCartao ? $bandeiraCartao->getDescricao() : '';
                },
                'attr' => array(
                    'class' => 'CAMPOS_CARTAO'
                )
            ));

            $builder->add('operadoraCartao', EntityType::class, array(
                'label' => 'Operadora',
                'class' => OperadoraCartao::class,
                'choices' => array_merge([null], $this->doctrine->getRepository(OperadoraCartao::class)->findAll(WhereBuilder::buildOrderBy('descricao'))),
                'choice_label' => function (?OperadoraCartao $operadoraCartao) {
                    return $operadoraCartao ? $operadoraCartao->getDescricao() : '';
                },
                'attr' => array(
                    'class' => 'CAMPOS_CARTAO'
                )
            ));

            $builder->add('centroCusto', EntityType::class, array(
                'class' => CentroCusto::class,
                'choices' => $this->doctrine->getRepository(CentroCusto::class)->findAll(),
                'choice_label' => 'descricaoMontada'
            ));


            $builder->add('dtMoviment', DateType::class, array(
                'label' => 'Dt Moviment',
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy',
                'attr' => array(
                    'class' => 'crsr-date'
                )
            ));

            $builder->add('dtVencto', DateType::class, array(
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy',
                'label' => 'Dt Vencto',
                'attr' => array(
                    'class' => 'crsr-date'
                )
            ));

            $builder->add('dtVenctoEfetiva', DateType::class, array(
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy',
                'label' => 'Dt Vencto Efet',
                'attr' => array(
                    'class' => 'crsr-date'
                ),
                'required' => false
            ));

            $builder->add('dtPagto', DateType::class, array(
                'widget' => 'single_text',
                'required' => false,
                'format' => 'dd/MM/yyyy',
                'label' => 'Dt Pagto',
                'attr' => array(
                    'class' => 'crsr-date'
                ),
                'required' => false
            ));

            $builder->add('dtUtil', DateType::class, array(
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy',
                'label' => 'Dt Útil',
                'attr' => array(
                    'class' => 'crsr-date'
                ),
                'required' => false
            ));


            $builder->add('pessoa', EntityType::class, array(
                'label' => 'Pessoa',
                'class' => Pessoa::class,
                'choices' => [],
                'choice_label' => 'nome'
            ));

            $builder->add('descricao', TextType::class, array(
                'label' => 'Descrição'
            ));

            $builder->add('documentoBanco', EntityType::class, array(
                'required' => false,
                'label' => 'Banco do Documento',
                'class' => Banco::class,
                'choices' => $this->doctrine->getRepository(Banco::class)
                    ->findAll(),
                'choice_label' => function (Banco $banco) {
                    return sprintf("%03d", $banco->getCodigoBanco()) . " - " . $banco->getNome();
                }
            ));

            $builder->add('documentoNum', TextType::class, array(
                'label' => 'Núm Documento',
                'required' => false
            ));

            $builder->add('obs', TextareaType::class, array(
                'label' => 'Obs',
                'required' => false
            ));

            $builder->add('valor', MoneyType::class, array(
                'label' => 'Valor',
                'currency' => 'BRL',
                'grouping' => 'true',
                'required' => false,
                'attr' => array(
                    'class' => 'crsr-money'
                )
            ));

            $builder->add('descontos', MoneyType::class, array(
                'required' => false,
                'label' => 'Descontos',
                'currency' => 'BRL',
                'grouping' => 'true',
                'attr' => array(
                    'class' => 'crsr-money'
                )
            ));

            $builder->add('acrescimos', MoneyType::class, array(
                'required' => false,
                'label' => 'Acréscimos',
                'currency' => 'BRL',
                'grouping' => 'true',
                'attr' => array(
                    'class' => 'crsr-money'
                )
            ));

            $builder->add('valorTotal', MoneyType::class, array(
                'label' => 'Valor Total',
                'currency' => 'BRL',
                'grouping' => 'true',
                'required' => false,
                'attr' => array(
                    'class' => 'crsr-money'
                ),
                'disabled' => true
            ));

            // Cheque

            $builder->add('chequeBanco', EntityType::class, array(
                'required' => false,
                'label' => 'Banco',
                'class' => Banco::class,
                'choices' => $this->doctrine->getRepository(Banco::class)
                    ->findAll(),
                'choice_label' => function (Banco $banco) {
                    return sprintf("%03d", $banco->getCodigoBanco()) . " - " . $banco->getNome();
                },
                'attr' => array(
                    'class' => 'CAMPOS_CHEQUE'
                )
            ));

            $builder->add('chequeAgencia', TextType::class, array(
                'label' => 'Agência',
                'required' => false,
                'attr' => array(
                    'class' => 'CAMPOS_CHEQUE'
                )
            ));

            $builder->add('chequeConta', TextType::class, array(
                'label' => 'Conta',
                'required' => false,
                'attr' => array(
                    'class' => 'CAMPOS_CHEQUE'
                )
            ));

            $builder->add('chequeNumCheque', TextType::class, array(
                'label' => 'Núm Cheque',
                'required' => false,
                'attr' => array(
                    'class' => 'CAMPOS_CHEQUE'
                )
            ));

            // Recorrência

            $builder->add('recorrente', ChoiceType::class, array(
                'label' => 'Recorrente',
                'choices' => array(
                    'SIM' => true,
                    'NÃO' => false
                ),
                'required' => true
            ));

            $builder->add('recorrDia', IntegerType::class, array(
                'label' => 'Dia',
                'attr' => ['min' => 1, 'max' => 31],
                'required' => false
            ));

            $builder->add('recorrTipoRepet', ChoiceType::class, array(
                'label' => 'Tipo Repet',
                'choices' => array(
                    'DIA FIXO' => 'DIA_FIXO',
                    'DIA ÚTIL' => 'DIA_UTIL'
                ),
                'required' => false
            ));

            $builder->add('recorrFrequencia', ChoiceType::class, array(
                'label' => 'Frequência',
                'choices' => array(
                    'MENSAL' => 'MENSAL',
                    'ANUAL' => 'ANUAL'
                ),
                'required' => false
            ));

            $builder->add('recorrVariacao', IntegerType::class, array(
                'label' => 'Variação',
                'required' => false
            ));
        });


    }


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Movimentacao::class
        ));
    }
}