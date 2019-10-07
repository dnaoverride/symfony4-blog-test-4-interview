<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleFormType;
use App\Repository\ArticleRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Cocur\Slugify\Slugify;
use Symfony\Component\Security\Core\User\UserInterface;



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
     * @Route("/myarticles", name="my_articles")
     * @Route("/myarticles", defaults={"page": "1"}, methods={"GET"}, name="my_articles")
     * @Route("/myarticles/page/{page<[1-9]\d*>}", methods={"GET"}, name="my_articles_paginated")
     */
    public function myArticles(Request $request, int $page, ArticleRepository $articles): Response
    {
        $user = $this->getUser();
        if ($user != null) {
            $articles = $articles->findUserArticles($page, $user);
        }
        else {
            $this->addFlash('danger','Not logged in!');
            return $this->redirectToRoute('blog_index');
        }

        return $this->render('article/myarticles.html.twig', [
            'paginator' => $articles,
        ]);
    }

    /**
     * @Route("/article/{slug}", name="article_show", methods={"GET"})
     *
     */
    //@Route("/article/{id}", name="article_show", methods={"GET"}), int $id
    public function show(Request $request, ArticleRepository $articleRepository, $slug, int $editCommentId = null ) : Response
    {
        if (defined("id")) {
            dump("NOT ID");die();
        }
        $currentUser = $this->getUser();
        $article = $this->articleRepository->findOneBy(['articleslug'=>$request->get('slug')]);

        if ($currentUser == $article->getUser()){
            $userHaveRightsToEdit = true;
        }
        else
        {
            $userHaveRightsToEdit = false;
        }
//        $A = [1, 1, 1, 2,1,1];
//        $turtle = $this->turtle($A);
//
//        dump($turtle);
        return $this->render('article/show.html.twig', [
            'article' => $article,
            'canedit' => $userHaveRightsToEdit,
            'editcommentid' => $editCommentId,
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
        $form = $this->createForm(ArticleFormType::class,$article);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Article $article */
            $article = $form->getData();
            $slug = $slugify->slugify($article->getTitle());
            $article->setArticleslug($slug);
            $user = $this->getUser();
            $article->setUser($user);
            $article->setImageURL('https://lorempixel.com/800/600/technics/');
            $article->setPublishedAt(new \DateTime());
            $em->persist($article);
            $em->flush();
            $this->addFlash('success', 'Article Created');
            return $this->redirectToRoute('blog_index');
        }
        return $this->render('article/manage/new.html.twig', [
            'article_form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/article/manage/edit/{id}", name="article_edit", methods={"POST"})
     * @IsGranted("ROLE_EDIT_ARTICLE", subject="article")
     */
    public function edit(Article $article, Request $request, EntityManagerInterface $em)
    {

        $form = $this->createForm(ArticleFormType::class, $article);
        $user = $this->getUser();
        if ($user === $article->getUser())
        {

            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $em->persist($article);
                $em->flush();
                $this->addFlash('success', 'Article Updated!');
                return $this->redirectToRoute('my_articles', [
                    'article' => $article,
                    'id' => $article->getId(),
                ]);
            }
        }
        else {
            $this->addFlash('danger','Not allowed to edit someoone\'s article!!!!');

        }
        return $this->render('article/manage/edit.html.twig', [
            'article_form' => $form->createView(),
            'article' => $article,
            'id' => $article->getId(),
        ]);
    }

    /**
     * @Route("/article/manage/delete/{id}", name="article_delete", methods={"POST"})
     * @IsGranted("ROLE_EDIT_ARTICLE")
     *
     */
    public function delete(Article $article, Request $request, EntityManagerInterface $em)
    {

        $user = $this->getUser();
        $existingArticle = $this->articleRepository->findOneBy(['id' => $article->getId()]);
        if ($user === $article->getUser()) {
            $em->remove($article);
            $em->flush();
            $this->addFlash('success','Article deleted.');
        }
        else
        {
            $this->addFlash('danger','Not allowed to delete someone else article!!!');

        }
        return $this->redirectToRoute('my_articles');

    }

    public function turtle(Array $A) {

        for ($i=3;$i<count($A);$i++) {
            if ($A[$i - 1] <= $A[$i - 3] && ($A[$i] >= $A[$i - 2] || $A[$i - 1] >= $A[$i - 3] - ($A[$i - 5] || 0) && $A[$i] >= $A[$i - 2] - $A[$i - 4] && $A[$i - 2] >= $A[$i - 4]))
                return $i;
        }
        return -1;
    }



}
