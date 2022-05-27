<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegistroController extends AbstractController
{
    /**
     * @Route("/registro", name="app_registro")
     */
    public function index(Request $request, UserPasswordEncoderInterface $encoder): Response
    {
        $user = new User();
        $form = $this->createForm( UserType::class, $user);
	$form->handleRequest($request);
	if($form->isSubmitted() && $form->isValid()){
		$em = $this->getDoctrine()->getManager();
		$user->setPassword($encoder->encodePassword($user,$form['password']->getData()));
		$em->persist($user);
		$em->flush();
		$this->addFlash( 'exito', User::REGISTRO_EXITOSO );
		return $this->redirectToRoute('app_registro');
	}
        return $this->render('registro/index.html.twig', [
            'controller_name' => 'RegistroController',
            'formulario'=>$form->createView()
        ]);
    }
}
