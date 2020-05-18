<?php


namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class WildController
 * @package App\Controller
 * @route("/wild", name="wild_")
 */

Class WildController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index() :Response
    {
        return $this->render('wild/index.html.twig', [
            'website' => 'Wild Series',
        ]);
    }
    /**
     * @Route("/show/{slug}", requirements={"slug"="[a-z0-9-]+"}, name="show")
     * @param string $slug
     * @return Response
     */
    public function show(string $slug = ''): Response
    {
        $slug = ucwords(str_replace('-',' ', $slug));
        return $this->render('wild/show.html.twig', [
            'slug' => $slug,
        ]);
    }

}