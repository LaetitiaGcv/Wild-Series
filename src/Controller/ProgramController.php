<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Program;
use App\Form\ProgramType;
use App\Repository\ProgramRepository;
use App\Service\Slugify;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/program")
 */
class ProgramController extends AbstractController
{
    /**
     * @Route("/", name="program_index", methods={"GET"})
     * @param ProgramRepository $programRepository
     * @return Response
     * @IsGranted("ROLE_ADMIN")
     */
    public function index(ProgramRepository $programRepository): Response
    {
        return $this->render('program/index.html.twig', [
            'programs' => $programRepository->findAllWithCategoriesAndActor(),
        ]);
    }

    /**
     * @Route("/new", name="program_new", methods={"GET","POST"})
     * @param Request $request
     * @param Slugify $slugify
     * @param MailerInterface $mailer
     * @return Response
     * @throws TransportExceptionInterface
     * @IsGranted("ROLE_ADMIN")
     */
    public function new(Request $request, Slugify $slugify, MailerInterface $mailer): Response
    {
        $program = new Program();
        $form = $this->createForm(ProgramType::class, $program);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $slug = $slugify->generate($program->getTitle());
            $program->setSlug($slug);
            $entityManager->persist($program);
            $entityManager->flush();
            $this->addFlash('success', 'La nouvelle série : ' . $program->getTitle() . ' vient d\'être ajoutée');

            $email = (new Email())
                ->from($this->getParameter('mailer_from'))
                ->to('lae.gon.test@gmail.com')
                ->subject('Une nouvelle série vient d\'être publiée !')
                ->html($this->renderView('/program/email/notification.html.twig', ['program' => $program]));

            $mailer->send($email);

            return $this->redirectToRoute('program_index');
        }

        return $this->render('program/new.html.twig', [
            'program' => $program,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{slug}", name="program_show", methods={"GET"})
     * @param Program $program
     * @return Response
     * @IsGranted("ROLE_ADMIN")
     */
    public function show(Program $program): Response
    {
        return $this->render('program/show.html.twig', [
            'program' => $program,
        ]);
    }

    /**
     * @Route("/{slug}/edit", name="program_edit", methods={"GET","POST"})
     * @param Request $request
     * @param Program $program
     * @param Slugify $slugify
     * @IsGranted("ROLE_ADMIN")
     * @return Response
     */
    public function edit(Request $request, Program $program, Slugify $slugify): Response
    {
        $form = $this->createForm(ProgramType::class, $program);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $slug = $slugify->generate($program->getTitle());
            $program->setSlug($slug);
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'La série : ' . $program->getTitle() . ' vient d\'être modifiée');

            return $this->redirectToRoute('program_index');
        }

        return $this->render('program/edit.html.twig', [
            'program' => $program,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{slug}", name="program_delete", methods={"DELETE"})
     * @param Request $request
     * @param Program $program
     * @return Response
     * @IsGranted("ROLE_ADMIN")
     */
    public function delete(Request $request, Program $program): Response
    {
        if ($this->isCsrfTokenValid('delete'.$program->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($program);
            $entityManager->flush();
            $this->addFlash('danger', 'La série : ' . $program->getTitle() . ' vient d\'être supprimée');
        }

        return $this->redirectToRoute('program_index');
    }

    /**
     * @Route("/{id}/watchlist", name="program_watchlist", methods={"GET","POST"})
     * @param Request $request
     * @param Program $program
     * @param EntityManagerInterface $manager
     * @return Response
     * @IsGranted("ROLE_SUBSCRIBER")
     */
    public function addToWatchlist(Request $request, Program $program, EntityManagerInterface $manager)
    {
        $user = $this->getUser();
        if ($user->isInWatchlist($program)) {
            $user->removeProgram($program);
        } else {
            $user->addProgram($program);
        }
        $manager->persist($user);
        $manager->flush();

        return $this->json([
            'isInWatchlist' => $user->isInWatchlist($program)
        ]);
    }
}
