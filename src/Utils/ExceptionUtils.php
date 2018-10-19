<?php
namespace App\Utils;

/**
 * Class ExceptionUtils.
 *
 * Tratamento de mensagem de exceçção do Doctrine.
 *
 * @package App\Utils
 */
class ExceptionUtils
{

    public static function treatException(\Exception $e)
    {
        $message = $e->getMessage();
        $code = $e->getErrorCode();

        $regex = '/(?:.*)(?:SQLSTATE)(?:.*)(?:' . $code . ')(?<msg>.*)/';
        preg_match($regex, $message, $matches);
        if (isset($matches['msg'])) {
            return $matches['msg'];
        } else {
            return $e->getMessage();
        }

    }

}