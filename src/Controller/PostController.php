<?php

namespace App\Controller;

use App\Entity\Post;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

use Doctrine\ORM\EntityManagerInterface;

use App\Form\PostType;

class PostController extends AbstractController
{
    #[Route('/post/create', name: 'app_post_create', methods: ['GET', 'POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PostType::class);

        $form->handleRequest($request);
        if( $form->isSubmitted() && $form->isValid() ) {
            $entityManager->persist($form->getData());
            $entityManager->flush();
            $this->addFlash('success', 'Post almacenado con éxito');
            return $this->redirectToRoute('app_post_create');
        }

        return $this->render('post/create_edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    #[Route('/post/{id}/edit', name: 'app_post_edit', methods: ['GET', 'POST'])]
    public function edit(Post $post, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PostType::class, $post);

        $form->handleRequest($request);
        if( $form->isSubmitted() && $form->isValid() ) {
            $entityManager->flush();
            $this->addFlash('success', 'Post actualizado con éxito');
            return $this->redirectToRoute('app_post_edit', ['id' => $post->getId()]);
        }

        return $this->render('post/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
