<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\LoginType;
use App\Form\UserType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SiteController extends Controller
{
    /**
     * @Route("/", name="site_index")
     */
    public function index(Request $request, AuthenticationUtils $authenticationUtils)
    {
        // redirect authenticated users to task index
        $context = $this->container->get('security.authorization_checker');
        if ($context->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirectToRoute('task_index');
        }
        
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        $form = $this->createForm(LoginType::class);

        return $this->render('site/login.html.twig', array(
            'last_username' => $lastUsername, // not used in template
            'error' => $error,
            'form' => $form->createView(),
        ));
    }
    

    /**
     * @Route("/register", name="site_register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        // redirect to site index if user already logged in
        $context = $this->container->get('security.authorization_checker');
        if ($context->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirectToRoute('site_index');
        }

        $user = new User();
        $user->setIsActive(true);
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            // encode password
            $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);
            
            // save entity
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Registration successful.');

            // redirect to site index on successful registration
            return $this->redirectToRoute('site_index');
        }

        return $this->render(
            'site/register.html.twig',
            array('form' => $form ? $form->createView() : false)
        );
    }
}
