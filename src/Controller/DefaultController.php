<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends Controller
{

    /**
     *
     * @Route("/admin")
     */
    public function admin()
    {
        return new Response('Well hi there '.$user->getFirstName());    }
}