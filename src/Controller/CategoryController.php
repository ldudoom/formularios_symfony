<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{
    #[Route('/category', name: 'app_category_list')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        return $this->render('category/index.html.twig', [
            'categories' => $entityManager->getRepository(Category::class)->findAll(),
        ]);
    }

    #[Route('/category/create', name: 'app_category_create', methods: ['GET', 'POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CategoryType::class);

        $form->handleRequest($request);
        if( $form->isSubmitted() && $form->isValid() ) {
            $entityManager->persist($form->getData());
            $entityManager->flush();
            $this->addFlash('success', 'Categoría almacenada con éxito');
            return $this->redirectToRoute('app_category_list');
        }

        return $this->render('category/create_edit.html.twig', [
            'form' => $form->createView(),
            'action' => 'Crear',
        ]);
    }

    #[Route('/category/{id}/edit', name: 'app_category_edit', methods: ['GET', 'POST'])]
    public function edit(Category $category, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);
        if( $form->isSubmitted() && $form->isValid() ) {
            $entityManager->flush();
            $this->addFlash('success', 'Categoría editada con éxito');
            return $this->redirectToRoute('app_category_edit', ['id' => $category->getId()]);
        }

        return $this->render('category/create_edit.html.twig', [
            'form' => $form->createView(),
            'action' => 'Editar',
        ]);
    }
}
