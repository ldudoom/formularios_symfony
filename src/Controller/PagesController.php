<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

use App\Form\ContactType;
use App\Entity\Post;
use Doctrine\ORM\EntityManagerInterface;

class PagesController extends AbstractController
{

    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        return $this->render('pages/index.html.twig', [
            'posts' => $entityManager->getRepository(Post::class)->findAll()
        ]);
    }

    #[Route('/contacts-v1', name: 'contact-v1', methods: ['GET', 'POST'])]
    public function contactsV1(Request $request): Response
    {
        $form = $this->createFormBuilder()
                     ->add('email', TextType::class)
                     ->add('message', TextareaType::class, [
                         'label' => 'Comentario, sugerencia o mensaje'
                     ])
                     ->add('send', SubmitType::class, [
                         'label' => 'Enviar'
                     ])
                     //->setMethod('GET')
                     //->setAction('otra-url')
                     ->getForm();

        $form->handleRequest($request);
        if( $form->isSubmitted() ) {
            // getData() contiene todos los valores que se han enviado
            //dd($form->getData(), $request);
            $this->addFlash('success', 'Prueba formulario #1 con éxito');
            return $this->redirectToRoute('contact-v1');
        }

        return $this->render('pages/create_edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/contacts-v2', name: 'contact-v2', methods: ['GET', 'POST'])]
    public function contactsV2(Request $request): Response
    {
        $form = $this->createForm(ContactType::class);

        $form->handleRequest($request);
        if( $form->isSubmitted() ) {
            //dd($form->getData(), $request);
            $this->addFlash('primary', 'Prueba formulario #2 con éxito');
            return $this->redirectToRoute('contact-v2');
        }

        return $this->render('pages/contact-v2.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/contacts-v3', name: 'contact-v3', methods: ['GET', 'POST'])]
    public function contactsV3(Request $request): Response
    {
        $form = $this->createForm(ContactType::class);

        $form->handleRequest($request);
        if( $form->isSubmitted() ) {
            //dd($form->getData());
            $this->addFlash('info', 'Prueba formulario #3 con éxito');
            return $this->redirectToRoute('contact-v3');
        }

        return $this->render('pages/contact-v3.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
