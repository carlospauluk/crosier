<?php

namespace App\Command\Estoque;

use App\Entity\Estoque\Grade;
use App\Entity\Estoque\GradeOcOption;
use App\Entity\Estoque\GradeTamanhoOcOptionValue;
use App\EntityOC\OcOption;
use App\EntityOC\OcOptionDescription;
use App\EntityOC\OcOptionValue;
use App\EntityOC\OcOptionValueDescription;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 *
 * @package App\Command\Base
 */
class CorrigirGradesOCCommand extends Command
{

    private $doctrine;

    public function __construct(RegistryInterface $doctrine)
    {
        $this->doctrine = $doctrine;
        parent::__construct();
    }

    /**
     * @return RegistryInterface
     */
    public function getDoctrine(): RegistryInterface
    {
        return $this->doctrine;
    }

    /**
     * @param RegistryInterface $doctrine
     */
    public function setDoctrine(RegistryInterface $doctrine): void
    {
        $this->doctrine = $doctrine;
    }


    protected function configure()
    {
        $this
            ->setName('crosier:corrigirGradesOCCommand');
    }

    /**
     * Cria os option e option_value na base do OC e vincula com as grades e tamanhos.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        //Executar com debug para verificar se não vai dar problema.
        if (true) return;

        $grades = $this->doctrine->getRepository(Grade::class)->findAll();

        $ocEntityManager = $this->doctrine->getEntityManager('oc');
        $em = $this->doctrine->getEntityManager();

        $ocEntityManager->beginTransaction();
        $em->beginTransaction();

        try {
            foreach ($grades as $grade) {
                // verifica se já está relacionada a uma ocOption
                $output->writeln('');
                $output->writeln('');
                $output->writeln('Trabalhando com a grade: ' . $grade->getObs());

                $gradeOcOption = $this->doctrine->getRepository(GradeOcOption::class)->findOneBy(['grade' => $grade]);
                if (!$gradeOcOption) {
                    // cria uma oc_option
                    $ocOption = new OcOption();
                    $ocOption->setType('select');
                    $ocOption->setSortOrder(0);

                    $ocEntityManager->persist($ocOption);
                    $ocEntityManager->flush();

                    $ocOptionDescription = new OcOptionDescription();
                    $ocOptionDescription->setOptionId($ocOption->getOptionId());
                    $ocOptionDescription->setLanguageId(2); // fixo
                    $ocOptionDescription->setName('Tamanho'); // para todos é o mesmo (devia ter um campo obs)

                    $ocEntityManager->persist($ocOptionDescription);
                    $ocEntityManager->flush();


                    $gradeOcOption = new GradeOcOption();
                    $gradeOcOption->setGrade($grade);
                    $gradeOcOption->setOptionId($ocOption->getOptionId());
                    $em->persist($gradeOcOption);
                    $em->flush();
                } else {
                    $ocOption = $ocEntityManager->getRepository(OcOption::class)->find($gradeOcOption->getOptionId());
                }
                if (!$ocOption) throw new \Exception('ocOption == null');

                foreach ($grade->getTamanhos() as $tamanho) {
                    $tamanhoOc = $this->doctrine->getRepository(GradeTamanhoOcOptionValue::class)->findOneBy(['gradeTamanho' => $tamanho]);
                    if (!$tamanhoOc) {
                        // cria uma oc_option
                        $ocOptionValue = new OcOptionValue();
                        $ocOptionValue->setOptionId($ocOption->getOptionId());
                        $ocOptionValue->setImage('');
                        $ocOptionValue->setSortOrder($tamanho->getOrdem());

                        $ocEntityManager->persist($ocOptionValue);
                        $ocEntityManager->flush();

                        $ocOptionValueDescription = new OcOptionValueDescription();
                        $ocOptionValueDescription->setOptionValueId($ocOptionValue->getOptionValueId());
                        $ocOptionValueDescription->setLanguageId(2); // fixo
                        $ocOptionValueDescription->setOptionId($ocOption->getOptionId());
                        $ocOptionValueDescription->setName($tamanho->getTamanho());

                        $ocEntityManager->persist($ocOptionValueDescription);
                        $ocEntityManager->flush();

                        $gradeTamanhoOcOptionValue = new GradeTamanhoOcOptionValue();
                        $gradeTamanhoOcOptionValue->setGradeTamanho($tamanho);
                        $gradeTamanhoOcOptionValue->setOptionValueId($ocOptionValue->getOptionValueId());
                        $em->persist($gradeTamanhoOcOptionValue);
                        $em->flush();
                    }
                }

            }

            $ocEntityManager->commit();
            $em->commit();
        } catch (\Exception $e) {
            $ocEntityManager->rollback();
            $em->rollback();
        }


    }

}