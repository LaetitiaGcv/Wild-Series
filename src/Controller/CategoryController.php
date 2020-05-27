<?php


namespace App\Controller;



use App\Form\CategoryType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{
    /**
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @route("/wild/category/add", name="category_add")
     * @return Response
     */

    public function add(EntityManagerInterface $entityManager, Request $request): Response
    {
        $form = $this->createForm(CategoryType::class);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $entityManager -> persist($data);
            $entityManager -> flush();

            return $this->redirectToRoute('category_add');
        }

        return $this->render(
            'wild/add.html.twig', [
                'form' => $form->createView(),
            ]
        );
    }
}
