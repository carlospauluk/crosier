<?php

namespace App\Form\Financeiro;

use App\Entity\Base\Pessoa;
use App\Entity\Financeiro\Carteira;
use App\Entity\Financeiro\Categoria;
use App\Entity\Financeiro\CentroCusto;
use App\Entity\Financeiro\Modo;
use App\Entity\Financeiro\Movimentacao;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Form\DataTransformer\EntityIdTransformer;
use App\Form\EntityIdAutoCompleteType;
use App\Form\ChoiceLoader;

class MovimentacaoType extends AbstractType {
	private $doctrine;
	private $entityIdTransformer;
	public function __construct(RegistryInterface $doctrine) {
		$this->doctrine = $doctrine;
	}
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$repoCarteira = $this->doctrine->getRepository(Carteira::class);
		$carteiras = $repoCarteira->findAll();
		
		$builder->add('carteira', EntityType::class, array (
				'class' => Carteira::class,
				'choices' => $carteiras,
				'choice_label' => function (Carteira $carteira) {
					return $carteira->getCodigo() . " - " . $carteira->getDescricao();
				} 
		));
		
		$repoModo = $this->doctrine->getRepository(Modo::class);
		$modos = $repoModo->findAll();
		
		$builder->add('modo', EntityType::class, array (
				'class' => Modo::class,
				'choices' => $modos,
				'choice_label' => function (Modo $modo) {
					return $modo->getCodigo() . " - " . $modo->getDescricao();
				} 
		));
		
		$repoCategoria = $this->doctrine->getRepository(Categoria::class);
		$categorias = $repoCategoria->findAll();
		
		$builder->add('categoria', EntityType::class, array (
				'class' => Categoria::class,
				'choices' => $categorias,
				'choice_label' => 'descricaoMontada' 
		));
		
		$builder->add('dtMoviment', DateType::class, array (
				'widget' => 'single_text',
				'format' => 'dd/MM/yyyy',
				'label' => 'Dt Moviment',
				'attr' => array (
						'class' => 'crsr-date' 
				) 
		));
		
		$builder->add('dtVencto', DateType::class, array (
				'widget' => 'single_text',
				'format' => 'dd/MM/yyyy',
				'label' => 'Dt Vencto',
				'attr' => array (
						'class' => 'crsr-date' 
				) 
		));
		
		$builder->add('dtVenctoEfetiva', DateType::class, array (
				'widget' => 'single_text',
				'format' => 'dd/MM/yyyy',
				'label' => 'Dt Vencto Efet',
				'attr' => array (
						'class' => 'crsr-date' 
				) 
		));
		
		// $pessoas = array ();
		// if ($builder->getData()->getPessoa() and $builder->getData()->getPessoa()->getId()) {
		// $repoPessoa = $this->doctrine->getRepository(Pessoa::class);
		// $pessoa = $repoPessoa->find($builder->getData()->getPessoa()->getId());
		// $pessoas [] = $pessoa;
		// }
		$repoPessoa = $this->doctrine->getRepository(Pessoa::class);
		$builder->add('pessoa', ChoiceType::class, array (
				// 'class' => Pessoa::class,
				// 'choices' => $pessoas,
				// 'choice_label' => 'nome',				
				'choice_loader' => new ChoiceLoader($builder, $repoPessoa),
				'choice_label' => function (Pessoa $pessoa) {
					return $pessoa->getNome();
				} 
		));
		
		// $builder->add('pessoa', EntityIdAutoCompleteType::class, array(
		// 'label' => 'Pessoa',
		// 'choices' => $pessoas,
		// 'repo' => $this->doctrine->getRepository(Pessoa::class)
		// ));
		
		// Adicionando lógicas para exibição do formulário
		$builder->addEventListener(FormEvents::PRE_SET_DATA, array (
				$this,
				'onPreSetData' 
		));
	}
	
	/**
	 *
	 * @param FormEvent $event
	 */
	public function onPreSetData(FormEvent $event) {
		$movimentacao = $event->getData();
		$form = $event->getForm();
		
		if ($movimentacao and $movimentacao->getCategoria() and $movimentacao->getCategoria()->getCentroCustoDif()) {
			
			$repo = $this->doctrine->getRepository(CentroCusto::class);
			$centrosCusto = $repo->findAll();
			
			$form->add('centroCusto', EntityType::class, array (
					'class' => CentroCusto::class,
					'choices' => $centrosCusto,
					'choice_label' => 'descricao' 
			));
		}
	}
	public function configureOptions(OptionsResolver $resolver) {
		$resolver->setDefaults(array (
				'data_class' => Movimentacao::class 
		));
	}
}