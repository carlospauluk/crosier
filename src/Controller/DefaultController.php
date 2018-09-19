<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends Controller
{

    /**
     *
     * @Route("/", name="root")
     */
    public function routes()
    {
        return $this->render('index.html.twig');

    }
}