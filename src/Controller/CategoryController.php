<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryFormType;
use App\Repository\CategoryRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CategoryController extends AbstractController
{
    /** @var EntityManagerInterface */
    private $entityManager;

    private $categoryRepository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->categoryRepository = $entityManager->getRepository('App:Category');
    }

    /**
     * @Route("/category", name="categoies_show")
     * @Route("/category", defaults={"page": "1", "_format"="html"}, methods={"GET"}, name="categoies_show")
     * @Route("/category/page/{page<[1-9]\d*>}", defaults={"_format"="html"}, methods={"GET"}, name="categoies_show_paginated")
     * @IsGranted("ROLE_EDIT_ARTICLE")
     */
    public function categoriesShow(int $page, CategoryRepository $categoryRepository): Response
    {
        $categories = $categoryRepository->findAll();

        return $this->render('category/show.html.twig', [
            'categories' => $categories,
        ]);
    }

    /**
     * @Route("/category/new", name="category_new")
     * @IsGranted("ROLE_EDIT_ARTICLE")
     */
    public function new(EntityManagerInterface $em, Request $request)
    {
        $form = $this->createForm(CategoryFormType::class);
        $category = new Category();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $category = $form->getData();
            $category->setCreatedAt(new \DateTime());
            $em->persist($category);
            $em->flush();
            $this->addFlash('success', 'Category Created');
            return $this->redirectToRoute('categoies_show');
        }
        return $this->render('category/new.html.twig', [
            'category_form' => $form->createView(),
        ]);
    }

    /**
     * @route("/category/edit/{catid}", name="edit_category", methods={"GET","POST"})
     * @IsGranted("ROLE_EDIT_ARTICLE")
     */
    public function categoryEdit(EntityManagerInterface $em, Request $request, int $catid, CategoryRepository $categoryRepository)
    {
        $category = $this->categoryRepository->findOneBy(['id' => $catid]);
        $form = $this->createForm(CategoryFormType::class, $category);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($category);
            $em->flush();
            $this->addFlash('success','Category updated');
            return $this->redirectToRoute('categoies_show');
        }
        return $this->render('category/edit.html.twig', [
            'category_form' => $form->createView(),
        ]);
    }


    /**
    * @route("/category/delete/{catid}", requirements={"id" = "\d+"}, name="delete_category", methods="POST")
    * @IsGranted("ROLE_EDIT_ARTICLE")
    */
    public function removeCategory( $catid ) : Response
    {
        $category = $this->getDoctrine()->getRepository(Category::class)->findOneBy(['id'=> $catid]);
        $manager = $this->getDoctrine()->getManager();
        $manager->remove($category);
        $manager->flush();
        $this->addFlash('success','Category deleted');

        return $this->redirectToRoute('categoies_show');

    }
}