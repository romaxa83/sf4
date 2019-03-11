<?php

namespace App\Controller;

use App\Entity\MicroPost;
use App\Entity\User;
use App\Form\MicroPostType;
use App\Repository\MicroPostRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

/**
 * @Route("/micro-post")
 */
class MicroPostController extends AbstractController
{
    /**
     * @var \Twig_Environment
     */
    private $twig;
    /**
     * @var MicroPostRepository
     */
    private $microPostRepository;
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var RouterInterface
     */
    private $router;
    /**
     * @var FlashBagInterface
     */
    private $flashBag;
    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    public function __construct(
        \Twig_Environment $twig,
        MicroPostRepository $microPostRepository,
        FormFactoryInterface $formFactory,
        EntityManagerInterface $entityManager,
        RouterInterface $router,
        FlashBagInterface $flashBag,
        AuthorizationCheckerInterface $authorizationChecker
    )
    {
        $this->twig = $twig;
        $this->microPostRepository = $microPostRepository;
        $this->formFactory = $formFactory;
        $this->entityManager = $entityManager;
        $this->router = $router;
        $this->flashBag = $flashBag;
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * @Route("/",name="micro_post_index")
     */
    public function index(TokenStorageInterface $tokenStorage,
                          UserRepository $userRepository)
    {
        //поучаем авторизованого пользователя
        /** @var $currentUser User*/
        $currentUser = $tokenStorage->getToken()->getUser();

        $usersToFollow = [];

        if ($currentUser instanceof User){
            $posts = $this->microPostRepository
                ->findAllByUsers($currentUser->getFollowing());

            $usersToFollow = count($posts) === 0
                ? $userRepository->findAllWithMoreThan5PostsExceptUser($currentUser)
                : [] ;

        } else {
            $posts = $this->microPostRepository->findBy([],['time' => 'DESC']);
        }

        $html = $this->twig->render('micro-post/index.html.twig',[
            'posts' => $posts,
            'usersToFollow' => $usersToFollow
        ]);

        return new Response($html);
    }

    /**
     * @Route("/edit/{id}",name="micro_post_edit")
     * @Security("is_granted('edit',microPost)",message="Access denied")
     */
    public function edit(MicroPost $microPost, Request $request)
    {
        $form = $this->formFactory->create(
            MicroPostType::class,$microPost
        );
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $this->entityManager->flush();

            return new RedirectResponse(
                $this->router->generate('micro_post_index')
            );
        }
        return new Response(
            $this->twig->render('micro-post/add.html.twig',[
                'form' => $form->createView()
            ])
        );
    }

    /**
     * @Route("/delete/{id}",name="micro_post_delete")
     * @Security("is_granted('delete',microPost)",message="Access denied")
     */
    public function delete(MicroPost $microPost)
    {
        $this->entityManager->remove($microPost);
        $this->entityManager->flush();

        $this->flashBag->add('notice','Пост был удален.');

        return new RedirectResponse(
            $this->router->generate('micro_post_index')
        );
    }

    /**
     * @Route("/add", name="micro_post_add")
     * @Security("is_granted('ROLE_USER')")
     */
    public function add(Request $request,TokenStorageInterface $tokenStorage)
    {
        $user = $tokenStorage->getToken()->getUser();

        $microPost = new MicroPost();
//        $microPost->setTime(new \DateTime());
        $microPost->setUser($user);

        $form = $this->formFactory->create(
            MicroPostType::class,$microPost
        );
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $this->entityManager->persist($microPost);
            $this->entityManager->flush();

            return new RedirectResponse(
                $this->router->generate('micro_post_index')
            );
        }

        return new Response(
            $this->twig->render('micro-post/add.html.twig',[
                'form' => $form->createView()
            ])
        );
    }

    /**
     * @Route("/user/{username}",name="micro_post_user")
     */
    public function userPosts(User $userWithPosts)
    {
        $html = $this->twig->render('micro-post/user-posts.html.twig',[
            'posts' => $this->microPostRepository->findBy(
                ['user' => $userWithPosts],
                ['time' => 'DESC']
            ),
            'user' => $userWithPosts
//            'posts' => $userWithPosts->getPosts()
        ]);

        return new Response($html);
    }

    /**
     * @Route("/{id}",name="micro_post_post")
     */
    public function post($id)
    {
        $post = $this->microPostRepository->find($id);

        return new Response(
            $this->twig->render('micro-post/post.html.twig',[
                'post' => $post
            ])
        );
    }
}