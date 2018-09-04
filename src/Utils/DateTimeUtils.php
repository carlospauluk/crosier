<?php

namespace App\Utils;

/**
 * Class DateTimeUtils.
 *
 * @package App\Utils
 * @author Carlos Eduardo Pauluk
 */
class DateTimeUtils
{

    /**
     *
     * Retorna se o período especificado é um período relatorial.
     * 01 - 15
     * 01 - ultimoDia
     * 16 - ultimoDia
     * 16 - 15
     *
     * @param \DateTime $dtIni
     * @param \DateTime $dtFim
     * @return bool
     * @throws \Exception
     */
    public static function isPeriodoRelatorial(\DateTime $dtIni, \DateTime $dtFim)
    {
        if ($dtIni->getTimestamp() > $dtFim->getTimestamp()) {
            throw new \Exception("dtIni > dtFim");
        }

        $dtFimEhUltimoDiaDoMes = $dtFim->format('Y-m-d') === $dtFim->format('Y-m-t');
        $dtIniDia = $dtIni->format('d');
        $dtFimDia = $dtFim->format('d');

        // 01 - 15
        // 01 - ultimoDia
        // 16 - ultimoDia
        // 16 - 15
        return ($dtIniDia == 1 and $dtFimDia == 15) OR
            ($dtIniDia == 1 and $dtFimEhUltimoDiaDoMes) OR
            ($dtIniDia == 16 and $dtFimEhUltimoDiaDoMes) OR
            ($dtIniDia == 16 and $dtFimDia == 15);

    }


    /**
     * Incrementa o período relatorial.     *
     *
     * @param \DateTime $dtIni
     * @param \DateTime $dtFim
     * @return false|string
     * @throws \Exception
     */
    public static function incPeriodoRelatorial(\DateTime $dtIni, \DateTime $dtFim)
    {
        if (!DateTimeUtils::isPeriodoRelatorial($dtIni, $dtFim)) {
            throw new \Exception("O período informado não é relatorial.");
        }

        $dtFimEhUltimoDiaDoMes = $dtFim->format('Y-m-d') === $dtFim->format('Y-m-t');
        $dtIniDia = $dtIni->format('d');
        $dtFimDia = $dtFim->format('d');

        $difMeses = $dtFim->diff($dtIni)->m;
        $difDias = $dtFim->diff($dtIni)->days;

        // A próxima dtIni vai ser sempre um dia depois da dtFim
        $proxDtIni = $dtFim->add(new \DateInterval('P1D'));

        // dtFim vai ser sempre dia 16 ou o último dia do mês
        if ($difMeses == 0) {
            if ($difDias > 16) {
                // Não é quinzena
                $proxDtFim = $dtFim->add(new \DateInterval('P1M'))->format('Y-m-t');
            } else {
                // é quinzena
                if ($dtFimDia == 15) {
                    $proxDtFim = $dtFim->format('Y-m-t');
                } else { // fimDia == ultimo dia do mês
                    $proxDtFim = $dtFim->add(new \DateInterval('P1M'));
                    $proxDtFim = $proxDtFim->setDate($proxDtFim->format('Y'), $proxDtFim->format('m'), 15);
                }
            }
        } else {
            // não estão no mesmo mês...

            // É um período de 45 dias ou mais
            if (($dtIniDia == 1) and ($dtFimDia == 15 or $dtFimEhUltimoDiaDoMes)) {
                // iniDia = 16 (fimDia + 1)
                // fimDia = ultimo dia do mês
                $proxDtFim = \DateTime::createFromFormat('Ymt', $dtFim->format('Y') . $dtFim->format('m') . '28');
            } else if (($dtIniDia == 16) and $dtFimEhUltimoDiaDoMes or $dtFimDia == 15) {
                $proxDtFim = $dtFim->add(new \DateInterval('P' . $difMeses . 'M'));
                $proxDtFim = \DateTime::createFromFormat('Ymt', $dtFim->format('Y') . $dtFim->format('m') . '15');
            }
        }

        $periodo['dtIni'] = $dtIni;
        $periodo['dtFim'] = $dtFim;

        return json_encode($periodo);
    }

}