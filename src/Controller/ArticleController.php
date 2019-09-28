<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class ArticleController extends AbstractController
{

    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var \Doctrine\Common\Persistence\ObjectRepository */
    private $articleRepository;

    /** @var \Doctrine\Common\Persistence\ObjectRepository */
    private $userRepository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->articleRepository = $entityManager->getRepository('App:Article');
        $this->userRepository = $entityManager->getRepository('App:User');
    }

    /**
     * @Route("/article", name="article")
     */
    public function index()
    {
        return $this->render('article/article.html.twig', [
            'controller_name' => 'IndexController',
        ]);
    }

    /**
     * @Route("/article/{slug}", name="article_show", methods={"GET"})
     */
    public function show($slug, Request $request) : Response
    {
        $article = $this->articleRepository->findOneBy(['articleslug'=>$request->get('slug')]);

        return $this->render('article/show.html.twig', [
            'article'=> $article,
        ]);
    }
}
