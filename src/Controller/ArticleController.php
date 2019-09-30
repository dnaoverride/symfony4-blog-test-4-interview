<?php

namespace App\Controller;

use App\Entity\Article;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\ArticleFormType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Cocur\Slugify\Slugify;
use Faker;


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

    /**
     * @Route("/article/manage/new", name="article_new")
     * @IsGranted("ROLE_EDIT_ARTICLE")
     */
    public function new(EntityManagerInterface $em, Request $request)
    {
        $slugify = new Slugify();
        $article = new Article();
        $faker = new Faker\Factory();
        $form = $this->createForm(ArticleFormType::class,$article);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Article $article */
            $article = $form->getData();
            $slug = $slugify->slugify($article->getTitle());
            $article->setArticleslug($slug);

            $em->persist($article);
            $em->flush();
            $this->addFlash('success', 'Article Created! Knowledge is power!');
            return $this->redirectToRoute('blog_index');
        }
        return $this->render('article/manage/new.html.twig', [
            'article_form' => $form->createView()
        ]);
    }
}
