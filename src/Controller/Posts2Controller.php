<?php

namespace App\Controller;

use App\Entity\Posts;
use App\Form\Posts1Type;
use App\Repository\PostsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/posts2")
 */
class Posts2Controller extends AbstractController
{
    /**
     * @Route("/", name="app_posts2_index", methods={"GET"})
     */
    public function index(PostsRepository $postsRepository): Response
    {
        return $this->render('posts2/index.html.twig', [
            'posts' => $postsRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="app_posts2_new", methods={"GET", "POST"})
     */
    public function new(Request $request, PostsRepository $postsRepository): Response
    {
        $post = new Posts();
        $form = $this->createForm(Posts1Type::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $postsRepository->add($post, true);

            return $this->redirectToRoute('app_posts2_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('posts2/new.html.twig', [
            'post' => $post,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_posts2_show", methods={"GET"})
     */
    public function show(Posts $post): Response
    {
        return $this->render('posts2/show.html.twig', [
            'post' => $post,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_posts2_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Posts $post, PostsRepository $postsRepository): Response
    {
        $form = $this->createForm(Posts1Type::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $postsRepository->add($post, true);

            return $this->redirectToRoute('app_posts2_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('posts2/edit.html.twig', [
            'post' => $post,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_posts2_delete", methods={"POST"})
     */
    public function delete(Request $request, Posts $post, PostsRepository $postsRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$post->getId(), $request->request->get('_token'))) {
            $postsRepository->remove($post, true);
        }

        return $this->redirectToRoute('app_posts2_index', [], Response::HTTP_SEE_OTHER);
    }
}
