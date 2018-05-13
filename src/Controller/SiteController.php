<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\LoginType;
use App\Form\UserType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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

//        return $this->render('site/login.html.twig', [
//            'controller_name' => 'SiteController',
//        ]);
        
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

//        $user = new User();
//        $user->setUsername($lastUsername ?? '');
//        $user->setDueDate(new \DateTime('tomorrow'));
        
        $form = $this->createForm(LoginType::class);

//        $form = $this->createFormBuilder($user)
//            ->add('username', TextType::class)
//            ->add('password', PasswordType::class)
//            ->add('submit', SubmitType::class, array('label' => 'Login'))
//            ->getForm();

        return $this->render('site/login.html.twig', array(
            'last_username' => $lastUsername,
            'error' => $error,
            'form' => $form->createView(),
        ));
    }

//    /**
//     * @Route("/login", name="site_login")
//     */
//    public function login(Request $request, AuthenticationUtils $authenticationUtils)
//    {
//        // redirect to site index if user already logged in
//        $context = $this->container->get('security.authorization_checker');
//        if ($context->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
//            return $this->redirectToRoute('site_index');
//        }
//
//        // get the login error if there is one
//        $error = $authenticationUtils->getLastAuthenticationError();
//
//        // last username entered by the user
//        $lastUsername = $authenticationUtils->getLastUsername();
//
////        $user = new User();
////        $user->setUsername($lastUsername ?? '');
////        $user->setDueDate(new \DateTime('tomorrow'));
//        
//        $form = $this->createForm(LoginType::class);
//
////        $form = $this->createFormBuilder($user)
////            ->add('username', TextType::class)
////            ->add('password', PasswordType::class)
////            ->add('submit', SubmitType::class, array('label' => 'Login'))
////            ->getForm();
//
//        return $this->render('site/login.html.twig', array(
//            'last_username' => $lastUsername,
//            'error' => $error,
//            'form' => $form->createView(),
//        ));
//    }

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

            return $this->redirectToRoute('site_login');
        }

        return $this->render(
            'site/register.html.twig',
            array('form' => $form ? $form->createView() : false)
        );
    }
}
