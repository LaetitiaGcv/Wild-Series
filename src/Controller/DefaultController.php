<?php

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


namespace App\Controller;


class DefaultController extends AbstractController
{
    /**
     * @Route("/wild", name="app_index")
     */
    public function index() :Response
    {
        return $this->render('wild/home.html.twig', [
            'home' => 'Welcome',
        ]);
    }

}