<?php

namespace App\Controller;

use App\Service\Greeting;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;

/**
 * @Route("/blog")
 */
class BlogController extends AbstractController
{
    /**
     * @var Greeting
     */
    private $greeting;
    /**
     * @var SessionInterface
     */
    private $session;
    /**
     * @var \Twig_Environment
     */
    private $twig;
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * BlogController constructor.
     */
    public function __construct(
        Greeting $greeting,
        SessionInterface $session,
        \Twig_Environment $twig,
        RouterInterface $router
    )
    {
        $this->greeting = $greeting;
        $this->session = $session;
        $this->twig = $twig;
        $this->router = $router;
    }


    /**
     * @Route("/",name="blog_index")
     */
    public function index()
    {
        return $this->render('blog/index.html.twig',[
            'posts' => $this->session->get('posts')
        ]);
    }

    /**
     * @Route("/add",name="blog_add")
     */
    public function add()
    {
        $posts = $this->session->get('posts');

        $posts[uniqid()] = [
            'title' => 'A random title ' . rand(1,500),
            'text' => 'Some random text nr ' . rand(1,500),
        ];
        $this->session->set('posts',$posts);

        return new RedirectResponse($this->router->generate('blog_index'));
    }

    /**
     * @Route("/show/{id}",name="blog_show")
     */
    public function show($id)
    {
        $posts = $this->session->get('posts');

//        dd($posts);

        if(!$posts || !isset($posts[$id])){
            throw new NotFoundHttpException('Post not found');
        }

        return $this->render('blog/post.html.twig',[
            'id' => $id,
            'post' => $posts[$id],
        ]);
    }
}