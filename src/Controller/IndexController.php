<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Faker;

class IndexController extends AbstractController
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var \Doctrine\Common\Persistence\ObjectRepository */
    private $userRepository;

    /** @var \Doctrine\Common\Persistence\ObjectRepository */
    private $articleRepository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->articleRepository = $entityManager->getRepository('App:Article');
        $this->userRepository = $entityManager->getRepository('App:User');
    }

    /**
     * @Route("/aaa", name="indexaaa")
     */
    public function indexaaa()
    {
        return $this->render('index/index.html.twig', [
            'controller_name' => 'IndexController',
        ]);
    }

    /**
     * @Route("/", defaults={"page": "1", "_format"="html"}, methods={"GET"}, name="blog_index")
     * @Route("/page/{page<[1-9]\d*>}", defaults={"_format"="html"}, methods={"GET"}, name="blog_index_paginated")
     */
    public function index(Request $request, int $page, ArticleRepository $articles, CategoryRepository $category): Response
    {
        $category = null;
        if ($request->query->has('category')) {
            $category = $category->findOneBySomeField(['id' => $request->query->get('id')]);
        }
        $latestArticles= $articles->findLatest($page, $category);

        return $this->render('index/index.html.twig', [
            'paginator' => $latestArticles,

        ]);
    }
}
