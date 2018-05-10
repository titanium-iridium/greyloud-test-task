<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class TaskController extends Controller
{
    /**
     * @Route("/task", name="task_index")
     */
    public function index()
    {
        return $this->render('task/index.html.twig', [
            'controller_name' => 'TaskController',
        ]);
    }
    
    /**
     * @Route("/task/view/{id}", name="task_view")
     */
    public function view($id)
    {
        return $this->render('task/view.html.twig', [
            'controller_name' => 'TaskController',
        ]);
    }
    
    /**
     * @Route("/task/create", name="task_create")
     */
    public function create()
    {
        return $this->render('task/view.html.twig', [
            'controller_name' => 'TaskController',
        ]);
    }
    
    /**
     * @Route("/task/update", name="task_update")
     */
    public function update()
    {
        return $this->render('task/update.html.twig', [
            'controller_name' => 'TaskController',
        ]);
    }
}
