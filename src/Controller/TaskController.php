<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Task;
use App\Form\TaskType;
use App\Repository\TaskRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/task")
 */
class TaskController extends Controller
{
    /**
     * @Route("/", name="task_index", methods="GET")
     */
    public function index(TaskRepository $taskRepository): Response
    {
        return $this->render('task/index.html.twig', ['rows' => $taskRepository->findAllWithLongestComment()]);
    }

    /**
     * @Route("/new", name="task_new", methods="GET|POST")
     */
    public function new(Request $request): Response
    {
        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        // for dev/test purposes
//        $form->get('title')->addError(new FormError('test error message'));

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($task);
            $em->flush();

            return $this->redirectToRoute('task_index');
        }

        return $this->render('task/new.html.twig', [
            'task' => $task,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="task_show", methods="GET|POST")
     */
    public function show(Request $request, Task $task): Response
    {
        $comment = new Comment();
        $comment->setTask($task);
        $comment->setAuthor($this->getUser());
        
        $form = $this->createFormBuilder($comment)->add('text', null, ['label' => 'Post a comment'])->getForm();
        
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            // actually it should be Comment controller that handles POST request of this form
            // but i feel too lazy to create controller just for one simple action (at least for the test task)
            $em = $this->getDoctrine()->getManager(); 
            $em->persist($comment);
            $em->flush();

            // redirect approach
            // pros: clears the POST (no res-submit on browser refresh)
            // cons: generates extra HTTP request (302 Found, Location: ... etc)
            return $this->redirectToRoute('task_show', ['id' => $task->getId()]);
            
            // no redirect approach
            /*
            $comment = new Comment();
            $comment->setTask($task);
            $comment->setAuthor($this->getUser());
            $form = $this->createForm(CommentType::class, $comment);
            */
        }

        return $this->render('task/show.html.twig', ['task' => $task, 'commentForm' => $form->createView()]);
    }

    /**
     * @Route("/{id}/edit", name="task_edit", methods="GET|POST")
     */
    public function edit(Request $request, Task $task): Response
    {
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('task_edit', ['id' => $task->getId()]);
        }

        return $this->render('task/edit.html.twig', [
            'task' => $task,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="task_delete", methods="DELETE")
     */
    public function delete(Request $request, Task $task): Response
    {
        if ($this->isCsrfTokenValid('delete'.$task->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($task);
            $em->flush();
        }

        return $this->redirectToRoute('task_index');
    }
}
