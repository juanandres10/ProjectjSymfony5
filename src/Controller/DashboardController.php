<?php

namespace App\Controller;

use App\Entity\Comentarios;
use App\Entity\Posts;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    /**
     * @Route("/", name="app_dashboard")
     */
    public function index(PaginatorInterface $paginator, Request $request): Response
    {
	$user = $this->getUser(); //OBTENGO AL USUARIO ACTUALMENTE LOGUEADO
	if($user){
		$em = $this->getDoctrine()->getManager();
		$query = $em->getRepository(Posts::class)->BuscarPosts();
		$comentarios = $em->getRepository(Comentarios::class)->BuscarComentarios($user->getId()); // Consulto los comentarios con el ID del usuario actualmente logueado
		$pagination = $paginator->paginate(
        		$query, /* Pasamos query, no resultado del query */
        		$request->query->getInt('page', 1), /*Numero pagina donde empieza*/
        		2 /*Limite de posts por pagina, si hay mas pagina nueva*/
   		);
		return $this->render('dashboard/index.html.twig', [
			'pagination' => $pagination,
			'comentarios'=>$comentarios
		]);
	}else{
            return $this->redirectToRoute('app_login');
        }
    }
}

