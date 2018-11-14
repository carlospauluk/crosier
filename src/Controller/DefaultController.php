<?php

namespace App\Controller;

use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends Controller
{

    private $logger;

    /**
     *
     * @Route("/", name="root")
     */
    public function routes()
    {
        return $this->render('index.html.twig');

    }

    /**
     * @Route("/logAnError", name="logAnError")
     */
    public function logAnError() {
        $this->getLogger()->error('Um erro que não é um erro.');
        return new Response('Errou!');
    }


    /**
     * @return mixed
     */
    public function getLogger(): LoggerInterface
    {
        return $this->logger;
    }

    /**
     * @required
     * @param mixed $logger
     */
    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }
}