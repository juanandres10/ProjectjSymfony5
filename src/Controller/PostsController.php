<?php

namespace App\Controller;

use App\Entity\Comentarios;
use App\Entity\Posts;
use App\Form\ComentariosType;
use App\Form\PostsType;
use App\Repository\PostsRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;

class PostsController extends AbstractController
{
    /**
     * @Route("/registrar-posts", name="RegistrarPosts")
     */
    public function index(Request $request, SluggerInterface $slugger): Response
    {
	$post = new Posts();
        $form = $this->createForm( PostsType::class, $post);
	$form->handleRequest($request);
	if($form->isSubmitted() && $form->isValid()){
		$brochureFile = $form->get('foto')->getData();
		if ($brochureFile) {
               		$originalFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);
                	// Obtiene el nombre de la url y hace unos cambios para que sea unico.
                	$safeFilename = $slugger->slug($originalFilename);
                	$newFilename = $safeFilename.'-'.uniqid().'.'.$brochureFile->guessExtension();

               		// Mueve la foto al directorio que le digamos para guardarla
                	try {
                    		$brochureFile->move(
                        		$this->getParameter('brochures_directory'),
                        		$newFilename
                    		);
                	} catch (FileException $e) {
                    		throw new \Exception ('Ha ocurrido un error, intentalo de nuevo');
                	}

                	// Actualiza el nombre de la foto para que en caso de que haya dos imagenes con el mismo nombre no den error; ya que al cambiarle un nombre le pone uno por defecto.
                	$post->setFoto($newFilename);
            	}
		$user = $this->getUser();
		$post->setUser($user);
		$em = $this->getDoctrine()->getManager();
		$em->persist($post);
		$em->flush();
		return $this->redirectToRoute('app_dashboard');
	}
        return $this->render('posts/index.html.twig', [
	    'formulario'=>$form->createView()
        ]);
    }

    /**
     * @Route("/posts/{id}", name="VerPost")
     */
    public function VerPost($id, Request $request, PaginatorInterface $paginator): Response{
	$em = $this->getDoctrine()->getManager();
	$comentario = new Comentarios();
	$post = $em->getRepository(Posts::class)->find($id);
	$queryComentarios = $em->getRepository(Comentarios::class)->BuscarComentariosDeUNPost($post->getId());
	$form = $this->createForm(ComentariosType::class, $comentario);
	$form->handleRequest($request);
	if($form->isSubmitted() && $form->isValid()){
		$user = $this->getUser();
            	$comentario->setPosts($post);
            	$comentario->setUser($user);
            	$em->persist($comentario);
            	$em->flush();
		$this->addFlash('Exito', Comentarios::COMENTARIO_AGREGADO_EXITOSAMENTE);
 		return $this->redirectToRoute('VerPost',['id'=>$post->getId()]);
	}
	$pagination = $paginator->paginate(
		$queryComentarios, /* query NOT result */
		$request->query->getInt('page', 1), /*page number*/
		5 /*limit per page*/
        );
        return $this->render('posts/verPost.html.twig', [
		'post' => $post,
		'form'=>$form->createView(),
		'comentarios'=>$pagination
        ]);
    }

    /**
     * @Route("/mis-posts", name="MisPosts", methods={"GET"})
     */
    public function MisPosts(): Response{
    $em = $this->getDoctrine()->getManager();
    $user = $this->getUser();
    $posts = $em->getRepository(Posts::class)->findBy(['user'=>$user]);
        return $this->render('posts/misPosts.html.twig', [
            'posts' => $posts,
        ]);
    }
}

