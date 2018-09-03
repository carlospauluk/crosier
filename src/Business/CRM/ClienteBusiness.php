<?php

namespace App\Business\CRM;

use App\Entity\Base\Pessoa;
use App\Entity\CRM\Cliente;
use Symfony\Bridge\Doctrine\RegistryInterface;

class ClienteBusiness
{

    private $doctrine;

    public function __construct(RegistryInterface $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function parseFormData(&$formData)
    {

        foreach ($formData as $key => $value) {
            if ($value == '') {
                $formData[$key] = null;
            }
        }

        $formData['codigo'] = (isset($formData['codigo']) and $formData['codigo'] > 0) ? $formData['codigo'] : null;
        $formData['cpf'] = (isset($formData['cpf']) and $formData['cpf'] !== null) ? preg_replace("/[^0-9]/", "", $formData['cpf']) : null;
        $formData['cnpj'] = (isset($formData['cnpj']) and $formData['cnpj'] !== null) ? preg_replace("/[^0-9]/", "", $formData['cnpj']) : null;

    }

    /**
     * Transforma um Cliente em um array para manipulação no ClienteType.
     * @param Cliente $cliente
     * @return array
     */
    public function cliente2FormData(Cliente $cliente)
    {
        $formData = array();

        // Campos gerais (tanto para PESSOA_FISICA quanto para PESSOA_JURIDICA)

        $formData['id'] = $cliente->getId();

        $formData['tipoPessoa'] = $cliente->getPessoa()->getTipoPessoa();
        $formData['pessoa_id'] = $cliente->getPessoa()->getId();
        $formData['codigo'] = $cliente->getCodigo();
        $formData['fone1'] = $cliente->getFone1();
        $formData['fone2'] = $cliente->getFone2();
        $formData['fone3'] = $cliente->getFone3();
        $formData['fone4'] = $cliente->getFone4();
        $formData['email'] = $cliente->getEmail();
        $formData['obs'] = $cliente->getObs();


        if ($cliente->getPessoa()->getTipoPessoa() == 'PESSOA_FISICA') {

            // Campos para PESSOA_FISICA

            $formData['cpf'] = $cliente->getPessoa()->getDocumento();
            $formData['nome'] = $cliente->getPessoa()->getNome();
            $formData['rg'] = $cliente->getRg();
            $formData['dtEmissaoRg'] = ($cliente->getDtEmissaoRg() instanceof \DateTime) ? $cliente->getDtEmissaoRg()->format('d/m/Y') : null;
            $formData['orgaoEmissorRg'] = $cliente->getOrgaoEmissorRg();
            $formData['estadoRg'] = $cliente->getEstadoRg();
            $formData['sexo'] = $cliente->getSexo();
            $formData['naturalidade'] = $cliente->getNaturalidade();
            $formData['estadoCivil'] = $cliente->getEstadoCivil();
            // FIXME: isso depois tem que ir na tabela de telefones
            $formData['aceitaWhatsapp'] = $cliente->getAceitaWhatsapp();
            $formData['temWhatsapp'] = $cliente->getTemWhatsapp();
        } else {

            // Campos para PESSOA_JURIDICA

            $formData['cnpj'] = $cliente->getPessoa()->getDocumento();
            $formData['razao_social'] = $cliente->getPessoa()->getNome();
            $formData['nome_fantasia'] = $cliente->getPessoa()->getNomeFantasia();
            $formData['inscricaoEstadual'] = $cliente->getInscricaoEstadual();
            $formData['inscricaoMunicipal'] = $cliente->getInscricaoMunicipal();
            $formData['contato'] = $cliente->getContato();
            $formData['website'] = $cliente->getWebsite();
        }

        return $formData;

    }

    /**
     * Converte um array do ClienteType para um Cliente.
     *
     * @param $formData
     * @return Cliente|null|object
     * @throws \Exception
     */
    public function formData2Cliente($formData)
    {
        if (isset($formData['id'])) {
            $cliente = $this->doctrine->getRepository(Cliente::class)->find($formData['id']);
            if (!$cliente) {
                $cliente = new Cliente();
                $cliente->setPessoa(new Pessoa());
            } else {
                $pessoa = $this->doctrine->getRepository(Pessoa::class)->find($formData['pessoa_id']);
                if (!$pessoa) {
                    throw new \Exception("Pessoa não encontrada.");
                }
                $cliente->setPessoa($pessoa);
            }
        } else {
            $cliente = new Cliente();
            $cliente->setPessoa(new Pessoa());
        }

        $cliente->getPessoa()->setTipoPessoa($formData['tipoPessoa']);

        $cliente->setCodigo($formData['codigo']);
        $cliente->setFone1($formData['fone1']);
        $cliente->setFone2($formData['fone2']);
        $cliente->setFone3($formData['fone3']);
        $cliente->setFone4($formData['fone4']);
        $cliente->setEmail($formData['email']);
        $cliente->setObs($formData['obs']);

        if ($cliente->getPessoa()->getTipoPessoa() == 'PESSOA_FISICA') {

            // Campos para PESSOA_FISICA

            $cliente->getPessoa()->setDocumento($formData['cpf']);
            $cliente->getPessoa()->setNome($formData['nome']);
            $cliente->setRg($formData['rg']);
            $cliente->setDtEmissaoRg($formData['dtEmissaoRg']);
            $cliente->setEstadoRg($formData['estadoRg']);
            $cliente->setSexo($formData['sexo']);
            $cliente->setNaturalidade($formData['naturalidade']);
            $cliente->setEstadoCivil($formData['estadoCivil']);
            $cliente->setAceitaWhatsapp($formData['aceitaWhatsapp']);
            $cliente->setTemWhatsapp($formData['temWhatsapp']);
        } else {

            // Campos para PESSOA_JURIDICA

            $cliente->getPessoa()->setDocumento($formData['cnpj']);
            $cliente->getPessoa()->setNome($formData['nome']);
            $cliente->getPessoa()->setNomeFantasia($formData['nomeFantasia']);
            $cliente->setInscricaoEstadual($formData['inscricaoEstadual']);
            $cliente->setInscricaoMunicipal($formData['inscricaoMunicipal']);
            $cliente->setContato($formData['contato']);
            $cliente->setWebsite($formData['website']);
        }

        return $cliente;
    }
}