<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SiteController extends Controller
{
    /**
     * @Route("/", name="index")
     */
    public function index()
    {
//        $context = $this->container->get('security.authorization_checker');
//        if (!($context->isGranted('IS_AUTHENTICATED_REMEMBERED'))) {
//            return $this->redirect('/login');
//        }

        return $this->render('site/index.html.twig', [
            'controller_name' => 'SiteController',
        ]);
    }

    /**
     * @Route("/login", name="site_login")
     */
    public function login(Request $request, AuthenticationUtils $authenticationUtils)
    {
//        $context = $this->container->get('security.authorization_checker');
//        if ($context->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
//            return $this->redirect('/');
//        }
  
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('site/login.html.twig', array(
            'last_username' => $lastUsername,
            'error' => $error,
        ));
    }

    /**
     * @Route("/register", name="site_register")
     */
    public function register()
    {
        return $this->render('site/register.html.twig', [
            'controller_name' => 'SiteController',
        ]);
    }
}
