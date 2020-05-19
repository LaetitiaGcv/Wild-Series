<?php


namespace App\Controller;

use App\Entity\Category;
use App\Entity\Program;
use App\Entity\Season;
use App\Entity\Episode;
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
     * Show all rows from Program's entity
     *defaults={"title"="Aucune série selectionnée"},
     * * @return Response A response instance
     * @Route("/", name="index")
     */
    public function index() :Response
    {
        $programs = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findAll();

        if (!$programs) {
            throw $this->createNotFoundException(
                'No program found in program\'s table.'
            );
        }
        return $this->render(
            'wild/index.html.twig',
            ['programs' => $programs]
        );
    }

    /**
     * Getting a program with a formatted slug for title
     * @param string $slug The slugger
     * @Route("/show/{slug<^[a-z0-9-]+$>}", defaults={"slug" = null}, name="show")
     * @return Response
     */
    public function show(string $slug = ''): Response
    {
        if (!$slug) {
            throw $this
                ->createNotFoundException('No slug has been sent to find a program in program\'s table.');
        }
        $slug = preg_replace(
            '/-/',
            ' ', ucwords(trim(strip_tags($slug)), "-")
        );
        $program = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findOneBy(['title' => mb_strtolower($slug)]);
        if (!$program) {
            throw $this->createNotFoundException(
                'No program with ' . $slug . ' title, found in program\'s table.'
            );
        }

        return $this->render('wild/show.html.twig', [
            'program' => $program,
            'slug' => $slug,
        ]);
    }
    /**
     * @Route("/category/{categoryName}" , name="show_category")
     * @param string $categoryName
     * @return Response
     */
    public function showByCategory(string $categoryName = ''): Response
    {
        $categoryRepository = $this->getDoctrine()->getRepository(Category::class);
        $category = $categoryRepository->findOneBy(['name' => $categoryName]);

        $programs = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findBy(['category' => $category->getId()], ['id' => 'DESC'], 3);

        return $this->render('wild/category.html.twig', [
            'category' => $category,
            'category_name' => $categoryName,
            'programs' => $programs,

        ]);

    }

    /**
     * @Route("/program/{programId}", requirements={"program"="[0-9]+"}, name="show_programId")
     * @param string $programId
     * @return Response
     */

    public function showByProgram(string $programId): Response
    {
        if (!$programId) {
            throw $this
                ->createNotFoundException('No program has been found');
        }
        $programTitle = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findOneBy(['id' => mb_strtolower($programId)]);

        return $this->render('wild/program.html.twig', [
            'seasons' => $programTitle->getSeasons(),
            'program' => $programTitle,

        ]);
    }

    /**
     * @Route("/season/{seasonId}", requirements={"season"="[0-9]+"}, name="show_seasonId")
     * @param int $seasonId
     * @return Response
     */
    public function showBySeason(int $seasonId): Response
    {
        if (!$seasonId) {
            throw $this
                ->createNotFoundException('No season has been found');
        }
        $season = $this->getDoctrine()
            ->getRepository(Season::class)
            ->findOneBy(['id' => mb_strtolower($seasonId)]);

        $program = $season->getProgram();
        $episodes = $season->getEpisodes();


        return $this->render('wild/episodes.html.twig', [
            'episodes' => $episodes,
            'program'  => $program,
            'season'   => $season,
        ]);
    }

}