<?php

namespace App\Business\Base;

use App\Entity\Base\Endereco;
use App\Entity\Base\Pessoa;
use Symfony\Bridge\Doctrine\RegistryInterface;

class PessoaBusiness
{

    private $doctrine;

    public function __construct(RegistryInterface $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * Preenche os campos 'transients' da pessoa.
     *
     * TODO: esses campos na verdade devem se tornar fixos na tabela bse_relacionamento.
     *
     * @param Pessoa $pessoa
     * @return \App\Entity\Base\Pessoa
     */
    public function fillTransients(Pessoa $pessoa)
    {
        if (!$pessoa->getId()) return $pessoa;
        $pessoaRepo = $this->doctrine->getRepository(Pessoa::class);

        $relacionamento = $pessoaRepo->findRelacionamento($pessoa);

        if ($relacionamento) {
            $pessoa->setEmail($relacionamento->getEmail());
            $pessoa->setFone1($relacionamento->getFone1());
            $pessoa->setInscricaoEstadual($relacionamento->getInscricaoEstadual());
        }

        $enderecoRepo = $this->doctrine->getRepository(Endereco::class);
        $endereco = $enderecoRepo->findPrimeiroByPessoa($pessoa);

        if ($endereco) {
            $pessoa->setEndereco($endereco);
        }

        return $pessoa;
    }

}